<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use App\Models\Hacking;
use App\Models\User;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getUserRole()
    {
        $roles = Auth::user()->user_roles();
        if(!$roles) {
            $modelRole = ModelHasRoles::where('model_id',Auth::user()->id)->first();
            $roles = Roles::where('id',$modelRole->role_id)->first();
            $user = User::find(Auth::user()->id);
            $user->role_id = $roles->id;
            $user->save();
        }

        session(['role' => $roles->name]);
        return $roles;
    }

    public function hacking(Request $request, $violation)
    {
        Hacking::create([
            'user_id' => Auth::user()->id,
            'request' => json_encode($request->all()),
            'violation' => $violation
        ]);
    }

    public function logger($message, $note='')
    {
        Log::channel('custom')->info(json_encode([$note => $message], JSON_PRETTY_PRINT));
    }
}
