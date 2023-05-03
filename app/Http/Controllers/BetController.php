<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Bet;
use App\Models\BetHistory;
use App\Models\User;

class BetController extends Controller
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

            $bet = Bet::create([
                'fight_no' => $request->fight_no,
                'user_id' => Auth::user()->id,
                'amount' => $request->amount,
                'side' => $request->side,
                'status' => 'F'
            ]);

            Auth::user()->decrement('points', $request->amount);

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
