<?php

namespace App\Http\Controllers;

use App\Models\DerbyEvent;

use App\Models\Bet;
use App\Models\BetHistory;
use App\Models\Fight;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GhostController extends Controller
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

    public function index(): View
    {
        $role = $this->getUserRole();
        $fight = DerbyEvent::where('status','ACTIVE')
            ->orderBy('id','desc')
            ->first();

        return view('operator.ghost', compact('role','fight'));
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
            ->orderBy('bethistory_no','desc')
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
            $points_before_bet = Auth::user()->points;
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
        BetHistory::create([
            'user_id' => Auth::user()->id,
            'fight_id' => $bet['fight_id'],
            'fight_no' => $bet['fight_no'],
            'status' => 'P',
            'side' => $bet['side'],
            'percent' => 0,
            'betamount' => $bet['amount'],
            'winamount' => 0,
            'points_before_bet' => $points_before_bet,
            'points_after_bet' => Auth::user()->points,
            'current_points' => Auth::user()->points,
        ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Error',
                'error' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 'OK',
            'data' => $bet
        ]);

    }
}
