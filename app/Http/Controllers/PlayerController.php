<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;

class PlayerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(): View
    {
        $role = $this->getUserRole();
        return view('player.play', compact('role'));
    }

    public function bethistory()
    {
        return view('player.bet-history');
    }

    public function deposit()
    {
        $user = Auth::user();
        return view('player.deposit', compact('user'));
    }
}
