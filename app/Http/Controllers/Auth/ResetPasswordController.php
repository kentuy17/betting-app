<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Models\User;
use App\Models\UserPasswordReset;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;


        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showresetpasswordview()
    {
        return view('auth.dark-reset');
    }

    public function submitresetpassword(Request $request)
    {
        try{
            $this->validate($request, [
                'username' => 'required',
                'phone_no' => 'required',
                'phone_no' => ['regex:/(0?9|\+?63)[0-9]{9}/'],
            ]);
    
            if( !User::where('phone_no', '=', $request->phone_no)
                    ->where('username', '=', $request->username)
                    ->exists()) {
                    return redirect('/password_reset')->with('error', 'Account not found!');
                }
    
            $users = User::where('phone_no', '=', $request->phone_no)
            ->where('username', '=', $request->username)
            ->first();


             $user = User::find($users->id);
             $user->password =  Hash::make('000000');
             $user->defaultpassword = true;
             $user->save();

            UserPasswordReset::create([
                'userid' => $user->id,
                'username' => $user->username,
                'phone_no' => $user->phone_no,
                'password' => '000000',
                'status' => 'pending'
            ]);       
    
        }catch (\Exception $e) {
            return redirect()->back()->with('Error', $e->getMessage());
        }
        return redirect('/password_reset')->with('success', 'Wait for new password via text');
    }
}
