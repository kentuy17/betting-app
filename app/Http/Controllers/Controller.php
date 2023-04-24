<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\ModelHasRoles;
use App\Models\Roles;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getUserRole()
    {
        $modelRole = ModelHasRoles::where('model_id',Auth::user()->id)->first();
        $roles = Roles::where('id',$modelRole->role_id)->first();
        return $roles;
    }
}
