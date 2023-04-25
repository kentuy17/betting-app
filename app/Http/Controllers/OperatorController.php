<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Transactions;

class OperatorController extends Controller
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
        return view('home');
    }

    public function fight(): View
    {
        return view('operator.fight');
    }

    public function transactions()
    {
        return view('operator.transactions');
    }

    public function getTransactions()
    {
        $trans = Transactions::with('user')->get();
        return response()->json([
            'data' => $trans
        ]);
    }
}
