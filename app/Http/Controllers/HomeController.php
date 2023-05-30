<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;

class HomeController extends Controller
{
    protected $redirectTo;
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
    public function index()
    {
        $role = $this->getUserRole();
        if($role->name == 'Player') {
            $this->redirectTo = '/play';
        }

        if($role->name == 'Operator' || $role->name == 'Cash-out Operator' || $role->name == 'Cash-in Operator') {
            $this->redirectTo = '/fight';
        }

        if($role->name == 'Admin') {
            $this->redirectTo = '/admin';
        }

        if($role->name == 'Auditor') {
            $this->redirectTo = '/transactions-auditor';
        }

        return redirect($this->redirectTo);
    }
}
