<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Agent;
use App\Models\User;
use App\Models\Referral;
use App\Models\CommissionHistory;
use App\Models\AgentCommission;
use Illuminate\Http\Request;

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
        ]);
    }

    public function getPlayerList(Request $request)
    {
        $sort = json_decode($request->sorting);
        $raw = Referral::with('user')->where('referrer_id', Auth::user()->id);

        $count = $raw->count();


        $filtered = $raw->offset($request->start ?? 0)
            ->limit($request->size ?? 10)->get();


        if (count($sort) > 0 && !empty($sort) && $sort[0]->desc) {
            $filtered->sortByDesc($sort[0]?->id ?? 'id');
        }

        return response()->json([
            'data' => $filtered,
            'total' => $count,
            'request' => $request->all(),
        ]);
    }

    public function topUpPoints(Request $request)
    {
        try {
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

            $agent = Auth::user();
            $agent->points -= $request->amount;
            $agent->save();


            $user = User::find($request->userId);
            $user->points += $request->amount;
            $user->save();

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
}
