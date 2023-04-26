<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Fight;

class FightController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getCurrentFight()
    {
        $current = Fight::whereNotNull('fight_no')
            ->whereNull('game_winner')
            ->orderBy('fight_no','desc')
            ->first();

        if(!$current) {
            $current = Fight::create([
                'user_id' => Auth::user()->id
            ]);
        }

        return response()->json([
            'data' => $current
        ]);
    }

    public function updateFight(Request $request)
    {
        $fight = Fight::whereNotNull('fight_no')
            ->whereNull('game_winner')
            ->orderBy('fight_no','desc')
            ->first();

        if($request->status == 'D') {
            return $this->fightDone($fight);
        }

        $update_fight = $fight->update([
            'status' => $request->status
        ]);

        return $update_fight;
    }

    private function fightDone($last_fight, $winner='M')
    {
        $last_fight->update([
            'status' => 'D',
            'game_winner' => $winner
        ]);

        $new_fight = Fight::create([
            'user_id' => Auth::user()->id,
        ]);

        return response()->json([
            'data' => $new_fight
        ]);
    }
}
