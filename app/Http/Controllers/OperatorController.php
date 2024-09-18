<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Transactions;
use App\Models\UserPasswordReset;
use App\Models\DerbyEvent;
use App\Models\User;
use App\Models\BetHistory;
use App\Models\Setting;
use App\Models\Referral;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class OperatorController extends Controller
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
        return view('home');
    }

    public function fight(): View
    {
        $role = $this->getUserRole();
        $fight = DerbyEvent::where('status', 'ACTIVE')->orderBy('id', 'desc')->first();
        $setting = Setting::where('name', 'video_display')->first()->value ?? false;
        return view('operator.fight', compact('role', 'fight', 'setting'));
    }

    public function transactions()
    {
        $kelso = User::find(666);
        $dummy = User::where('username', $kelso->name)->first();
        return view('operator.transactions', compact('dummy'));
    }

    public function getDepositTrans(Request $request)
    {
        $morp = Auth::user()->id == 1
            ? [1, 2, 0]
            // ? $request->morph
            : [0, 2];

        $start = $request->date_from ?? date('Y') . '01-01';
        $from = Carbon::parse($start)->toDateString();
        $to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();
        $trans = Transactions::where('action', 'deposit')
            ->whereIn('morph', $morp)
            ->whereIn('status', $request->status)
            ->with('user')
            ->with('operator')
            ->whereYear('created_at', date('Y'))
            ->whereBetween('created_at', [$from, $to->addDay()->toDateString()])
            ->orderBy('created_at', 'desc')
            ->get();


        return DataTables::of($trans)
            ->addIndexColumn()
            ->with('unpaid_count', $trans->where('status', 'completed')
                ->where('reference_code', NULL)
                ->count())
            ->make(true);
    }

    public function getTopupTrans()
    {
        $topup = Transactions::where('action', 'topup')
            ->whereIn('morph', [0, 2])
            ->with('user')
            ->with('operator')
            ->whereYear('created_at', date('Y'))
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($topup)
            ->addIndexColumn()
            ->make(true);
    }

    public function processDepositRevert(Request $request)
    {
        try {
            $trans = Transactions::find($request->id);
            $trans->processedBy = Auth::user()->id;
            $trans->amount = $request->amount;
            $trans->note = $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            $player = User::find($trans->user_id);
            $points = $player->points - $request->curr_amount;
            $player->points = $points + $request->amount;
            $player->save();

            $operator = User::find(Auth::user()->id);
            $points = $operator->points + $request->curr_amount;
            $operator->points = $points - $request->amount;
            $operator->save();
        } catch (\Exception $e) {
            return response()->json([
                'msg' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return redirect()->back()->with('success', 'Revert Points Request Successful!');
    }

    public function processDeposit(Request $request)
    {
        try {
            $operator = User::find(Auth::user()->id);
            if ($operator->points <  $request->amount) {
                return response()->json([
                    'msg' => 'Insuficient points!',
                    'status' => 'error',
                ], 400);
            }

            if ($request->action == 'approve' && $request->amount == "") {
                return response()->json([
                    'msg' => 'Please add points Mea!',
                    'status' => 'error',
                ], 400);
            }

            $check = Transactions::where('reference_code', $request->ref_code)->first();
            if ($check && $request->action == 'approve') {
                return response()->json([
                    'msg' => 'Double receipt!',
                    'status' => 'error',
                ], 400);
            }

            $trans = Transactions::find($request->id);

            if ($request->action == 'approve' && $trans->status == 'completed') {
                $approver = User::find($trans->processedBy);
                return response()->json([
                    'msg' => 'Oops! Request already approved by ' . $approver->username,
                    'status' => 'error',
                ], 400);
            }

            $status = 'completed';
            if ($request->action == 'reject') {
                $status = 'failed';
            }

            if ($request->action == 'update') {
                $status = $trans->status;
                $request->amount = $trans->amount;
            }

            $trans->status =  $status;
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->amount = $request->amount;
            $trans->note = $request->action == 'approve' ? 'DONE' : $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if ($request->action == 'approve') {
                $player = User::find($trans->user_id);
                $referral = Referral::where('user_id', $trans->user_id)
                    ->where('promo_done', false)
                    ->first();

                if ($referral && ($player->points < 100)) {
                    $referral->promo_done = true;
                    $referral->save();
                }

                $player->points +=  $trans->amount;
                $player->save();

                $operator->points -=  $trans->amount;
                $operator->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'msg' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'msg' => 'Success!',
            'status' => 'OK',
            'points' => $operator->points,
        ], 200);
    }

    public function processUtang(Request $request)
    {
        try {
            $user = User::find($request->id);
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => 'error',
                ], 400);
            }
            $utang_note = $request->action == 'utang' && $request->note == null ? 'Utang' : $request->note;
            $cashin = Transactions::create([
                'user_id' => $user->id,
                'action' => 'deposit',
                'mobile_number' => $user->phone_no,
                'status' => 'completed',
                'processedBy' => Auth::user()->id,
                'outlet' => 'Utang',
                'note' => $utang_note ?? 'PAID',
                'morph' => $request->morph ?? 0,
                'amount' => $request->amount,
                'reference_code' => $request->action != 'utang' ? $request->ref_code : null,
            ]);

            if ($cashin) {
                $user->points += $request->amount;
                $user->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'msg' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'msg' => 'Success!',
            'status' => 'OK',
            'points' => Auth::user()->points,
        ], 200);
    }

    public function getWithdrawTrans(Request $request)
    {
        $morp = Auth::user()->id == 1
            ? [1, 2, 0]
            // ? $request->morph
            : [0, 2];

        $start = $request->date_from ?? date('Y') . '01-01';
        $from = Carbon::parse($start)->toDateString();
        $to = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $trans = Transactions::where('action', 'withdraw')
            ->whereIn('morph', $morp)
            ->whereIn('status', $request->status)
            ->with('user')
            ->with('operator')
            ->where('deleted', false)
            ->whereBetween('created_at', [$from, $to->addDay()->toDateString()])
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($trans)
            ->addIndexColumn()
            ->with('pending_count', $trans->where('status', 'pending')->count())
            ->toJson();
    }

    public function processWithdraw(Request $request)
    {
        try {
            if ($request->action == 'reject' || $request->action == 'cancel') {
                $status = 'failed';
            } else {
                $status = 'completed';
            }

            $trans = Transactions::find($request->id);
            $trans->status = $status;
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->note = $request->action == 'reject' ? $request->note : 'DONE';
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if ($request->action == 'approve') {
                $operator = User::find(Auth::user()->id);
                $operator->points += $trans->amount;
                $operator->save();
            }

            if ($request->action == 'cancel') {
                $player = User::find($trans->user_id);
                $player->points += $trans->amount;
                $player->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'msg' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'msg' => 'Success!',
            'status' => 'OK',
            'points' => $operator->points ?? Auth::user()->points,
        ], 200);
    }

    public function getEvents()
    {
        $events = DerbyEvent::orderBy('id', 'desc')->get();
        return response()->json([
            'data' => $events
        ]);
    }

    public function eventList()
    {
        return view('operator.derby-events');
    }

    public function addNewEvent(Request $request)
    {
        try {
            $event = DerbyEvent::create([
                'name' => $request->name,
                'schedule_date' => $request->schedule_date,
                'schedule_time' => $request->schedule_time,
                'status' => 'WAITING',
                'added_by' => Auth::user()->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $event,
            'message' => 'OK'
        ]);
    }

    public function remit()
    {
        return view('operator.remit-points');
    }

    public function remitSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required',
            ]);

            $amount = str_replace(',', '', $request->amount);
            $user = User::find(Auth::user()->id);
            if ($user->points < $amount) {
                return redirect()->back()
                    ->with('danger', 'Insuficient points!');
            }

            $user->points -=  $amount;
            $user->save();

            Transactions::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'mobile_number' => $request->phone_no,
                'action' => 'remit',
                'status' => 'pending',
                'processedBy' => null,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Remit Points Request Successful!');
    }

    public function refill()
    {
        $user = Auth::user();
        // $auditor = ModelHasRoles::has('auditor')->get();
        // $auditors = ModelhasRoles::where('role_id', 5)->get();
        $auditor = User::find(1);
        // ->pluck('users')
        // ->first();

        // return dd($auditor);

        return view('operator.refill-points', compact('user', 'auditor'));
    }

    public function refillSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'formFile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imageName = time() . '.' . $request->formFile->extension();
            $path = 'public/' . $imageName;
            Storage::disk('local')->put($path, file_get_contents($request->formFile));

            Transactions::create([
                'user_id' => Auth::user()->id,
                'action' => 'refill',
                'mobile_number' => Auth::user()->phone_no,
                'filename' => $imageName,
                'status' => 'pending',
                'processedBy' => $request->auditor_id,
                'outlet' => 'Gcash'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Submitted Successfully!');
    }

    public function getRequests()
    {
        $trans = Transactions::with('user')
            ->where('user_id', Auth::user()->id)
            ->whereIn('action', ['remit', 'refill'])
            ->with('operator')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $trans,
            'status' => 'OK',
        ], 200);
    }

    public function viewRequests()
    {
        return view('operator.requests');
    }

    public function getresetpassword()
    {
        $trans = UserPasswordReset::with('user')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $trans,
            'status' => 'OK',
        ], 200);
    }

    public function viewResetPassword()
    {
        return view('operator.password-reset');
    }

    public function changePasswordApprove(Request $request)
    {
        //Change Passwor

        $upr = UserPasswordReset::find($request->id);
        $upr->status = 'completed';
        $upr->save();

        $trans = UserPasswordReset::with('user')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $trans,
            'status' => 'OK',
        ], 200);
    }

    public function getBetHistoryByUserId($id)
    {
        $history = BetHistory::where('user_id', $id)
            ->with('fight.event')
            ->orderBy('bethistory_no', 'desc')
            ->get();

        return DataTables::of($history)
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make(true);
    }

    public function updateModeOfPayment(Request $request)
    {
        $mop = User::find(104);
        $mop->phone_no = $request->change_mop;

        switch ($request->change_mop) {
                // case '09563559858':
                //     $mop->name = 'ME****L EM*******E G.';
                //     break;
                // case '09364969298':
                //     $mop->name = 'JE*O AN****O A.';
                //     break;
            case '09163377896':
                $mop->name = 'KE****H C.';
                break;
                // case '09364544325':
                //     $mop->name = 'CH*****N C.';
                //     break;
            case '09272306987':
                $mop->name = 'KY*E B.';
                break;
            default:
                $mop->name = 'KE****H C.';
                break;
        }

        $mop->save();

        return redirect()
            ->back()
            ->with('success', 'MOP updated succesfully!');
    }
}
