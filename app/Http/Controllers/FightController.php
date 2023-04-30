<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Fight;
use App\Events\Fight as FightEvent;
use App\Models\DerbyEvent;

class FightController extends Controller
{
    public $current_event;
    public $prev_match;
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
        // $current = Fight::whereNotNull('fight_no')
        //     ->whereNull('game_winner')
        //     ->orderBy('fight_no','desc')
        //     ->first();

        // if(!$current) {
        //     $current = Fight::create([
        //         'user_id' => Auth::user()->id
        //     ]);
        // }
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        $this->prev_match = Fight::where('event_id', $this->current_event)
            ->whereNotNull(['game_winner', 'status'])
            ->orderBy('id','desc')
            ->first();

        $current = Fight::where('event_id', $this->current_event->id)
            ->whereNull('game_winner')
            ->orderBy('id', 'desc')
            ->first();
        
        return response()->json([
            'current' => $current,
            'points' => Auth::user()->points,
            'event' => $this->current_event,
        ]);
    }

    private function currenctMatch()
    {
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        return Fight::where('event_id', $this->current_event->id)
            ->where('game_winner',null)
            ->orderBy('id', 'desc')
            ->first();
    }

    public function updateFight(Request $request)
    {
        // $fight = Fight::whereNotNull('fight_no')
        //     ->whereNull('game_winner')
        //     ->orderBy('fight_no','desc')
        //     ->first();

        $fight = $this->currenctMatch();

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
