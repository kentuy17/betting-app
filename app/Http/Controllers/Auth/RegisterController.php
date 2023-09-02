<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\ModelHasRoles;
use App\Models\Referral;
use App\Models\Agent;
use App\Models\Promo;
use App\Models\IpBan;
use App\Models\AgentCommission;
use App\Models\Hacking;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone_no' => ['string', 'string', 'max:255', 'unique:users,phone_no'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    private function checkIfBan($ip)
    {
        $ip_ban = IpBan::where('ip_address', $ip)->first();
        return $ip_ban ? true : false;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $validator = $this->validator($data);
        $ip_ban = IpBan::where('ip_address', request()->ip())->first();

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        $create = User::create([
            'username' => $validated['username'],
            'phone_no' => $validated['phone_no'],
            'password' => Hash::make($validated['password']),
            'email' => request()->ip(),
            'active' => true,
            'name' => $validated['username'],
        ]);

        $referrer = null;
        $referral = null;
        $agent_comm = null;

        if (isset($data['rid'])) {
            $referrer = User::where('rid', $data['rid'])->first();
            $agent = Agent::where('user_id', $referrer->id)->first();
            $agent->player_count += 1;
            $agent->save();

            $referral = Referral::create([
                'rid' => $data['rid'],
                'referrer_id' => $referrer->id,
                'user_id' => $create->id,
            ]);

            $agent_comm = AgentCommission::create([
                'agent_id' => $referrer->id,
                'user_id' => $create->id,
                'commission' => 0,
            ]);
        }

        $active_codes = ['SWW-SIQ', 'SWW23-FCK'];
        $bonus = false;

        if ($data['code'] && in_array(strtoupper($data['code']), $active_codes)) {
            $bonus = true;
            if (!$referral) {
                Referral::create([
                    'rid' => 'REFSVWXM9N8',
                    'referrer_id' => 1,
                    'user_id' => $create->id,
                ]);
            }
        }

        if ($referrer && $referrer->id == 1) {
            $bonus = true;
        }

        if($ip_ban) {
            $bonus = false;
        }

        if ($bonus) {
            Promo::create([
                'user_id' => $create->id,
                'code' => $data['code'] ?? 'SWW23',
            ]);

            // $create->points = 150;
            $create->points = 50;
            $create->save();

            if (!$agent_comm) {
                AgentCommission::create([
                    'agent_id' => 1,
                    'user_id' => $create->id,
                    'commission' => 0,
                ]);
            }
        }

        ModelHasRoles::create([
            'role_id' => '2',
            'model_type' => "App\Models\User",
            'model_id' => $create->id,
        ]);

        return $create;
    }

    public function showRegistrationForm(Request $request)
    {
        return view('auth.dark-register');
    }
}
