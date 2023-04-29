<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

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

    public function redirectTo()
    {
        \Log::channel('custom')->info(json_encode(Auth::user(),JSON_PRETTY_PRINT));
        $role = $this->getUserRole();
        if($role->name == 'Player') {
            $this->redirectTo = '/play';
        } 
        
        if($role->name == 'Operator') {
            $this->redirectTo = '/home';
        }

        if($role->name == 'Admin') {
            $this->redirectTo = '/home';
        }

        return $this->redirectTo;
    }
}
