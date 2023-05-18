<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Fight;
use App\Events\Fight as FightEvent;
use App\Events\Result;
use App\Models\DerbyEvent;
use App\Models\Bet;
use App\Models\User;
use App\Models\BetHistory;
use Illuminate\Support\Facades\DB;

class FightController extends Controller
{
    public $current_event;
    public $prev_match;
    public $fight;
    private $percent = 5;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getTotalBets()
    {
        $meron = Bet::where(['fight_id' => $this->fight->id, 'side' => 'M'])
            ->sum('amount');

        $wala = Bet::where(['fight_id' => $this->fight->id, 'side' => 'W'])
            ->sum('amount');

        return [
            'meron' => $meron,
            'wala' => $wala,
        ];
    }

    private function getTotalPlayerBet()
    {
        $bet_meron = Bet::where([
                'fight_id' => $this->fight->id,
                'side' => 'M',
                'user_id' => Auth::user()->id,
            ])->sum('amount');

        $bet_wala = Bet::where([
            'fight_id' => $this->fight->id,
            'side' => 'W',
            'user_id' => Auth::user()->id,
        ])->sum('amount');

        return [
            'meron' => $bet_meron,
            'wala' => $bet_wala,
        ];
    }

    public function getCurrentFight()
    {
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        $this->prev_match = Fight::where('event_id', $this->current_event)
            ->whereNotNull(['game_winner', 'status'])
            ->orderBy('id','desc')
            ->first();

        $this->fight = Fight::where('event_id', $this->current_event->id)
            ->whereNull('game_winner')
            ->orderBy('id', 'desc')
            ->first();

        
        return response()->json([
            'current' => $this->fight,
            'points' => Auth::user()->points,
            'event' => $this->current_event,
            'bets' => $this->getTotalBets(),
            'player' => $this->getTotalPlayerBet(),
            'id' => Auth::user()->id,
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

    private function calcFightPercentage()
    {
        $bets = $this->getTotalBets();
        $total_bets = $bets['meron'] + $bets['wala'];
        $commision = $this->percent * $total_bets / 100;
        $win = $total_bets - $commision;

        return [
            'meron' => $bets['meron'] > 0 ? $win / $bets['meron'] * 100 : 0,
            'wala' => $bets['wala'] > 0 ? $win / $bets['wala'] * 100 : 0,
        ];
    }

    public function updateFight(Request $request)
    {
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

    private function fightDone($last_fight, $winner)
    {
        $last_fight->update([
            'status' => 'D',
            'game_winner' => $winner,
        ]);

        Bet::where('fight_id',$last_fight->id)
            ->update(['status' => 'D']);

        $new_fight = Fight::create([
            'user_id' => Auth::user()->id,
            'fight_no' => $last_fight->fight_no + 1,
            'event_id' => $this->current_event->id,
        ]);

        $fight = [
            'prev' => $last_fight,
            'curr' => $new_fight,
        ];

        event(new FightEvent($fight));

        if($winner == 'C' || $winner == 'D'){
            $this->fight = $last_fight;
            $calc = $this->calcFightPercentage();
            
            $user_bets = Bet::where([
                'fight_id' => $last_fight->id
            ])->get();

            $percentage = 0;
            foreach($user_bets as $bet) {
                $update = Bet::find($bet->bet_no);
                $update->win_amount = $bet->amount;
                $update->save();

                $user = User::find($bet->user_id);
                $user->points += $bet->amount;
                $user->save();

                if($winner == 'D')
                {
                    $percentage = $bet->side == 'M' ? $calc['meron'] : $calc['wala'];
                }
                
                $betHist = BetHistory::where('fight_no',$bet->fight_no)
                ->where('user_id', $bet->user_id)
                ->where('status','P')
                ->where('side',$bet->side)
                ->where('betamount',$bet->amount)
                ->get();

                foreach($betHist as $history) {
                     $hist = BetHistory::find($history->bethistory_no);
                     $hist->percent = $percentage;
                     $hist->winamount = $bet->amount;
                     $hist->status = $winner;
                     $hist->save();
                }

                event(new Result($update));
            }
        }
        if($winner == 'M' || $winner == 'W') 
        {
            $this->fight = $last_fight;
            $calc = $this->calcFightPercentage();
            $percentage = $winner == 'M' ? $calc['meron'] : $calc['wala'];
            
            $user_bets = Bet::where([
                    'side' => $winner, 
                    'fight_id' => $last_fight->id
                ])->get();

            foreach($user_bets as $bet) 
            {
                $update = Bet::find($bet->bet_no);
                $update->win_amount = $bet->amount * $percentage / 100;
                $update->save();

                $user = User::find($bet->user_id);
                $user->points += $bet->amount * $percentage / 100;
                $user->save();

                $betHist = BetHistory::where('fight_no',$bet->fight_no)
                ->where('user_id', $bet->user_id)
                ->where('status','P')
                ->where('side',$bet->side)
                ->where('betamount',$bet->amount)
                ->get();

                foreach($betHist as $history) {
                    $hist = BetHistory::find($history->bethistory_no);
                    $hist->percent = $percentage;
                    $hist->winamount = $update->win_amount;
                    $hist->status = 'W';
                    $hist->save();
                }

                event(new Result($update));                
            }

            #region updatelose history
            $lose = $winner == 'M'?'W':'M';
            $percentagelb = $lose == 'M' ? $calc['meron'] : $calc['wala'];
            $lose_bet = Bet::where([
                       'side' => $lose, 
                        'fight_id' => $last_fight->id
                    ])->get();
            
            foreach($lose_bet as $lb) 
            {
                $betHistLB = BetHistory::where('fight_no',$lb->fight_no)
                            ->where('user_id', $lb->user_id)
                            ->where('status','P')
                            ->where('side',$lb->side)
                            ->where('betamount',$lb->amount)
                            ->get();
            
                foreach($betHistLB as $historyLB) {
                    $histLB = BetHistory::find($historyLB->bethistory_no);
                    $histLB->percent = $percentagelb;
                    $histLB->status = 'L';
                    $histLB->save();
                }
            }
            #endregion

        }

        return response()->json([
            'data' => $new_fight
        ]);
    }

    private function setWinner($winner)
    {
        switch ($winner) {
            case 'M':
                return 'Meron Wins';
                break;

            case 'W':
                return 'Wala Wins';
                break;

            case 'D':
                return 'Draw';
                break;

            default:
                return 'Cancelled';
                break;
        }
    }

    public function fightResults()
    {
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();
        $fights = Fight::where('event_id', $this->current_event->id)
            ->get();

        $data = [];
        foreach ($fights as $key => $fight) {
            $data[] = [
                0 => $this->setWinner($fight->game_winner),
                1 => $fight->fight_no,
            ];
        }

        return $data;
    }
}
