<?php

namespace App\Http\Controllers;

use App\Models\ModelHasRoles;
use Illuminate\Http\Request;
use App\Models\ShareHolder;
use App\Models\User;
use App\Models\Roles;
use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\Incorpo;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $roles = Roles::get();
        return view('users.index', compact('roles'));
    }

    public function shareHolders()
    {
        $share_holders = ShareHolder::with('user')
            ->orderBy('percentage', 'desc')
            ->get();
        return view('admin.share-holders', compact('share_holders'));
    }

    public function getOnlineUsers()
    {
        try {
            $onlineUsers = User::online()->get();
        } catch (\Exception $e) {
            return response($e, 500);
        }

        return response()->json([
            'data' => $onlineUsers,
            'message' => 'OK',
        ], 200);
    }

    public function getNotAgents()
    {
        try {
            $term = request('term')['term'];
            if (strlen($term) > 3) {
                $users = User::where('username', 'LIKE', '%' . $term . '%')
                    ->where('rid', NULL)->get();
            }
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }

        return response()->json([
            'data' => $users
        ], 200);
    }


    public function incorpo()
    {
        return view('admin.incorpo');
    }

    public function getCorpos()
    {
        try {
            $incorpo = Incorpo::with('user')->where('master_agent', 1)->get();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return DataTables::of($incorpo)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function addCorpoAgent(Request $request)
    {
        try {
            //code...
            $create = Incorpo::create([
                'user_id' => $request->user_id,
                'bracket' => $request->bracket,
                'master_agent' => 1, // for main corpo only
                'player_count' => 0,
            ]);

            $corpo = new Request();
            $corpo->user_id = $request->user_id;
            $this->addAgent($corpo);

            $referral = Referral::create([
                'rid' => 'REFSVWXM9N8',
                'referrer_id' => 1,
                'user_id' => $create->id,
            ]);

            AgentCommission::create([
                'agent_id' => $referral->referrer_id,
                'user_id' => $create->id,
                'commission' => 0,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Corpo created!'
        ]);
    }

    public function addCorpSubAgents(Request $request)
    {
        try {
            $master_agent = User::find($request->user_id);
            $corpo = Incorpo::where('user_id', $master_agent->id)
                ->where('master_agent', 1)
                ->first();

            $agents_count = Incorpo::where('master_agent', $corpo->user_id)->count();
            $created_agents = [];
            for ($i = 1; $i <= $request->agent_count; $i++) {
                $randomPass = Str::random(6);
                $rid = 'REF' . $this->generateRandomString(8);
                $alyas = $master_agent->username . $agents_count + $i;

                // create user
                $create = User::create([
                    'username' => $alyas,
                    'name' => $alyas,
                    'phone_no' => $master_agent->phone_no,
                    'password' => Hash::make($randomPass),
                    'active' => true,
                    'rid' => $rid,
                    'role_id' => 2,
                ]);

                $created_agents[] = $create;
                $add_agent_req = new Request([
                    'user_id' => $create->id,
                    'bracket' => $corpo->bracket,
                ]);

                Incorpo::create([
                    'user_id' => $create->id,
                    'bracket' => $corpo->bracket,
                    'master_agent' => $corpo->user_id, // for main corpo only
                    'player_count' => 0,
                    'default_pass' => $randomPass
                ]);

                $this->addAgent($add_agent_req);

                ModelHasRoles::create([
                    'role_id' => '2',
                    'model_type' => "App\Models\User",
                    'model_id' => $create->id,
                ]);

                $referral = Referral::create([
                    'rid' => $rid,
                    'referrer_id' => $corpo->user_id,
                    'user_id' => $create->id,
                ]);

                AgentCommission::create([
                    'agent_id' => $referral->referrer_id,
                    'user_id' => $create->id,
                    'commission' => 0,
                ]);
            }

            $corpo->player_count += count($created_agents);
            $corpo->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Agents created successfully!',
            'data' => $created_agents
        ]);
    }

    public function getSubAgentsByAgentId($id)
    {
        $sub_agents = Incorpo::with('agent_commission', 'user')->where('master_agent', $id)->get();

        return DataTables::of($sub_agents)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function getUsers()
    {
        try {
            if (request('order')[0]['column'] == 4) {
                $users = User::orderBy('points', request('order')[0]['dir'])->get();
            } else {
                $users = User::orderBy('updated_at', 'desc')->get();
            }

            $users_with_roles = [];
            foreach ($users as $user) {
                $users_with_roles[] = $user->getRoleNames();
                if ($user->active && !$user->isOnline()) {
                    $user->active = false;
                    $user->save();
                }
            }
            $users->roles = $users_with_roles;
        } catch (\Exception $e) {
            return response($e, 500);
        }

        return DataTables::of($users)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->addColumn('status', function (User $user) {
                return $user->active == 1 ? 'ONLINE' : 'OFFLINE';
            })
            ->with('online_count', $users->where('active', 1)->count())
            ->toJson();
    }

    public function createUser(Request $request)
    {
        return $request->all();
    }

    public function getUserPagePermissions($user_id)
    {
        try {
            $permissions = ModelHasRoles::where('model_id', $user_id)->get();
            $user = User::find($user_id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'data' => $permissions,
            'user' => $user,
            'message' => 'OK',
        ], 200);
    }

    public function updateUser(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            $user->phone_no = $request->phone_no;
            $user->role_id = $request->role;
            $user->username = $request->username;
            $user->name = $request->name;
            $user->save();

            ModelHasRoles::where('model_id', $user->id)->delete();
            $roles = [];

            // return $request->page_access;
            foreach ($request->page_access as $access) {
                $roles[] = [
                    'model_id' => $user->id,
                    'model_type' => 'App\Models\User',
                    'role_id' => $access,
                    'created_at' => now(),
                ];
            }
            $insert = ModelHasRoles::insert($roles);
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'message' => 'Update Success',
            'data' => $insert,
        ], 200);
    }

    public function getAgents()
    {
        return view('admin.agents');
    }

    public function agentList()
    {
        $agents = Agent::with('user')->orderBy('id', 'desc')->get();
        return response()->json([
            'data' => $agents
        ]);
    }

    private function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

    public function addAgent(Request $request)
    {
        try {
            //code...
            $rid = 'REF' . $this->generateRandomString(8);
            Agent::create([
                'user_id' => $request->user_id,
                'rid' => $rid,
            ]);

            $user = User::find($request->user_id);
            $user->rid = $rid;
            $user->save();
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'data' => 'OK'
        ], 200);
    }

    public function accessUser($id)
    {
        try {
            if (Auth::user()->role_id != 1) {
                $this->hacking(request(), 'Security breached');
                return redirect()->back();
            }

            $user = User::find($id);
            Auth::login($user);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }

        return redirect('/user/profile')
            ->with('success', 'You Are now logged in as ' . Auth::user()->username);
    }
}
