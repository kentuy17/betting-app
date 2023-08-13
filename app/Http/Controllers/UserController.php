<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transactions;
use App\Models\ShareHolder;
use App\Models\Setting;
use App\Models\Referral;
use App\Models\Promo;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Support\Str;

use DB;
use Hash;

class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
         $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        $role = $this->getUserRole();
        return view('users.index',compact('data','role'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.create',compact('roles'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'username' => 'required|unique:users,username,'.$id,
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')
                        ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');
    }

    public function profile()
    {
        $user = Auth::user();
        $referral = Referral::where('user_id', Auth::user()->id)
            ->where('promo_done',false)
            ->first();

        $promo = Promo::where('user_id', Auth::user()->id)->first();
        $availed = $referral && $promo ? false : true;
        return view('users.userprofile', compact('user','availed'));
    }

    public function getProfileByUserID()
    {
        $profile = User::where('id', Auth::user()->id)
            ->get();

        return response()->json([
              'data' => $profile,
        ]);
    }

    public function editprofile(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required'
            ]);
            $trimPhone = $request->phone_no;
            if (Str::startsWith($request->phone_no, ['+63', '63']))
            {
                 $trimPhone = preg_replace('/^\+?63/', '0', $trimPhone);
            }else if (Str::startsWith($request->phone_no, ['9']))
            {
                $trimPhone = '0' . $request->phone_no;
            }

                $this->validate($request, [
               'phone_no' => ['regex:/(0?9|\+?63)[0-9]{9}/'],
                ]);

            if( User::where('phone_no', '=', $request->phone_no)->exists()
                && $request->phone_no != Auth::user()->phone_no ) {
                return Redirect()->back()->withInput()->with('danger', 'This Number exist !');
            }

            $user = User::find(Auth::user()->id);
            $user->phone_no = $trimPhone;
            $user->password = bcrypt($request->new_pass);

            if(Auth::user()->password != bcrypt($request->new_pass)) {
                if($request->new_pass != $request->confirm_pass) {
                    return redirect('/user/profile')->with('danger', 'Password did not Match!');
                }

                $user->password = bcrypt($request->new_pass);
            }

            $userArray=$user->toArray();
            // $user->updateContactNumber($user->id,$userArray);
            $user->update($userArray);
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect('/user/profile')->with('success', 'Updated Successfully!');
    }

    public function updatePoints($id)
    {
        try {
            $trans = Transactions::find($id);
            $user = User::find($trans->user_id);

            $user->points += $trans->amount;
            $trans->status = "completed";

            $user->save();
            $trans->save();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Updated Successfully!');
    }

    public function convertCommission(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $boss = ShareHolder::where('user_id', $user->id)->first();

        if($request->points > $boss->current_commission) {
            return response()->json([
                'current_commission' => $boss->current_commission,
                'points' => $user->points,
                'message' => 'Insuficient Commission Points!',
            ], 403);
        }

        $user->points += $request->points;
        $user->save();

        $boss->current_commission -= $request->points;
        $boss->save();

        return response()->json([
            'current_commission' => number_format($boss->current_commission, 2),
            'points' => number_format($user->points, 2),
        ], 200);
    }

    public function setVideoDisplay(Request $request)
    {
        $setting = Setting::where('name','video_display')->first();
        $setting->value = $request->screen;
        $setting->save();

        return response()->json([
            'data' => $setting,
        ]);
    }
}
