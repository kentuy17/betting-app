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
use App\Models\Referral;
use App\Models\Agent;
use App\Models\AgentCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Str;

class FightController extends Controller
{
    public $current_event;
    public $prev_match;
    public $fight;
    private $percent = 13;
    private $botchok_id = 10;
    private $payout = 187;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    private function getTotalBets()
    {
        $total_bets = array(
            'meron' =>  0,
            'wala'  =>  0,
        );

        if (!$this->fight) {
            return $total_bets;
        }

        $total_bets['meron'] = Bet::where(['fight_id' => $this->fight->id, 'side' => 'M'])->sum('amount');
        $total_bets['wala'] = Bet::where(['fight_id' => $this->fight->id, 'side' => 'W'])->sum('amount');

        if ($total_bets['meron'] == 0 || $total_bets['wala'] == 0) {
            return array('meron' => 0, 'wala' => 0);
        }

        return $total_bets;
    }

    private function getTotalPlayerBet()
    {
        $player_bet = array('meron' => 0, 'wala' => 0);

        if (!$this->fight) {
            return $player_bet;
        }

        $player_bet['meron'] = Bet::where([
            'fight_id' => $this->fight->id,
            'side' => 'M',
            'user_id' => Auth::user()->id,
        ])->sum('amount');

        $player_bet['wala'] = Bet::where([
            'fight_id' => $this->fight->id,
            'side' => 'W',
            'user_id' => Auth::user()->id,
        ])->sum('amount');

        return $player_bet;
    }

    public function getCurrentFight($type = 'player')
    {
        $acceptHeader = request()->header('Accept');
        if ($acceptHeader != 'application/json') {
            return response()->json([], 406);
        }

        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        $this->prev_match = Fight::where('event_id', $this->current_event->id)
            ->whereNotNull(['game_winner', 'status'])
            ->orderBy('id', 'desc')
            ->first();

        $this->fight = Fight::where('event_id', $this->current_event->id)
            ->whereNull('game_winner')
            ->orderBy('id', 'desc')
            ->first();

        if (!$this->fight) {
            $last_fight = Fight::orderBy('id', 'desc')->first();
            $dummy_fight = [
                'id' =>  +$last_fight->id + 1,
                'event_id' => $this->current_event->id,
                'fight_no' => 1,
                'user_id' => 1,
                'status' => 'O',
                'game_winner' => null,
            ];
        }

        if ($this->fight && $type == 'operator') {
            $actual_bets = [
                'meron' => $this->fight->bet_legit_meron->sum('amount'),
                'wala' => $this->fight->bet_legit_wala->sum('amount'),
            ];
        } else {
            $actual_bets = Str::uuid();
        }

        $total_meron = (int)Redis::get('M') + Redis::get('extra:M');
        $total_wala = (int)Redis::get('W') + Redis::get('extra:W');

        if ($total_meron == 0 && $total_wala == 0) {
            $total_meron = $this->fight->bet->where('side', 'M')->sum('amount');
            $total_wala = $this->fight->bet->where('side', 'W')->sum('amount');
        }

        return response()->json([
            'current' => $this->fight ?? $dummy_fight,
            'points' => Auth::user()->points,
            'event' => $this->current_event,
            'bets' => [
                'meron' => $total_meron,
                'wala' => $total_wala,
            ],
            'player' => $this->getTotalPlayerBet(),
            'id' => Auth::user()->id,
            'legit' => Auth::user()->legit,
            'tokein' => $actual_bets
        ]);
    }

    private function currenctMatch()
    {
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        return Fight::where('event_id', $this->current_event->id)
            ->where('game_winner', null)
            ->orderBy('id', 'desc')
            ->first();
    }

