<?php

namespace App\Http\Controllers\Api;

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
use Illuminate\Database\Eloquent\Collection;
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

            // if ($current_fight) {
            //     $current_fight->status = 'O';
            //     $current_fight->save();
            // }
            if ($current_fight) {
                $fight_request = new Request(['status' => 'O']);
                $this->fightController->updateFight($fight_request);
            }

            Redis::set('fight', $current_fight->fight_no);
            Redis::set($request->side, 0);
        }

        // Redis::incr($request->side, $request->amount);
        Redis::set($request->side, $request->total);
        $total = Redis::get($request->side);

        // broadcast SecuredBet
        // uuid, side, total, 
        // 
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

        if ($fight->fight_no != $request->fight_no) {
            throw new Exception('Invalid fight', 402);
        }

        // calculate meron
        $meron = Redis::get('M') ?? 0;
        $legit_meron = Bet::where('fight_id', $fight->id)
            ->where('side', 'M')
            ->whereNotIn('user_id', [9])
            ->sum('amount');

        Bet::create([
            'side' => 'M',
            'amount' => $meron - $legit_meron,
            'fight_no' => $request->fight_no,
            'user_id' => 9,
            'fight_id' => $fight->id,
            'status' => 'F'
        ]);

        // calculate meron
        $wala = Redis::get('W') ?? 0;
        $legit_wala = Bet::where('fight_id', $fight->id)
            ->where('side', 'W')
            ->whereNotIn('user_id', [9])
            ->sum('amount');

        Bet::create([
            'side' => 'W',
            'amount' => $wala - $legit_wala,
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

        $update = $fight_controller->updateFight($fight_request);
        return $update;
    }
}
