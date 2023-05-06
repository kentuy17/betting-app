<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use App\Models\User;
use App\Models\Transactions;
use \Illuminate\Support\Str;


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
        return view('player.play', compact('role'));
    }

    public function bethistory()
    {
        return view('player.bet-history');
    }

    public function deposit()
    {
        $user = Auth::user();
        return view('player.deposit', compact('user'));
    }

    public function depositSubmit(Request $request)
    {
     try{
            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required|numeric|between:100,50000'
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
    
            $user = User::find(Auth::user()->id);
            $trans = new Transactions;
            $trans['user_id'] = $user->id;
            $trans['amount'] = $request['amount'];
            $trans['action'] = 'withdraw';
            $trans['mobile_number'] = $request->phone_no;
            $trans['status'] = 'pending';
            $param = $trans->toArray();
            $trans->createTransaction($param);

            //$user->createTransaction($trans);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/deposit')->with('success', 'Updated Successfully!');
    }
}
