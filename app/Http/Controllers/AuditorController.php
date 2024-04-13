<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
// use \Illuminate\Contracts\Support\Carbon;
use App\Models\ModelHasRoles;
use App\Models\Transactions;
use App\Models\DerbyEvent;
use App\Models\Fight;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class AuditorController extends Controller
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

    public function transactions()
    {
        return view('auditor.transactions-operator');
    }

    public function getRefillTrans()
    {
        $trans = Transactions::where('action', 'refill')
            ->with('user')
            ->with('auditor')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $trans
        ]);
    }

    public function processRefill(Request $request)
    {
        try {
            $auditor = User::find(Auth::user()->id);
            if ($auditor->points <  $request->amount) {
                return response()->json([
                    'msg' => 'Insuficient points!',
                    'status' => 'error',
                ], 500);
            }

            $trans = Transactions::find($request->id);
            $trans->status = $request->action == 'approve' ? 'completed' : 'failed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->amount = $request->amount;
            $trans->note = $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if ($request->action == 'approve') {
                $operator = User::find($trans->user_id);
                $operator->points += $request->amount;
                $operator->save();

                $auditor->points -=  $request->amount;
                $auditor->save();
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
            'points' => $auditor->points,
        ], 200);
    }

    public function getRemitTrans()
    {
        $trans = Transactions::where('action', 'remit')
            ->with('user')
            ->with('auditor')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'data' => $trans
        ]);
    }

    public function processRemit(Request $request)
    {
        try {
            $trans = Transactions::find($request->id);
            $trans->status = $request->status == 'reject' ? 'failed' : 'completed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->note = $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if ($request->action == 'approve') {
                $operator = User::find($trans->user_id);
                $operator->points = 0;
                $operator->save();

                $auditor = User::find(Auth::user()->id);
                $auditor->points += $trans->amount;
                $auditor->save();
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
            'points' => $auditor->points,
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
            $event = DerbyEvent::create($request->all());
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

            $user = User::find(Auth::user()->id);
            if ($user->points < $request->amount) {
                return redirect()->back()
                    ->with('danger', 'Insuficient points!');
            }

            $user->points -=  $request->amount;
            $user->save();

            Transactions::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
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

    public function betSummary()
    {
        return view('auditor.bet-summary');
    }

    public function betSummaryEvent(Request $request)
    {
        $event = DerbyEvent::whereDate('created_at', $request->schedule_date)->get();
        return response()->json([
            'request' => $request,
            'data' => $event,
        ]);
    }

    public function getBetSummaryByDate(Request $request)
    {
        $start_of_event = Carbon::createFromFormat('Y-m-d', '2023-10-13');
        if ($request->event_date < $start_of_event) {
            return DataTables::of([])
                ->addIndexColumn()
                ->make(true);
        }

        $event = DerbyEvent::whereDate('schedule_date', $request->event_date)->get();

        $fights = Fight::whereIn('event_id', $event->pluck('id'))
            ->with('event:id,schedule_date')
            ->withSum('bet_legit_meron', 'amount')
            ->withSum('bet_legit_wala', 'amount')
            ->orderBy('fight_no', 'asc')
            ->get();

        $total_net = 0;
        foreach ($fights as $fight) {
            $sum_wala = $fight->bet_legit_wala_sum_amount ?? 0;
            $sum_meron = $fight->bet_legit_meron_sum_amount ?? 0;

            if ($fight->game_winner == 'W') {
                $income = $sum_meron - $sum_wala;
            } elseif ($fight->game_winner == 'M') {
                $income = $sum_wala - $sum_meron;
            } else {
                $income = 0;
            }

            $total_net += $income;
        }

        return DataTables::of($fights)
            ->addIndexColumn()
            ->addColumn('net', function ($fight) {
                if ($fight->game_winner == 'W') {
                    return $fight->bet_legit_meron_sum_amount - $fight->bet_legit_wala_sum_amount;
                } elseif ($fight->game_winner == 'M') {
                    return $fight->bet_legit_wala_sum_amount - $fight->bet_legit_meron_sum_amount;
                } else {
                    return 0;
                }
            })
            ->with('total_net', number_format($total_net, 2, '.', ','))
            ->make(true);
    }
}
