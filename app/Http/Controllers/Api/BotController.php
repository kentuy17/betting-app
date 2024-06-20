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
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Str;

class BotController extends Controller
{
    public function __construct(
        FightController $fightController,
        BetController $betController
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
    }

    public function addBet(Request $request)
    {
        $bet_request = new Request([
            'fight_no' => (int)$request->fight_no,
            'amount' => (int)$request->amount,
            'side' => $request->side
        ]);

        $add_bet = $this->betController->addBet($bet_request);
        return $add_bet;
    }

    public function addBetCached(Request $request)
    {
        $fight = Redis::get('fight');

        if (!$fight || $fight != $request->fight_no) {
            $event = DerbyEvent::where('status', 'ACTIVE')->orderBy('id', 'DESC')->first();
            $current_fight = Fight::where('event_id', $event->id)
                ->where('fight_no', $request->fight_no)
                ->orderBy('id', 'DESC')
                ->first();

            if ($current_fight) {
                // Open fight
                $fight_request = new Request(['status' => 'O']);
                $fight_controller = new \App\Http\Controllers\FightController;

                sleep(5);
                $fight_controller->updateFight($fight_request);
                sleep(2);
            }

            Redis::set('fight', $current_fight->fight_no);
            // Redis::set($request->side, 0);
            $this->cleanup();
        }

        // Redis::incr($request->side, $request->amount);
        // switch
        // $switch = $request->side == 'M' ? 'W' : 'M';
        $switch = $request->side;

        // Redis::set($request->side, $request->total);
        // $extra = Redis::get('extra:' . $request->side);
        // $total = Redis::get($request->side) + $extra;

        Redis::set($switch, $request->total);
        $extra = Redis::get('extra:' . $switch);
        $total = Redis::get($switch) + $extra;

        // broadcast SecuredBet
        // uuid, side, total,
        //
        $securedBet = collect([
            'uuid' => Str::uuid(),
            // 'side' => $request->side,
            'side' => $switch,
            'total' => $total
        ]);

        // event(new SecuredBet($securedBet));
        if ($securedBet) {
            event(new SecuredBet($securedBet));
        }

        return $securedBet;
    }

    public function addExtraBet(Request $request)
    {
        Redis::incr('extra:' . $request->side, $request->amount);
        $extra = Redis::get('extra:' . $request->side);
        $total = $extra + Redis::get($request->side);

        $securedBet = collect([
            'uuid' => Str::uuid(),
            'side' => $request->side,
            'total' => $total
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
            throw new Exception('Fight already closed',  402);
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
            'amount' => ($meron + $extra_m) - $legit_meron,
            'fight_no' => $request->fight_no,
            'user_id' => 9,
            'fight_id' => $fight->id,
            'status' => 'F'
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
            'amount' => ($wala - $legit_wala) + $extra_w,
            'fight_no' => $request->fight_no,
            'user_id' => 9,
            'fight_id' => $fight->id,
            'status' => 'F'
        ]);

        // close the fight
        $fight_request = new Request([
            'status' => $request->status,
            'result' => $request->result
        ]);

        $update = $this->fightController->updateFight($fight_request);
        return $update;
    }

    public function issueToken($id = null)
    {
        $user = $id ? User::find($id) : Auth::user();
        $token = $user->createToken('operator');
        return response()->json([
            'token' => $token->plainTextToken
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
            'result' => $request->result
        ]);

        // padaog
        sleep(10);
        $update = $this->fightController->updateFight($fight_request);
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
            'result' => 'CANCEL'
        ]);

        $update = $this->fightController->updateFight($cancel_request);
        return $update;
    }
}
