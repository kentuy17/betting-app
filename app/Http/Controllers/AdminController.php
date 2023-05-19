<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShareHolder;

class AdminController extends Controller
{
    public function shareHolders()
    {
        $share_holders = ShareHolder::with('user')->get();
        return view('admin.share-holders', compact('share_holders'));
    }
}
