<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShareHolder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view('users.index');
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
}
