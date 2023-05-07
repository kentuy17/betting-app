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
use Illuminate\Support\Lottery;

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
        // $operators = ModelHasRoles::where('role_id',3)->with('users')->first();
        // $operators = ModelHasRoles::with('roles')
        //     ->where('role_id',3)
        //     ->first();
        $operators = ModelHasRoles::with('users')
            ->where('role_id',3)
            ->inRandomOrder()
            ->first();

        return view('player.deposit', compact('user', 'operators'));
    }

    public function depositSubmit(Request $request)
    {
        try {
            $this->validate($request, [
                'phone_no' => 'required',
                'amount' => 'required|numeric|between:100,50000'
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
    
            Transactions::create([
                'user_id' => Auth::user()->id,
                'amount' => $request->amount,
                'action' => 'deposit',
                'mobile_number' => $request->phone_number,
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Updated Successfully!');
    }
}
