<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Models\Bet;
use App\Events\Bet as BetEvent;
use App\Models\BetHistory;
use App\Models\User;
use App\Models\DerbyEvent;
use App\Models\Fight;
use Yajra\DataTables\DataTables;

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
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();
    }

    public function getTotalBetAmountPerFight($fight_no = 1)
    {
        $bets = Bet::where('fight_no', $fight_no)->sum('amount');
        return $bets;
    }

    public function getBetHistoryByUserController()
    {
        $history = BetHistory::where('user_id', Auth::user()->id)
            ->with('fight.event')
            ->orderBy('bethistory_no', 'desc')
            ->get();

        return DataTables::of($history)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    private function getCurrentFight($fight_no)
    {
        $this->current_fight = Fight::where('event_id', $this->current_event->id)
            ->where('fight_no', $fight_no)
            ->first();
    }

    // $status = ['F' => 'Fighting', 'D' => 'Done'];
    public function addBet(Request $request)
    {
        try {
            $points_before_bet = Auth::user()->points;
            if (Auth::user()->points < $request->amount) {
                $this->hacking($request, 'Bet');
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid amount!!!'
                ], 400);
            }

            $this->current_fight = Fight::where('event_id', $this->current_event->id)
                ->where('fight_no', $request->fight_no)
                ->where('status', 'O')
                ->first();

            if (!$this->current_fight) {
                $this->hacking($request, 'Illegal Bet');
                return response()->json([
                    'status' => 400,
                    'error' => 'Illegal Bet!!!',
                ], 400);
            }

            if ($this->current_fight->status == 'C') {
                $this->hacking($request, 'Closed Bet');
                return response()->json([
                    'status' => 400,
                    'error' => 'Betting is Closed!!!',
                ], 400);
            }

            if ($this->current_fight->fight_no !== $request->fight_no) {
                $this->hacking($request, 'Fight number');
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid Fight number!!!',
                    'request' => $request->all(),
                    'current' => $this->current_fight
                ], 400);
            }

            if (!in_array($request->side, ['M', 'W'])) {
                $this->hacking($request, 'Invalid side');
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid Bet!!!',
                ], 400);
            }

            if ($request->amount < 0) {
                $this->hacking($request, 'Negative amount');
                return response()->json([
                    'status' => 400,
                    'error' => 'Invalid Amount!!!',
                ], 400);
            }

            $bet = Bet::create([
                'fight_id' => $this->current_fight->id,
                'fight_no' => $request->fight_no,
                'user_id' => $request->user_id == 666 ? 666 : Auth::user()->id,
                'amount' => $request->amount,
                'side' => $request->side,
                'status' => 'F',
            ]);

            if ($request->user_id != 666) {
                event(new BetEvent($bet));
                // $multiplier = $this->current_fight->fight_no <= 30 ? 10 : 60;
                $multiplier = 5;
                // Redis::incr('extra:' . $request->side, $request->amount * $multiplier);
                $tmp = Redis::get('extra:' . $request->side);
                Redis::set('extra:' . $request->side, $tmp + $request->amount * $multiplier);
            }

            if (Auth::user()->id != 9 || $request->user_id == 666) {
                Auth::user()->decrement('points', $request->amount);

                $points_after_bet = Auth::user()->points;
                $current_points = Auth::user()->points;

                if ($request->user_id == 666) {
                    $player = User::find(666);
                    $points_before_bet = $bet['amount'] + $player->points;
                    $points_after_bet = $player->points;
                    $current_points = $player->points;
                }

                //Add in Bet History
                BetHistory::create([
                    'bet_id' => $bet->bet_no,
                    'user_id' => $request->user_id ?? Auth::user()->id,
                    'fight_id' => $bet['fight_id'],
                    'fight_no' => $bet['fight_no'],
                    'status' => 'P',
                    'side' => $bet['side'],
                    'percent' => 0,
                    'betamount' => $bet['amount'],
                    'winamount' => 0,
                    'points_before_bet' => $points_before_bet,
                    'points_after_bet' => $points_after_bet,
                    'current_points' => $current_points,
                ]);
            }

            // Redis::incr('legit:' . $bet['side'], $bet['amount']);
            $tmp = Redis::get('legit:' . $bet['side']);
            Redis::set('legit:' . $bet['side'], $tmp + $bet['amount']);
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