    private function calcFightPercentage()
    {
        $bets = $this->getTotalBets();
        $total_bets = $bets['meron'] + $bets['wala'];

        $meron_comm = $bets['meron'] * $this->percent / 100;
        $win_meron = $total_bets - $meron_comm;
        $meron_percentage = $win_meron > 0 ? $win_meron / $bets['meron'] * 100 : 0;

        $wala_comm = $bets['wala'] * $this->percent / 100;
        $win_wala = $total_bets - $wala_comm;
        $wala_percentage = $win_wala > 0 ? $win_wala / $bets['wala'] * 100 : 0;

        return [
            // 'meron' => $bets['meron'] > 0 ? $win / $bets['meron'] * 100 : 0,
            // 'wala' => $bets['wala'] > 0 ? $win / $bets['wala'] * 100 : 0,
            'meron' => $meron_percentage,
            'wala' => $wala_percentage,
        ];
    }

    private function cleanup($fight_no)
    {
        // Redis::set('fight', $fight_no);
        Redis::set('M', 0);
        Redis::set('W', 0);
        Redis::set('extra:M', 0);
        Redis::set('extra:W', 0);
    }

    public function updateFight(Request $request)
    {
        try {
            $fight = $this->currenctMatch();
            if ($request->status == 'D') {
                $this->cleanup($fight->fight_no);
                return $this->fightDone($fight, $request->result);
            }

            if (!$fight) {
                $this->hacking($request, 'Update Fight');
                throw new \ErrorException('Invalid request');
            }

            $updated = $fight->update(['status' => $request->status]);
            if ($updated) {
                event(new FightEvent($fight));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return $fight;
    }

    private function calculatePrevFight($fight)
    {
        //TotalBats
        $bets = array('meron' => 0, 'wala' => 0);

        if (!$fight) {
            return $bets;
        }

        $bets['meron'] = Bet::where(['fight_id' => $fight->id, 'side' => 'M'])->sum('amount');
        $bets['wala'] = Bet::where(['fight_id' => $fight->id, 'side' => 'W'])->sum('amount');

        if ($bets['meron'] == 0 || $bets['wala'] == 0) {
            return array('meron' => 0, 'wala' => 0);
        }

        //TotalBets
        //Calculation
        $total_bets = $bets['meron'] + $bets['wala'];

        $meron_comm = $bets['meron'] * $this->percent / 100;
        $win_meron = $total_bets - $meron_comm;
        $meron_percentage = $win_meron / $bets['meron'] * 100;

        $wala_comm = $bets['wala'] * $this->percent / 100;
        $win_wala = $total_bets - $wala_comm;
        $wala_percentage = $win_wala / $bets['wala'] * 100;

        return [
            // 'meron' => $bets['meron'] > 0 ? $win / $bets['meron'] * 100 : 0,
            // 'wala' => $bets['wala'] > 0 ? $win / $bets['wala'] * 100 : 0,
            'meron' => $meron_percentage,
            'wala' => $wala_percentage,
        ];
    }

    public function revertResult(Request $request)
    {
        $this->current_event = DerbyEvent::where('status', 'ACTIVE')->first();

        $fightDetail = Fight::where('fight_no', $request->fight_no)
            ->where('event_id', $this->current_event->id)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$fightDetail) {
            return response()->json([
                'message' => 'Invalid fight number!',
                'status' => 'Error',
            ], 402);
        }

        $faultWinner = $fightDetail->game_winner;

        //Get revert win amount
        if ($faultWinner == 'M' || $faultWinner == 'W') {
            $bets = BetHistory::where('fight_id', $fightDetail->id)
                ->whereNot('user_id', 9)
                ->get();

            foreach ($bets as $bet_h) {
                $to_revert = BetHistory::find($bet_h->bethistory_no);
                $affected_user = User::find($bet_h->user_id);

                if ($bet_h->side == $faultWinner) {
                    $affected_user->points -= $bet_h->winamount;

                    $to_revert->status = 'L';
                    $to_revert->winamount = 0;
                } else {
                    $win_amount_sana = ($bet_h->betamount * $bet_h->percent) / 100;
                    $affected_user->points += $win_amount_sana;

                    $to_revert->status = 'W';
                    $to_revert->winamount = $win_amount_sana;
                }

                $to_revert->save();
                $affected_user->save();
            }
        }

        if ($faultWinner == 'D' || $faultWinner == 'C' && !in_array($request->result, ['D', 'C'])) {
            $bets = BetHistory::with('user')
                ->where('fight_id', $fightDetail->id)->get();

            foreach ($bets as $bet) {
                # code...
                $status = $request->result == $bet->side ? 'W' : 'L';
                $percent = ($this->payout * 2) - $bet->percent > $this->payout
                    ? 180.00
                    : ($this->payout * 2) - $bet->percent;
                $win = $status == 'W' ? ($percent * 0.01) * $bet->betamount : 0;

                $bet->status = $status;
                $bet->percent = $percent;
                $bet->winamount =  $win;
                $bet->save();

                $player = User::find($bet->user_id);
                $player->points = ($player->points - $bet->betamount) - $win;
                $player->save();
            }
        }

        //Update Fight
        $fightDetail->game_winner = $request->result;
        $fightDetail->save();

        return response()->json([
            'data' => $fightDetail,
            'message' => 'Succesfully reverted!',
        ], 200);
    }

    private function fightDone($last_fight, $winner)
    {
        $last_fight->update([
            'status' => 'D',
            'game_winner' => $winner,
        ]);

        Bet::where('fight_id', $last_fight->id)
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

        if ($winner == 'C' || $winner == 'D') {
            $this->fight = $last_fight;
            $user_bets = Bet::where('fight_id', $last_fight->id)
                ->whereNot('user_id', 9)
                ->get();

            $percentage = 0;
            foreach ($user_bets as $bet) {
                $update = Bet::with('user')->where('bet_no', $bet->bet_no)->first();
                $update->win_amount = $bet->amount;
                $update->status = 'X';
                $update->save();

                $user = User::find($bet->user_id);
                $user->points += $bet->amount;
                $user->save();

                if ($winner == 'D') {
                    $calc = $this->calcFightPercentage();
                    $percentage = $bet->side == 'M' ? $calc['meron'] : $calc['wala'];
                }

                $betHist = BetHistory::where('bet_id', $bet->bet_no)->first();
                $betHist->percent = $percentage;
                $betHist->winamount = $bet->amount;
                $betHist->current_points = $user->points;
                $betHist->status = $winner;
                $betHist->save();
                // event(new Result($update));
            }
        }

        if ($winner == 'M' || $winner == 'W') {
            $this->fight = $last_fight;
            $calc = $this->calcFightPercentage();
            $percentage = $winner == 'M' ? $calc['meron'] : $calc['wala'];

            $user_bets = Bet::where('side', $winner)
                ->where('fight_id', $last_fight->id)
                ->whereNot('user_id', 9)
                ->get();

            foreach ($user_bets as $bet) {
                $update = Bet::with('user')->where('bet_no', $bet->bet_no)->first();
                $update->win_amount = $bet->amount * $percentage / 100;
                $update->save();

                $user = User::find($bet->user_id);
                $user->points += $bet->amount * $percentage / 100;
                $user->save();

                $betHist = BetHistory::where('bet_id', $bet->bet_no)->first();
                $betHist->percent = $percentage ?? $this->payout;
                $betHist->winamount = $update->win_amount;
                $betHist->current_points = $user->points;
                $betHist->status = 'W';
                $betHist->save();
                // event(new Result($update));
            }

            // Agent/Master-agent Commission
            $this->calcRefComm($this->fight->id);

            #region updatelose history
            $lose = $winner == 'M' ? 'W' : 'M';
            $percentagelb = $lose == 'M' ? $calc['meron'] : $calc['wala'];

            $lose_bet = Bet::where('side', $lose)
                ->where('fight_id', $last_fight->id)
                ->whereNot('user_id', 9)
                ->get();

            foreach ($lose_bet as $lb) {
                $user_2 = User::find($lb->user_id);
                $betHistLB = BetHistory::where('bet_id', $lb->bet_no)->first();
                $betHistLB->percent = $percentagelb  ?? $this->payout;
                $betHistLB->current_points = $user_2->points;
                $betHistLB->status = 'L';
                $betHistLB->save();
            }
            #endregion
        }

        return response()->json([
            'data' => $new_fight
        ]);
    }

    private function commissionAmount($base_amount, $percent)
    {
        return ($base_amount * $percent) / 100;
    }

    public function calcRefComm($fight_id)
    {
        if (!$fight_id) {
            throw new \Exception('Invalid fight_id');
        }

        $ghost_bettors = User::where('legit', false)->get()->pluck('id');
        $bets = Bet::where('fight_id', $fight_id)
            ->whereNotIn('user_id', $ghost_bettors)
            ->whereNot('user_id', 9)
            ->where('status', 'D')
            ->with('referral')
            ->has('referral')
            ->get();

        foreach ($bets as $bet) {
            // exclude agents
            if (in_array($bet->referral->referrer_id, [10, 1, 92539, 818]))
                continue;

            $base_amount = $bet->win_amount > 0
                ? $bet->win_amount - $bet->amount
                : $bet->amount;
            $agent = Agent::where('user_id', $bet->referral->referrer_id)->first();
            $commission = $this->commissionAmount($base_amount, $agent->percent);

            $agent->current_commission += $commission;
            $agent->save();

            $agent_commission = AgentCommission::firstOrNew(['user_id' => $bet->user_id]);
            $agent_commission->agent_id = $agent->user_id;
            $agent_commission->commission += $commission;
            $agent_commission->save();

            if ($agent->type === 'sub-agent') {
                $referral = Referral::where('user_id', $agent->user_id)->first();
                $master_agent = Agent::where('user_id', $referral->referrer_id)->first();
                $master_agent_percent = $master_agent->percent - $agent->percent;
                $master_agent_comm = $this->commissionAmount($base_amount, $master_agent_percent);

                $master_agent->current_commission += $master_agent_comm;
                $master_agent->save();

                $master_agent_commission = AgentCommission::firstOrNew(['user_id' => $agent->user_id]);
                $master_agent_commission->agent_id = $master_agent->user_id;
                $master_agent_commission->commission += $master_agent_comm;
                $master_agent_commission->save();

                $commission = $master_agent_comm + $commission;
            }

            Bet::find($bet->bet_no)->update([
                'agent_commission' => $commission
            ]);
        }

        return response()->noContent();
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
            ->orderBy('fight_no', 'ASC')
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

            // if ($event->status == 'DONE') {
            //     return response()->json([
            //         'message' => 'Can\'t Activate DONE Event!',
            //         'success' => false,
            //     ], 403);
            // }

            if ($event->status == 'ACTIVE') {
                // set current event as done
                $event->status = 'DONE';
                $event->updated_by = Auth::user()->id;
                $event->save();

                // set next event as active
                $first = DerbyEvent::find(188);
                $next = $event->next() ?? $first;
                $next->status = 'ACTIVE';
                $event->updated_by = Auth::user()->id;
                $next->save();
            }

            if ($event->status == 'WAITING' || $event->status == 'DONE') {
                // done active event
                DerbyEvent::where('status', 'ACTIVE')
                    ->update(['status' => 'DONE']);

                // activate selected event
                $event->status = 'ACTIVE';
                $event->updated_by = Auth::user()->id;
                $event->save();

                Redis::set('event', $event->id);
            }

            $activated_event = DerbyEvent::where('status', 'ACTIVE')->first();
            if (!$activated_event) {
                $activated_event = DerbyEvent::find(188);
            }

            $fights_count = Fight::where('event_id', $activated_event->id)->count();
            $last_fight = Fight::orderBy('id', 'desc')->first();

            if ($fights_count == 0) {
                Fight::create([
                    // 'id' => $last_fight->id + 1,
                    'event_id' => $activated_event->id,
                    'fight_no' => 1,
                    'user_id' => 1,
                    'status' => null,
                    'game_winner' => null,
                ]);
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
