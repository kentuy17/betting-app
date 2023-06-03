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
use App\Models\Commission;
use App\Models\ShareHolder;
use Illuminate\Support\Facades\DB;

class FightController extends Controller
{
    public $current_event;
    public $prev_match;
    public $fight;
    private $percent = 10;
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

        $meron_comm = $bets['meron'] * $this->percent / 100;
        $win_meron = $total_bets - $meron_comm;
        $meron_percentage = $win_meron / $bets['meron'] * 100;

        $wala_comm = $bets['wala'] * $this->percent / 100;
        $win_wala = $total_bets - $wala_comm;
        $wala_percentage = $win_wala / $bets['meron'] * 100;

        return [
            // 'meron' => $bets['meron'] > 0 ? $win / $bets['meron'] * 100 : 0,
            // 'wala' => $bets['wala'] > 0 ? $win / $bets['wala'] * 100 : 0,
            'meron' => $meron_percentage,
            'wala' => $wala_percentage,
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
                $update->status = 'X';
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

        if($last_fight->game_winner == 'M' || $last_fight->game_winner == 'W') {
            $kusgan = ShareHolder::get();
            $data = [];
            $per = 10;
            $total_win_amount = Bet::where('fight_id',$last_fight->id)->sum('win_amount');

            if($kusgan->sum('percentage') < $per) {
                $unallocated = array(
                    'id' => '-1',
                    'user_id' => '-1',
                    'percentage' => $per - $kusgan->sum('percentage')
                );

                $kusgan->push($unallocated);
                $kusgan->all();
            }

            foreach ($kusgan as $key => $gwapo) {
                if(gettype($gwapo) == 'array') {
                    $gwapo = (object) $gwapo;
                }
                $data[] = [
                    'user_id' => $gwapo->user_id,
                    'points' => $percentage / $per * $gwapo->percentage,
                    'percentage' => $gwapo->percentage,
                    'total_win_amount' => $total_win_amount,
                    'fight_id' => $last_fight->id,
                    'event_id' => $this->current_event->id,
                    'active' => true,
                    'created_at' => now(),
                ];
            }

            Commission::insert($data);
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

    public function setGameEvent(Request $request)
    {
        try {
            $event = DerbyEvent::find($request->id);

            if($event->status == 'DONE') {
                return response()->json([
                    'message' => 'Can\'t Activate DONE Event!',
                    'success' => false,
                ], 403);
            }

            if($event->status == 'ACTIVE') {
                // set current event as done
                $event->status = 'DONE';
                $event->save();

                // set next event as active
                $next = $event->next();
                $next->status = 'ACTIVE';
                $next->save();
            }

            if($event->status == 'WAITING') {
                // dont active event
                DerbyEvent::where('status','ACTIVE')
                    ->update(['status' => 'DONE']);

                // activate selected event
                $event->status = 'ACTIVE';
                $event->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'success' => false,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success',
        ]);
    }
}
