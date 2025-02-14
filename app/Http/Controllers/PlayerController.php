<?php

namespace App\Http\Controllers;

use App\Models\DerbyEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transactions;
use App\Models\BetHistory;
use App\Models\Referral;
use App\Models\Promo;
use App\Models\IpBan;
use App\Models\Chat;
use App\Models\Setting;
use App\Events\CashIn;
use App\Models\Agent;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class PlayerController extends Controller
{
    private $tawas;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // $this->middleware('maintenance');
        $this->tawas = config('app.tawas');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // if (Auth::user() && Auth::user()->id !== 1) {
        //     Auth::logout();
        //     return redirect()
        //         ->route('login')
        //         ->with('error', 'System maintenance');
        // }

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
        // $active_operators = ModelHasRoles::with('users')
        //     ->has('active_operators')->get()
        //     ->pluck('users')
        //     ->sortBy('points')
        //     ->first();

        // $low_pts = ModelHasRoles::with('users')
        //     ->has('operators')->get()
        //     ->pluck('users')
        //     ->sortBy('points')
        //     ->first();

        // $operators = $active_operators ?? $low_pts;
        // dd($operators);

        $operators = User::find(104);

        if (in_array(Auth::user()->id, [9, 10, 92908, 93043, 92961, 20]) && $this->tawas) {
            $operators->name = 'KY*E B.';
            $operators->phone_no = '09272306987';
        }

        return view('player.deposit', compact('user', 'operators'));
    }

    public function getTransactionByPlayerController($action = true)
    {
        $katok = session('katok');
        $morph =  $katok ? [0] : [0, 1];
        $actions = [$action];
        if ($action == 'deposit') {
            array_push($actions, 'topup');
        }

        $trans = Transactions::where('user_id', Auth::user()->id)
            ->with('user')
            ->with('operator')
            ->orderBy('id', 'desc')
            ->whereIn('action', $actions)
            ->where('deleted', false)
            ->whereIn('morph', $morph)
            ->get();

        return DataTables::of($trans)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->with('katok', $katok)
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

    public function sendGotify($deposit = null)
    {
        try {
            $curl = curl_init();
            $player = $deposit->action . '::' . $deposit->user->username . '::' . $deposit->amount;
            $recibo = "https://isp24.live/storage/" . $deposit->filename;
            $iyak = "https://i.pinimg.com/736x/6d/26/a2/6d26a28e9269843a0103da816b83457f.jpg";
            $img = $deposit->action == 'deposit' ? $recibo : $iyak;
            $tawas = config('app.tawas');
            $token = $tawas ? 'AfWjgyG3TWWytfc' : 'AIo5fLAiBXEcMkm';

            curl_setopt_array($curl, array(
                // CURLOPT_URL => 'http://192.46.230.32:8080/message?token=AIo5fLAiBXEcMkm', // straight
                CURLOPT_URL => 'http://192.46.230.32:8080/message?token=' . $token, // tawas
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                "extras": {
                "client::display": {
                "contentType": "text/markdown"
                }
            },
            "message": "![](' . $img . ')",
            "priority": 10,
            "title": "' . $player . '"
            }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            // echo $response;

        } catch (\Throwable $th) {
            \Log::info("CI: " . json_encode($th->getMessage()));
        }

        \Log::info("CI: " . json_encode($response));
        return response()->noContent();
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
                'morph' => $this->tawas ? 1 : 0,
            ]);

            $active = Setting::find(1);
            $kapar = User::find($request->operator_id);
            if ($active->value == 0) {
                $this->hacking($request, 'Huli: ' . $kapar->name);
            }

            $this->sendGotify($trans);
            event(new CashIn($trans));
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        // promo
        // session()->flash('warning', 'Note: Minimum turnover should be 5x the cashin amount.');

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

            // promo rebate

            $referral = Referral::where('user_id', Auth::user()->id)->first();
            $promo = Promo::where('user_id', Auth::user()->id)->first();

            if ($promo && !$referral->promo_done) {
                if ($amount < 1500) {
                    return redirect()->back()
                        ->with('danger', "For bonus credit only, you need to win 1500 to cash out " . $request->amount);
                } else {
                    $referral->promo_done = true;
                    $referral->save();

                    IpBan::create(['ip_address' => $user->email]);
                }
            }

            $user->points -= $amount;
            $user->save();

            $trans = Transactions::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'mobile_number' => $request->phone_no,
                'action' => 'withdraw',
                'status' => 'pending',
                'processedBy' => null,
            ]);

            if ($trans) {
                event(new CashIn($trans));
                $this->sendGotify($trans);
            }
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
        // if (Auth::user()->id === 1) {
        //     $co = Transactions::find(22911);
        //     $this->sendGotify($co);
        // }

        $is_online = Setting::where('name', 'video_display')->first()->value ?? false;
        $agent = Agent::with('referral')->where('user_id', Auth::user()->id)->first();
        $master_agent = false;
        $permissions = Auth::user()->_user_permissions()->toArray();
        $mop = User::find(104)->phone_no;

        $mops = array();
        // 'KE****H C.'
        // '09163377896'
        // $tawas_name = (config('app.tawas') && !in_array(Auth::user()->id, [9, 20]))
        //     ? 'CH*****N C.' : 'KY*E B.';
        // $tawas_num = (config('app.tawas') && !in_array(Auth::user()->id, [9, 20]))
        //     ? '09364544325' : '09272306987';

        if ($this->tawas && in_array(Auth::user()->id, [9, 20])) {
            $mop = '09272306987';
        }

        $mops = collect([
            (object)[
                'name' => 'KE****H C.',
                'number' => '09163377896'
            ],
            // (object)[
            //     'name' => 'CH*****N C.',
            //     'number' => '09364544325',
            // ],
            (object)[
                'name' => 'KY*E B.',
                'number' => '09272306987'
            ],
        ]);

        if ($agent) {
            $master_agent = $agent->is_master_agent;
        }

        return view('layouts.landing', compact('is_online', 'master_agent', 'permissions', 'mop', 'mops'));
    }

    public function watchMovie()
    {
        return view('player.movie');
    }
}
