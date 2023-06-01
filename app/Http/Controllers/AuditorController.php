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
        $trans = Transactions::where('action','refill')
            ->with('user')
            ->with('auditor')
            ->orderBy('id','desc')
            ->get();
            
        return response()->json([
            'data' => $trans
        ]);
    }

    public function processRefill(Request $request)
    {
        try {
            $trans = Transactions::find($request->id);
            $trans->status = $request->action == 'approve' ? 'completed' : 'failed';
            $trans->processedBy = Auth::user()->id;
            $trans->reference_code = $request->ref_code;
            $trans->amount = $request->amount;
            $trans->note = $request->note;
            $trans->completed_at = date('Y-m-d H:i:s');
            $trans->save();

            if($request->action == 'approve') {
                $operator = User::find($trans->user_id);
                $operator->points +=  $request->amount;
                $operator->save();

                $auditor = User::find(Auth::user()->id);
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
        $trans = Transactions::where('action','remit')
            ->with('user')
            ->with('auditor')
            ->orderBy('id','desc')
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

            if($request->action == 'approve') {
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
             
            $imageName = time().'.'.$request->formFile->extension();
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
}
