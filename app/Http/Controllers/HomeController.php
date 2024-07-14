<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;

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
        // $this->middleware('maintenance');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $role = $this->getUserRole();
        if ($role->name == 'Player') {
            # if (Auth::user()->defaultpassword) {
            #     $this->redirectTo = '/changepassword';
            # } else {
            #     $this->redirectTo = '/landing';
            # }
            $this->redirectTo = Auth::user()->defaultpassword
                ? '/changepassword'
                : '/landing';
        }

        if ($role->name == 'Operator') {
            $this->redirectTo = '/fight';
        }

        if ($role->name == 'Cash-out Operator' || $role->name == 'Cash-in Operator') {
            $this->redirectTo = '/landing';
        }

        if ($role->name == 'Admin') {
            $this->redirectTo = '/landing';
        }

        if ($role->name == 'Auditor') {
            $this->redirectTo = '/transactions-auditor';
        }

        if ($role->name == 'Guest Admin') {
            $this->redirectTo = '/transactions';
        }

        return redirect($this->redirectTo);
    }

    public function showChangePasswordGet()
    {
        return view('auth.change-password');
    }

    public function changePasswordPost(Request $request)
    {
        if ($request->new_password !== $request->confirm_password) {
            return redirect()->back()
                ->with('error', 'Password does not match');
        }

        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->defaultpassword = false;
        $user->save();

        return redirect('/play');
    }

    public function getNotifications()
    {
        return false;
    }
}
