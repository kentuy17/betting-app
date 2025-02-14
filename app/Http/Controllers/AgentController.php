<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Agent;
use App\Models\User;
use App\Models\Referral;
use App\Models\CommissionHistory;
use App\Models\AgentCommission;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Log;

class AgentController extends Controller
{
    public function commissionConvert(Request $request)
    {
        try {
            if ($request->points > Auth::user()->agent->current_commission) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Insufficient points to convert',
                ], 402);
            }

            if ($request->points < 200) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Minimum commission points is 200.00',
                ], 402);
            }

            $agent = Agent::where('user_id', Auth::user()->id)->first();
            $agent->current_commission -= $request->points;
            $agent->save();

            $user = User::find(Auth::user()->id);
            $user->points += $request->points;
            $user->save();

            CommissionHistory::create([
                'user_id' => Auth::user()->id,
                'points_converted' => $request->points,
                'current_points' => $user->points,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'data' => [
                'current_commission' => number_format($agent->current_commission, 2),
                'points' => $user->points,
            ],
            'status' => 'success',
        ], 200);
    }

    public function playersUnder()
    {
        return view('agents.index');
    }

    public function playerLists()
    {
        // $players = Referral::with('user')
        //     ->with('bet')
        //     ->withSum('bet','agent_commission')
        //     ->where('referrer_id', Auth::user()->id)->get();

        $players = AgentCommission::with('user', 'agent')
            ->where('agent_id', Auth::user()->id)
            ->get();

        return response()->json([
            'data' => $players,
            'status' => 'success',
        ], 200);
    }

    public function agentPlayerList()
    {
        try {
            $agents = Agent::where('player_count', '>', 0)
                ->whereNotIn('user_id', [10])
                ->get();

            $players = Referral::with('user')
                ->with('bet')
                ->withSum('bet', 'agent_commission')
                ->whereIn('referrer_id', $agents->pluck('user_id'))->get();

            $sums = [];
            foreach ($players as $key => $player) {
                $sums[] = $player->bet_sum_agent_commission;
                AgentCommission::create([
                    'agent_id' => $player->referrer_id,
                    'user_id' => $player->user_id,
                    'commission' => $player->bet_sum_agent_commission ?? 0,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'message' => 'OK',
            'data' => [
                'players' => $players,
                'sums' => $sums,
            ],
        ], 200);
    }

    public function masterAgent()
    {
        return view('agents.master-agent');
    }

    public function getMasterAgentPoints()
    {
        return response()->json([
            'points' => Auth::user()->points,
            'commission' => Auth::user()->agent->current_commission,
            'players' => Auth::user()->agent->player_count,
            'ref_link' => 'https://isp24.live/register?rid=' . Auth::user()->rid,
        ]);
    }

    public function getPlayerList(Request $request)
    {
        $sort = json_decode($request->sorting);
        $aggrate_col = 'commission';
        $aggrate_model = 'agent_commission';
        $order_by = 'desc';

        if (count($sort) > 0 && !empty($sort) && $sort[0]->id != 'type') {
            $tmp = $sort[0]->id;
            $aggregate = explode('.', $tmp);
            $aggrate_col = $aggregate[1];
            $aggrate_model = $aggregate[0] ?? 'user';
            $order_by = $sort[0]->desc ? 'desc' : 'asc';
        }

        $raw = Referral::withAggregate($aggrate_model, $aggrate_col)
            ->with('user', 'agent_commission', 'sub_agent')
            ->where('referrer_id', Auth::user()->id)
            ->orderBy($aggrate_model . '_' . $aggrate_col, $order_by);

        $count = $raw->count();


        $filtered = $raw->offset($request->start ?? 0)
            ->limit($request->size ?? 10)->get();

        return response()->json([
            'data' => $filtered,
            'total' => $count,
            'request' => $request->all(),
        ]);
    }

    public function topUpPoints(Request $request)
    {
        try {
            /**
             * JohnWick => 818
             * Papawa   => 92966
             * 
             */
            $prohibited = [818, 92966];
            // $allow = [93008, 92342, 1, 6];
            if (in_array(Auth::user()->id, $prohibited)) {
                return response()->json([
                    'error' => 'Disabled sorry',
                    'status' => 500,
                ], 500);
            }

            // if (in_array(Auth::user()->id, [10, 92966]) && $request->amount > 500) {
            //     $papawa = Transactions::where('processedBy', 92966)
            //         ->where('action', 'topup')
            //         ->whereDate('created_at', Carbon::now())
            //         ->sum('amount');

            //     if ($papawa > 500) {
            //         $this->hacking($request, 'topup');
            //         return response()->json([
            //             'error' => 'ayaw pasulabi do',
            //             'status' => 402,
            //         ], 402);
            //     }
            // }

            $referral = Referral::where('user_id', $request->userId)->first();

            if ($referral->referrer_id != Auth::user()->id) {
                return response()->json([
                    'error' => 'Invalid request!',
                    'status' => 402,
                ], 402);
            }

            if (Auth::user()->points < $request->amount) {
                return response()->json([
                    'error' => 'Insufficient points!',
                    'status' => 402,
                ], 402);
            }

            $user = User::find($request->userId);
            $cashin = Transactions::create([
                'user_id' => $user->id,
                'action' => 'topup',
                'mobile_number' => $user->phone_no,
                'status' => 'completed',
                'processedBy' => Auth::user()->id,
                'outlet' => 'Agent',
                'note' => 'Agent Topup',
                'morph' => 0,
                'amount' => $request->amount,
            ]);

            if ($cashin) {
                $agent = Auth::user();
                $agent->points -= $request->amount;
                $agent->save();

                $user = User::find($request->userId);
                $user->points += $request->amount;
                $user->save();
            }

            $players = Referral::with('user')->where('referrer_id', $referral->referrer_id)->get();
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

        return response()->json([
            'status' => 200,
            'total' => $players->count(),
            'data' => $players,
        ], 200);
    }

    public function updateAgentType(Request $request)
    {
        try {
            $agent = Agent::where('user_id', $request->user_id)->first();
            $user = User::find($request->user_id);

            if (!$user->rid) {
                $user->rid = 'REF' . $this->generateRandomString(8);
                $user->save();
            }

            if ($request->type == '') {
                return response()->json(['error' => 'Invalid type!'], 400);
            }

            if ($request->type == 'player') {
                if ($agent && $agent->current_commission > 0) {
                    return response()->json([
                        'error' => 'Player has commission!',
                        'status' => 400,
                    ], 400);
                }

                if ($agent) $agent->delete();
            }

            if ($request->type == 'sub-agent') {
                if ($agent) {
                    $agent->percent = $request->percent;
                    $agent->type = $request->type;
                    $agent->is_master_agent = 1; // Agent Dashboard Access
                    $agent->save();
                } else {
                    $agent = Agent::create([
                        'rid' => $user->rid ?? 'REF' . $this->generateRandomString(8),
                        'user_id' => $request->user_id,
                        'percent' => $request->percent,
                        'player_count' => Referral::where('referrer_id', $request->user_id)->count(),
                        'is_master_agent' => 1,
                        'type' => 'sub-agent',
                    ]);
                }
            }

            if ($request->type == 'master-agent') {
                if ($agent) {
                    $agent->percent = 3;
                    $agent->type = $request->type;
                    $agent->is_master_agent = 1; // Agent Dashboard Access
                    $agent->save();
                } else {
                    $agent = Agent::create([
                        'rid' => $user->rid ?? 'REF' . $this->generateRandomString(8),
                        'user_id' => $request->user_id,
                        'percent' => $request->percent,
                        'player_count' => Referral::where('referrer_id', $request->user_id)->count(),
                        'is_master_agent' => 1,
                        'type' => 'master-agent',
                    ]);
                }
            }

            if ($request->type == 'agent') {
                if ($agent) {
                    $agent->percent = $request->percent;
                    $agent->type = $request->type;
                    $agent->is_master_agent = 1; // Agent Dashboard Access
                    $agent->save();
                } else {
                    $agent = Agent::create([
                        'rid' => $user->rid ?? 'REF' . $this->generateRandomString(8),
                        'user_id' => $request->user_id,
                        'percent' => $request->percent,
                        'player_count' => Referral::where('referrer_id', $request->user_id)->count(),
                        'is_master_agent' => 1,
                        'type' => 'agent',
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

        return response()->json([
            'data' => $agent,
            'status' => 200,
        ], 200);
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public function userMasterAgent()
    {
        $agent = Agent::where('user_id', Auth::user()->id)->first();
        return response()->json([
            'type' => $agent->type,
        ], 200);
    }
}
