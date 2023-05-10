<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Bet;
use App\Events\Bet as BetEvent;
use App\Models\BetHistory;
use App\Models\User;
use App\Models\DerbyEvent;
use App\Models\Fight;

class BetController extends Controller
{
    private $current_event;
    private $current_fight;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->current_event = DerbyEvent::where('status','ACTIVE')->first();
    }

    public function getTotalBetAmountPerFight($fight_no=1)
    {
        $bets = Bet::where('fight_no',$fight_no)->sum('amount');
        return $bets;
    }

    public function getBetHistoryByUserController()
    {
        $history = BetHistory::where('user_id', Auth::user()->id)
            ->with('fight.event')
            ->get();

        return response()->json([
              'data' => $history,
        ]);
    }

    private function getCurrentFight($fight_no)
    {
        $this->current_fight = Fight::where('event_id',$this->current_event->id)
            ->where('fight_no', $fight_no)
            ->first();
    }

    // $status = ['F' => 'Fighting', 'D' => 'Done'];
    public function addBet(Request $request)
    {
        try {
            if(Auth::user()->points < $request->amount) {
                $this->hacking($request, 'Bet');
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid amount!!!'
                ], 400);
            }

            $this->current_fight = Fight::where('event_id', $this->current_event->id)
                ->where('fight_no', $request->fight_no)
                ->first();

            $bet = Bet::create([
                'fight_id' => $this->current_fight->id,
                'fight_no' => $request->fight_no,
                'user_id' => Auth::user()->id,
                'amount' => $request->amount,
                'side' => $request->side,
                'status' => 'F'
            ]);

            event(new BetEvent($bet));
            Auth::user()->decrement('points', $request->amount);

        //Add in Bet History
        $betHistory = BetHistory::create([
            'user_id' => Auth::user()->id,
            'fight_no' => $bet['fight_no'],
            'status' => 'P',
            'side' => $bet['side'],
            'percent' => 0,
            'betamount' => $bet['amount'],
            'winamount' => 0
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
        
        return response()->json([
            'status' => 'OK',
            'data' => $bet
        ]);

    }
}
