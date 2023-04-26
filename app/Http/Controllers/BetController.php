<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Bet;
use App\Models\BetHistory;


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

    public function getBetHistoryByUser()
    {
        //$sUser = User::where('user_id',session('user_id'))->first();
        $sUser = Auth::user()->id;
        $history = BetHistory::where('user_id', $sUser)->get();

        return response()->json([
              'data' => $history,
        ]);
    }
}
