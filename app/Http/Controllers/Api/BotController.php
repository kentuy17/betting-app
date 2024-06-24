<?php

namespace App\Http\Controllers\Api;

use App\Events\ClosingBetEvent;
use App\Events\SecuredBet;
use App\Http\Controllers\BetController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FightController;
use App\Models\Bet;
use App\Models\DerbyEvent;
use App\Models\BetHistory;
use App\Models\Fight;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Str;

class BotController extends Controller 
{
    public function __construct(
        FightController $fightController,
        BetController $betController,
    ) {
        // $this->middleware('auth:sanctum');
        $this->fightController = $fightController;
        $this->betController = $betController;
    }

    private function cleanup() 
    {
        Redis::set('M', 0);
        Redis::set('W', 0);
        Redis::set('extra:M', 0);
        Redis::set('extra:W', 0);
        Redis::set('legit:M', 0);
        Redis::set('legit:W', 0);
    }

    /**private function testPersonal() 
    {
        $meron = Redis::get('M') + Redis::get('extra:M');
        $wala = Redis::get('W') + Redis::get('extra:W');
        $secret_fan = User::find(666);
        
        if ($meron > 0 && $wala > 0) {
            $maloi_nag_iisa = $meron > $wala ? 'M' : 'W';

            $kiyod = BetHistory::where('user_id', 666)
                ->orderBy('bethistory_no', 'desc')
                ->first();

            
            if($kiyod->status === 'L')
                $pusta = $kiyod->betamount *2.5;
            if($kiyod->status === 'W') {
                $pusta = ($secret_fan->points/16) >= 10
                    ? ($secret_fan->points/16)
                    : $secret_fan->points;
            }
            if(in_array($kiyod->status, ['C','D'])){
            }  $pusta = $kiyod->betamount;
            }
                
            $log = [
                'idol' => $maloi_nag_iisa,
                // 'kiyod' => $kiyod,
                'pusta' => $pusta
            ];

            \Log::channel('custom')->info(json_encode($log));
        }
    }**/

    public function addBet(Request $request) 
    {
        $bet_request = new Request([
            'fight_no' => (int) $request->fight_no,
            'amount' => (int) $request->amount,
            'side' => $request->side,
        ]);

        $add_bet = $this->betController->addBet($bet_request);
        return $add_bet;
    }

    private function getEvent() 
    {
        $event = Redis::get('event');

        if (!$event) {
            $db_event = DerbyEvent::where('status', 'ACTIVE')
                ->orderBy('id', 'DESC')
                ->first();
            $event = $db_event->id;
            Redis::set('event', $event);
        }
        return $event;
    }

    public function addBetCached(Request $request) 
    {
        try {
            $fight = Redis::get('fight');
            $kill_witch = Redis::get('counter');

            if ($kill_witch >= 20) {
                throw new Exception('Error Processing Request', 1);
            }

            if ($fight != $request->fight_no) {
                sleep(5);
            }

            if (!$fight || $fight != $request->fight_no) {
                $current_fight = Fight::where('event_id', $this->getEvent())
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($current_fight->fight_no < $request->fight_no) {
                    Redis::set('fight', $current_fight->fight_no);
                    $cancel_fight = $this->fuse();
                    return $cancel_fight;
                }

                if ($current_fight->status == 'C') {
                    throw new Exception('Cannot bet');
                }

                $this->cleanup();

                // Open fight
                $fight_request = new Request(['status' => 'O']);
                $fight_controller = new \App\Http\Controllers\FightController();

                $fight_controller->updateFight($fight_request);
                sleep(2);
                Redis::set('fight', $current_fight->fight_no);
            }

            Redis::set($request->side, $request->total);
            $extra = Redis::get('extra:' . $request->side);
            $total = Redis::get($request->side) + $extra;

            $securedBet = collect([
                'uuid' => Str::uuid(),
                'side' => $request->side,
                'total' => $total,
            ]);

            // event(new SecuredBet($securedBet));
            if ($securedBet) {
                event(new SecuredBet($securedBet));
            }
        } catch (\Throwable $th) {
            Redis::incr('counter', 1);
            throw $th;
        }

        return $securedBet;
    }

