<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use App\Models\Hacking;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getUserRole()
    {
        $modelRole = ModelHasRoles::where('model_id',Auth::user()->id)->first();
        $roles = Roles::where('id',$modelRole->role_id)->first();
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
}
