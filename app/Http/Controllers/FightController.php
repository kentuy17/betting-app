<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Fight;
use App\Events\Fight as FightEvent;

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
            return $this->fightDone($fight, $request->result);
        }

        $updated = $fight->update(['status' => $request->status]);
        if($updated) {
            event(new FightEvent($fight));
        }
        
        return $fight;
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

        event(new FightEvent($new_fight));

        return response()->json([
            'data' => $new_fight
        ]);
    }
}
