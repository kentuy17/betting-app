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
use App\Models\BetHistory;
use App\Models\Referral;
use App\Models\Promo;
use App\Models\IpBan;
use App\Models\Chat;
use App\Models\Setting;
use App\Events\CashIn;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

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
        $fight = DerbyEvent::where('status', 'ACTIVE')->orderBy('id', 'desc')->first();
        $video_display = Setting::where('name', 'video_display')->first()->value ?? false;

        $user = Auth::user();
        $user->email = request()->ip();
        $user->last_activity = Carbon::now();
        $user->save();

        return view('player.play', compact('role', 'fight', 'video_display'));
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
        $active_operators = ModelHasRoles::with('users')
            ->has('active_operators')->get()
            ->pluck('users')
            ->sortBy('points')
            ->first();

        $low_pts = ModelHasRoles::with('users')
            ->has('operators')->get()
            ->pluck('users')
            ->sortBy('points')
            ->first();

        $operators = $active_operators ?? $low_pts;
        return view('player.deposit', compact('user', 'operators'));
    }

    public function getTransactionByPlayerController($action = true)
    {
        $trans = Transactions::where('user_id', Auth::user()->id)
            ->with('user')
            ->with('operator')
            ->orderBy('id', 'desc')
            ->where('action', $action)
            ->where('deleted', false)
            ->whereIn('morph', [0, 1])
            ->get();

        return DataTables::of($trans)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function profileWithdraw()
    {
        $user = Auth::user();
        return view('player.withdraw', compact('user'));
    }

    public function withdraw()
    {
        $referral = Referral::where('user_id', Auth::user()->id)
            ->where('promo_done', false)
            ->first();

        $promo = Promo::where('user_id', Auth::user()->id)->first();
        $availed = $referral && $promo ? false : true;
        return view('player.withdraw-form', compact('availed'));
    }

    public function depositSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'formFile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $file = $request->file('formFile');
            $check_receipt = Transactions::where('receipt_name', $file->getClientOriginalName())->first();
            if ($check_receipt) {
                return redirect()->back()
                    ->with('danger', 'Duplicate receipt!');
            }

            // $trimPhone = $request->phone_no;
            // if (Str::startsWith($request->phone_no, ['+63', '63'])) {
            //      $trimPhone = preg_replace('/^\+?63/', '0', $trimPhone);
            // }
            // else if (Str::startsWith($request->phone_no, ['9'])) {
            //     $trimPhone = '0' . $request->phone_no;
            // }

            // $this->validate($request, [
            //     'phone_no' => ['regex:/(0?9|\+?63)[0-9]{9}/'],
            // ]);

            $imageName = time() . '.' . $request->formFile->extension();
            $path = 'public/' . $imageName;
            Storage::disk('local')->put($path, file_get_contents($request->formFile));

            $trans = Transactions::create([
                'user_id' => Auth::user()->id,
                'action' => 'deposit',
                'mobile_number' => $request->phone_no,
                'filename' => $imageName,
                'status' => 'pending',
                'processedBy' => $request->operator_id,
                'receipt_name' => $file->getClientOriginalName(),
                'outlet' => $request->payment_mode ?? 'Gcash',
            ]);

            event(new CashIn($trans));
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Submitted Successfully!');
    }

    // Current Working
    public function withdrawSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required',
            ]);

            $amount = str_replace(',', '', $request->amount);
            $user = User::find(Auth::user()->id);

            if ($amount < 100) {
                return redirect()->back()
                    ->with('danger', "Minimum Withdrawal is P100");
            }

            if ($user->points < $amount) {
                return redirect()->back()
                    ->with('danger', 'Insuficient points!');
            }

            $referral = Referral::where('user_id', Auth::user()->id)->first();
            $promo = Promo::where('user_id', Auth::user()->id)->first();

            if ($promo && !$referral->promo_done) {
                if ($amount < 1500) {
                    return redirect()->back()
                        ->with('danger', "For bonus credit only, you need to win 1500 to cash out " . $request->amount);
                } else {
                    $referral->promo_done = true;
                    $referral->save();

                    IpBan::create([ 'ip_address' => $user->email ]);
                }
            }

            $user->points -= $amount;
            $user->save();

            Transactions::create([
                'user_id' => $user->id,
                'amount' => $amount,
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
                return redirect('/withdrawform')->with('error', 'Incorrect Password!');
            }

            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required',
                'curr_pass' => 'required'
            ]);

            $trimPhone = $request->phone_no;
            if (Str::startsWith($request->phone_no, ['+63', '63'])) {
                $trimPhone = preg_replace('/^\+?63/', '0', $trimPhone);
            } else if (Str::startsWith($request->phone_no, ['9'])) {
                $trimPhone = '0' . $request->phone_no;
            }

            $this->validate($request, ['phone_no' => ['regex:/(0?9|\+?63)[0-9]{9}/']]);

            if ($user->points < $request->amount) {
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

    public function getUserMsg()
    {
        $chat = Chat::where('user_id', Auth::user()->id)
            ->where('role_id', 2)
            ->get();

        return response()->json([
            'data' => $chat,
            'status' => 'OK',
        ], 200);
    }

    public function sendUserMsg(Request $request)
    {
        try {
            Chat::create([
                'user_id' => Auth::user()->id,
                'message' => $request->message,
                'sender' => $request->sender,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'not sent',
            ], 500);
        }
        return response()->json([
            'status' => 'OK',
            'message' => 'sent',
        ], 200);
    }

    public function seenMessage(Request $request)
    {
        try {
            Chat::where('user_id', Auth::user()->id)
                ->where('seen', false)
                ->update([
                    'seen' => true,
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'Failed',
                'message' => $e->getMessage(),
            ], 500);
        }
        return response()->json([
            'status' => 'OK',
            'message' => 'seen',
        ], 200);
    }

    public function getBetsByUserId($id)
    {
        try {
            //code...
            $bets = BetHistory::where('user_id', $id)
                ->orderBy('bethistory_no', 'desc')->get();
        } catch (\Exception $e) {
            //throw $th;
            return response()->json([
                'status' => 'Failed',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'data' => $bets,
        ], 200);
    }

    public function getUserPoints()
    {
        return response()->json([
            'points' => Auth::user()->points,
        ]);
    }

    public function cancelWithdraw(Request $request)
    {
        try {
            $withdraw = Transactions::where('id', $request->id)
                ->where('user_id', Auth::user()->id)
                ->where('action', 'withdraw')
                ->where('status', 'pending')
                ->first();

            if (!$withdraw) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request!!!',
                ], 402);
            }

            $withdraw->status = 'failed';
            $withdraw->note = 'Cancelled by user';
            $withdraw->save();
            Auth::user()->increment('points', $withdraw->amount);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Withdrawal request successfully cancelled!',
            'points' => Auth::user()->points,
        ], 200);
    }

    public function landing()
    {
        $is_online = Setting::where('name', 'video_display')->first()->value ?? false;
        return view('layouts.landing', compact('is_online'));
    }

    public function watchMovie()
    {
        return view('player.movie');
    }
}
