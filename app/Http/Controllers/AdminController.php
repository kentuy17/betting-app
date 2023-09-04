<?php

namespace App\Http\Controllers;

use App\Models\ModelHasRoles;
use Illuminate\Http\Request;
use App\Models\ShareHolder;
use App\Models\User;
use App\Models\Roles;
use App\Models\Agent;
use App\Models\Incorpo;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Auth\RegisterController;

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
            $incorpo = Incorpo::where('master_agent', 1)->get();
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

            if ($create) {
                $corpo = new Request();
                $corpo->user_id = $request->user_id;
                $this->addAgent($corpo);
            }
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
        $default_pass = '123456';
        $corpo = User::find($request->user_id);
        $reg = new RegisterController();
        for ($i = 1; $i <= $request->agent_count; $i++) {
            $agent_req = new Request([
                'username' => $corpo->username . $i,
                'password' => $default_pass,
                'password_confirmation' => $default_pass,

            ]);
        }
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
        ]);
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
