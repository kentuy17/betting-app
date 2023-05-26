<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use \Illuminate\Support\Str;
use App\Models\Transactions;

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
            'username' => 'required|unique:users',
            'password' => 'same:confirm-password',
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
        return view('users.userprofile', compact('user'));
        //return view('users.userprofile');
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
                return Redirect()->back()->withInput()->with('error', 'This Number exist !');
            }

            $user = User::find(Auth::user()->id);
            $user->phone_no = $trimPhone;
            $user->password = bcrypt($request->new_pass);

            if(Auth::user()->password != bcrypt($request->new_pass)) {
                if($request->new_pass != $request->confirm_pass) {
                    return redirect('/user/profile')->with('error', 'Password did not Match!');
                }

                $user->password = bcrypt($request->new_pass);
            }

            $userArray=$user->toArray();
            $user->updateContactNumber($user->id,$userArray);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
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

            // return $user;
            // $userArray=$user->toArray();
            // $user->updateContactNumber($user->id,$userArray);

            // $transArray=$trans->toArray();
            // $trans->updateStatus($trans->id,$userArray);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Updated Successfully!');
    }
}
