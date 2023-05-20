<?php

namespace App\Http\Controllers;

use App\Models\DerbyEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use App\Models\User;
use App\Models\Transactions;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response as FacadeResponse;

class PlayerController extends Controller
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
        $role = $this->getUserRole();
        $fight = DerbyEvent::where('status','ACTIVE')
            ->orderBy('id','desc')
            ->first();

        return view('player.play', compact('role','fight'));
    }

    public function bethistory()
    {
        return view('player.bet-history');
    }

    public function playerTransaction()
    {
        return view('player.player-transaction');
    }

    public function deposit()
    {
        $user = Auth::user();
        $active_operators = ModelHasRoles::with('users')->has('active_operators')->get()
            ->pluck('users')
            ->sortBy('points')
            ->first();

        $low_pts = ModelHasRoles::with('users')->has('operators')->get()
            ->pluck('users')
            ->sortBy('points')
            ->first();

        $operators = $active_operators ?? $low_pts;
        return view('player.deposit', compact('user', 'operators'));
    }

    public function getTransactionByPlayerController()
    {
        $trans = Transactions::where('user_id', Auth::user()->id)
            ->with('user')
            ->with('operator')
            ->orderBy('id','desc')
            ->get();

        return response()->json([
              'data' => $trans,
        ]);
    }

    public function profileWithdraw()
    {
        $user = Auth::user();
        return view('player.withdraw', compact('user'));
    }

    public function withdraw()
    {
        return view('player.withdraw-form');
    }

    public function depositSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'formFile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);  
             
            $trimPhone = $request->phone_no;
            if (Str::startsWith($request->phone_no, ['+63', '63'])) {
                 $trimPhone = preg_replace('/^\+?63/', '0', $trimPhone);
            }
            else if (Str::startsWith($request->phone_no, ['9'])) {
                $trimPhone = '0' . $request->phone_no;
            }
    
            $this->validate($request, [
                'phone_no' => ['regex:/(0?9|\+?63)[0-9]{9}/'],
            ]);

            $imageName = time().'.'.$request->formFile->extension();
            $path = 'public/' . $imageName;
            Storage::disk('local')->put($path, file_get_contents($request->formFile));

            Transactions::create([
                'user_id' => Auth::user()->id,
                'action' => 'deposit',
                'mobile_number' => $request->phone_no,
                'filename' => $imageName,
                'status' => 'pending',
                'processedBy' => $request->operator_id,
                'outlet' => $request->outlet ?? 'Gcash',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Submitted Successfully!');
    }

    public function withdrawSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required',
            ]);  

            $user = User::find(Auth::user()->id);
            if($user->points < $request->amount) {
                return redirect()->back()
                    ->with('danger', 'Insuficient points!');
            }
            
            $user->points -=  $request->amount;
            $user->save();

            Transactions::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'mobile_number' => $request->phone_no,
                'action' => 'withdraw',
                'status' => 'pending',
                'processedBy' => null,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Withdraw Request Successful!');
    }

    public function submitWithdraw(Request $request) 
    {
        try {
            $user = User::find(Auth::user()->id); 
            if (Hash::check($user->password, $request->curr_pass)) {
            //   if($user->password != bcrypt($request->curr_pass)) {
                  return redirect('/withdrawform')->with('error', 'Incorrect Password!');
              }

            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required',
                'curr_pass' => 'required'
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

             if($user->points < $request->amount){
                 return redirect('/withdrawform')->with('error', 'Insufficient Amount!');
             }

            Transactions::create([
                'user_id' => Auth::user()->id,
                'action' => 'withdraw',
                'mobile_number' => $trimPhone,
                'status' => 'pending',
                'amount' => $request->amount,
                'outlet' => $request->outlet,
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/withdrawform')->with('success', 'Submitted Successfully!');
    }

    public function video()
    {
        $video = Storage::disk('local')->get("hls/mystream.m3u8");
        $response = FacadeResponse::make($video, 200);
        $response->header('Content-Type', 'video/mp4');
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Access-Control-Allow-Headers', 'Origin');
        $response->header('Access-Control-Allow-Methods', '*');

        return $response;
    }
}
