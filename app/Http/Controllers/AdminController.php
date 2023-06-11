<?php

namespace App\Http\Controllers;

use App\Models\ModelHasRoles;
use Illuminate\Http\Request;
use App\Models\ShareHolder;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Auth;

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
            ->orderBy('percentage','desc')
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

    public function getUsers()
    {
        try {
            $users = User::get();
            $users_with_roles = [];
            foreach ($users as $user) {
                $users_with_roles[] = $user->getRoleNames();
                $user->active = $user->isOnline();
                $user->save();
            }
            $users->roles = $users_with_roles;
        } catch (\Exception $e) {
            return response($e, 500);
        }

        return response()->json([
            'data' => $users,
            'message' => 'OK',
        ], 200);
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
}
