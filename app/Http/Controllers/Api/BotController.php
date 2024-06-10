<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BetController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FightController;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class BotController extends Controller
{
    public function __construct(
        FightController $fightController,
        BetController $betController
    ) {
        $this->middleware('auth:sanctum');
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
