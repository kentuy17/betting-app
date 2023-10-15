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
        return view('operator.transactions');
    }

    public function getDepositTrans()
    {
        $trans = Transactions::where('action', 'deposit')
            ->whereIn('morph', [0, 2])
            ->with('user')
            ->with('operator')
            ->orderBy('created_at', 'desc')
            ->get();

        return DataTables::of($trans)
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

            $trans->status = $request->action == 'approve' ? 'completed' : 'failed';
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

    public function getWithdrawTrans()
    {
        $trans = Transactions::where('action', 'withdraw')
            ->with('user')
            ->with('operator')
            ->where('deleted', false)
            ->whereIn('morph', [0, 2])
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
            $trans = Transactions::find($request->id);
            $trans->status = $request->action == 'reject' ? 'failed' : 'completed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->note = $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if ($request->action == 'approve') {
                $operator = User::find(Auth::user()->id);
                $operator->points += $trans->amount;
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
        $auditor = ModelHasRoles::with('users')->has('auditor')->get()
            ->pluck('users')
            ->first();

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
}
