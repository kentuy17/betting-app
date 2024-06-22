<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;
    protected $redirectTo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        return config('app.under_construction')
            ? view('auth.dark-login-mt')
            : view('auth.dark-login');
    }

    public function secritLogin()
    {
        return view('auth.dark-secrit-login');
    }

    public function redirectTo()
    {
        $role = $this->getUserRole();
        $user = User::find(Auth::user()->id);
        $user->update(['active' => true]);
        $user->save();

        $info = [
            'ip' => request()->ip(),
            'user' => $user->username,
            'points' => $user->points,
        ];

        Log::channel('custom')->info(json_encode($info));

        if (Auth::user()->defaultpassword) {
            $this->redirectTo = '/changepassword';
        }

        if ($role->name == 'Player') {
            $this->redirectTo = '/landing';
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

        return $this->redirectTo;
    }
}
