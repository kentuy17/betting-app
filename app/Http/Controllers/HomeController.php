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
            if(Auth::user()->defaultpassword){
                $this->redirectTo = '/changepassword';
            } else{
                $this->redirectTo = '/play';
            }
        }

        if($role->name == 'Operator') {
            $this->redirectTo = '/fight';
        }

        if($role->name == 'Cash-out Operator' || $role->name == 'Cash-in Operator') {
            $this->redirectTo = '/transactions';
        }

        if($role->name == 'Admin') {
            $this->redirectTo = '/admin';
        }

        if($role->name == 'Auditor') {
            $this->redirectTo = '/transactions-auditor';
        }

        return redirect($this->redirectTo);
    }

    public function showChangePasswordGet() {
        return view('auth.change-password');
    }

    public function changePasswordPost(Request $request) {
        // if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
        //     // The passwords matches
        //     return redirect()->back()->with("error","Your current password does not matches with the password.");
        // }

        if(strcmp($request->get('current-password'), $request->get('new-password')) == 0){
            // Current password and new password same
            return redirect()->back()->with("error","New Password cannot be same as your current password.");
        }

        $validatedData = $request->validate([
            'new-password' => 'required|string|min:6|confirmed',
        ]);

        //Change Password
        $user = Auth::user();
        $user->password = Hash::make($request->get('new-password'));
        $user->defaultpassword = false;
        $user->save();

        return redirect('/play');
    }

}
