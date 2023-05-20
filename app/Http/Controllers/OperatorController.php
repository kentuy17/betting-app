<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Contracts\Support\Carbon;
use App\Models\ModelHasRoles;
use App\Models\Transactions;
use App\Models\DerbyEvent;
use App\Models\User;

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
        return view('operator.fight');
    }

    public function transactions()
    {
        return view('operator.transactions');
    }

    public function getDepositTrans()
    {
        $trans = Transactions::where('action','deposit')
            ->with('user')
            ->with('operator')
            ->orderBy('id','desc')
            ->get();
            
        return response()->json([
            'data' => $trans
        ]);
    }

    public function processDeposit(Request $request)
    {
        try {
            $trans = Transactions::find($request->id);
            $trans->status = $request->action == 'approve' ? 'completed' : 'failed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->amount = $request->amount;
            $trans->note = $request->note;
            $trans->completed_at = Carbon::now();
            $trans->save();

            if($request->action == 'approve') {
                $player = User::find($trans->user_id);
                $player->points +=  $request->amount;
                $player->save();

                $operator = User::find(Auth::user()->id);
                $operator->points +=  $request->amount;
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
        $trans = Transactions::where('action','withdraw')
            ->with('user')
            ->with('operator')
            ->orderBy('id','desc')
            ->get();
            
        return response()->json([
            'data' => $trans
        ]);
    }

    public function processWithdraw(Request $request)
    {
        try {
            $trans = Transactions::find($request->id);
            $trans->status = $request->status == 'reject' ? 'failed' : 'completed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->note = $request->note;
            $trans->completed_at = Carbon::now();
            $trans->save();

            if($request->action == 'approve') {
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
            'points' => $operator->points,
        ], 200);
    }

    public function getEvents()
    {
        $events = DerbyEvent::orderBy('id','desc')->get();
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
}
