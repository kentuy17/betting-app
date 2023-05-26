<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShareHolder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function shareHolders()
    {
        $share_holders = ShareHolder::with('user')->get();
        return view('admin.share-holders', compact('share_holders'));
    }

    public function getOnlineUsers()
    {
      try {
        $onlineUsers = User::online()->get();
      }
      catch (\Exception $e) {
        return response($e, 500);
      }

      return response()->json([
        'data' => $onlineUsers,
        'message' => 'OK',
      ], 200);
    }
}
