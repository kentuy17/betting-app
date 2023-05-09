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
use Illuminate\Support\Facades\Storage;

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
                'mobile_number' => $request->phone_number,
                'filename' => $imageName,
                'status' => 'pending',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('danger', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Submitted Successfully!');
    }
}
