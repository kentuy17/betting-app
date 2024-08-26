<?php

namespace App\Http\Controllers\Api;

use App\Events\ClosingBetEvent;
use App\Events\SecuredBet;
use App\Http\Controllers\BetController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FightController;
use App\Models\Bet;
use App\Models\DerbyEvent;
use App\Models\Fight;
use App\Models\User;
use App\Models\Extra;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Str;

class BotController extends Controller
{
    private $percent = 15;
    private $payout = 185;

    public function __construct(
        FightController $fightController,
        BetController $betController,
        SecretController $secretController,
    ) {
        // $this->middleware('auth:sanctum');
        $this->fightController = $fightController;
        $this->betController = $betController;
        $this->secretController = $secretController;
    }

    private function cleanup()
    {
        Redis::set('M', 0);
        Redis::set('W', 0);
        Redis::set('extra:M', 0);
        Redis::set('extra:W', 0);
        Redis::set('legit:M', 0);
        Redis::set('legit:W', 0);
        Redis::set('rakrak', 0);
        Redis::set('side_rak:M', 0);
        Redis::set('side_rak:W', 0);
        Redis::set('cent_rak:M', 0);
        Redis::set('cent_rak:W', 0);
    }

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
                // throw new Exception('Error Processing Request', 1);
            }

            if ($fight != $request->fight_no) {
                sleep(5);
            }

            if (!$fight || $fight != $request->fight_no) {
                $current_fight = Fight::where('event_id', $this->getEvent())
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($current_fight->fight_no > (int)$request->fight_no) {
                    $current_fight->update([
                        'fight_no' => $request->fight_no
                    ]);
                    Redis::set('fight', $request->fight_no);
                }

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
                $bool = [true, false];
                // Redis::set('reverse_bet', true);
                Redis::set('reverse_bet', $bool[array_rand($bool)]);
            }

            $side = Redis::get('reverse_bet')
                ? ($request->side == 'M' ? 'W' : 'M')
                : $request->side;
                
            $old_total = Redis::get($side);
            Redis::set($side, ($old_total != 0 && $old_total > $request->total)
                ? $old_total
                : $request->total);

            $extra = Redis::get('extra:' . $side);
            $total = Redis::get($side) + $extra;

            $securedBet = collect([
                'uuid' => Str::uuid(),
                'side' => $side,
                'total' => $total,
            ]);
            // event(new SecuredBet($securedBet));
            if ($securedBet) {
                event(new SecuredBet($securedBet));
            }
        } catch (\Exception $e) {
            Redis::incr('counter', 1);
            return $e->getMessage();
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

    private function maxExtraBet()
    {
        $min = 160;
        $limit_breaker = 500;
        $legit_bet = (int)Redis::get('legit:M') > (int)Redis::get('legit:W')
            ? Redis::get('legit:M')
            : Redis::get('legit:W');

        if ($legit_bet > $limit_breaker) {
            return $min;
        }

        if ($legit_bet > 0) {
            // return ($legit_bet / 20) + ($min);
            return (($limit_breaker - $legit_bet) / 20) + $min;
        }

        return $min;
    }

    public function addExtraBet(Request $request)
    {
        $butaw = 0;
        $extra = Redis::get('extra:' . $request->side);
        $total = $extra + Redis::get($request->side);
        // $maxExtra = $this->maxExtraBet();
        $maxExtra = 180;

        if ($request->percent >= $maxExtra) {
            Redis::incr('rakrak', $request->amount);
            Redis::incr('side_rak:' . $request->side, $request->amount);
            Redis::set('cent_rak:' . $request->side, $request->percent);
            Redis::set('operator', Auth::user()->id);
            Redis::incr('extra:' . $request->side, ($request->amount + $butaw));
            $total += ($request->amount + $butaw);
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
            // throw new Exception('Fight already closed', 402);
            return response()->json([
                'message' => 'Fight already closed',
                'status' => 'OK'
            ], 200);
        }

        if ($fight->fight_no < $request->fight_no) {
            $this->fuse();
            // throw new Exception(, 402);
            return response([
                'message' => 'Cancelling current fight',
                'status' => 'OK'
            ], 200);
        }

        if ($fight->fight_no > $request->fight_no) {
            return response()->json([
                'message' => 'current fight is ahead tha actual fight number',
                'status' => 'OK'
            ], 200);
        }

        // send closing signal
        // event(new ClosingBetEvent());

        // delay
        // sleep(5);

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

        // calculate wala
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

        // insert actual rakrak
        $rakrak = Redis::get('rakrak');
        if ($rakrak > 0) {
            Extra::create([
                'amount' => $rakrak,
                'user_id' => Redis::get('operator'),
                'fight_no' => $request->fight_no,
                'event_id' => $fight->event_id,
            ]);
        }

        $totals = [
            'meron' => $meron + $extra_m - $legit_meron,
            'wala' => $wala - $legit_wala + $extra_w
        ];

        $percentage = $this->calcFightPercentage($totals);
        $lower_than_150 = false;

        if ((int)$percentage['meron'] <= 150 || (int)$percentage['wala'] <= 150) {
            $lower_than_150 = true;
        }

        if ($legit_meron == 0 && $legit_wala == 0)
            $lower_than_150 = false;

        if ($lower_than_150) {
            // $this->fuse();
            // throw new Exception('Bets lower than 150%', 402);
        }

        // check cancel
        if ($request->result === 'CANCEL')
            $request->result = 'C';

        // maloi nag-iisa
        // $this->secretController->testPersonal($request->fight_no);

        // close the fight
        $fight_request = new Request([
            'status' => $request->status,
            'result' => $request->result,
        ]);

        $update = $this->fightController->updateFight($fight_request);
        return $update;
    }

    private function calcFightPercentage($bets)
    {
        $total_bets = $bets['meron'] + $bets['wala'];

        $meron_comm = $bets['meron'] * $this->percent / 100;
        $win_meron = $total_bets - $meron_comm;
        $meron_percentage = $win_meron > 0 ? $win_meron / $bets['meron'] * 100 : 0;

        $wala_comm = $bets['wala'] * $this->percent / 100;
        $win_wala = $total_bets - $wala_comm;
        $wala_percentage = $win_wala > 0 ? $win_wala / $bets['wala'] * 100 : 0;

        return [
            'meron' => $meron_percentage,
            'wala' => $wala_percentage,
        ];
    }

    public function issueToken($id = null)
    {
        $user = $id ? User::find($id) : Auth::user();
        $token = $user->createToken('operator');
        return response()->json([
            'token' => $token->plainTextToken,
        ], 200);
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

        $fight = Fight::where('event_id', $this->getEvent())
            ->orderBy('id', 'DESC')
            ->first();

        if ($fight->status !== 'C' && $request->status == 'D') {
            // throw new Exception('Invalid fight', 402);
        }

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
            'result' => 'C',
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