    private function allowManual($side) 
    {
        $abay = Redis::get($side) + Redis::get('extra:' . $side);
        $pikas =
            Redis::get($side == 'M' ? 'W' : 'M') +
            Redis::get('extra:' . ($side == 'M') ? 'W' : 'M');
        return $pikas / $abay > 0.9;
    }

    public function addExtraBet(Request $request)
    {
        // Redis::incr('extra:' . $request->side, $request->amount);
        $extra = Redis::get('extra:' . $request->side);
        $total = $extra + Redis::get($request->side);

        if ($request->percent >= 175) {
            Redis::incr('extra:' . $request->side, $request->amount);
            $total += $request->amount;
        }

        $securedBet = collect([
            'uuid' => Str::uuid(),
            'side' => $request->side,
            'total' => $total,
        ]);

        if ($securedBet) {
            event(new SecuredBet($securedBet));
        }

        return $securedBet;
    }

    public function closeFight(Request $request) 
    {
        $fight = Fight::orderBy('id', 'DESC')->first();

        if ($fight->status == 'C' && $fight->fight_no == $request->fight_no) {
            throw new Exception('Fight already closed', 402);
        }

        if ($fight->fight_no != $request->fight_no) {
            throw new Exception('Invalid fight', 402);
        }

        // send closing signal
        // event(new ClosingBetEvent());

        // delay
        sleep(5);

        // calculate meron
        $meron = Redis::get('M') ?? 0;
        $extra_m = Redis::get('extra:M') ?? 0;
        $legit_meron = Bet::where('fight_id', $fight->id)
            ->where('side', 'M')
            ->whereNotIn('user_id', [9])
            ->sum('amount');

        Bet::create([
            'side' => 'M',
            'amount' => $meron + $extra_m - $legit_meron,
            'fight_no' => $request->fight_no,
            'user_id' => 9,
            'fight_id' => $fight->id,
            'status' => 'F',
        ]);

        // calculate meron
        $wala = Redis::get('W') ?? 0;
        $extra_w = Redis::get('extra:W') ?? 0;
        $legit_wala = Bet::where('fight_id', $fight->id)
            ->where('side', 'W')
            ->whereNotIn('user_id', [9])
            ->sum('amount');

        Bet::create([
            'side' => 'W',
            'amount' => $wala - $legit_wala + $extra_w,
            'fight_no' => $request->fight_no,
            'user_id' => 9,
            'fight_id' => $fight->id,
            'status' => 'F',
        ]);
        
        // maloi nag-iisa
        // $this->testPersonal();

        // close the fight
        $fight_request = new Request([
            'status' => $request->status,
            'result' => $request->result,
        ]);

        $update = $this->fightController->updateFight($fight_request);
        return $update;
    }

    public function issueToken($id = null) 
    {
        $user = $id ? User::find($id) : Auth::user();
        $token = $user->createToken('operator');
        return response()->json(
            [
                'token' => $token->plainTextToken,
            ],
            200,
        );
    }

    public function getUserTokens(Request $request) 
    {
        return $request->user()->tokens;
    }

    public function updateFight(Request $request) 
    {
        $fight_request = new Request([
            'status' => $request->status,
            'result' => $request->result,
        ]);

        // padaog
        sleep(15);
        $update = $this->fightController->updateFight($fight_request);
        Redis::set('counter', 0);
        return $update;
    }

    public function updateDelay(Request $request) 
    {
        Redis::set('delay', $request->delay);
        return response()->noContent();
    }

    public function fuse() 
    {
        $cancel_request = new Request([
            'status' => 'D',
            'result' => 'CANCEL',
        ]);

        $update = $this->fightController->updateFight($cancel_request);
        return $update;
    }

    public function cancelAlert($fight_no = null) 
    {
        try {
            $alert_enabled = Redis::get('alert:cancel');

            if (!$alert_enabled) {
                return response()->noContent();
            }

            $curl = curl_init();
            $fight = 'Fight::#' . $fight_no;

            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://192.46.230.32:8080/message?token=ASk47t8pVjV3fHo',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>
                    '{
                    "message": "' .
                    $fight .
                    '",
                    "priority": 10,
                    "title": "CANCEL FIGHT"
                }',
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);
            // echo $response;

            Redis::set('alert:cancel', false);
        } catch (\Throwable $th) {
            \Log::info('CF: ' . json_encode($th->getMessage()));
        }

        \Log::info('CF: ' . json_encode($response));
        return response()->noContent();
    }
}
