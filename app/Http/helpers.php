<?php
use App\Models\User;

    function hasAccess($permission) {
        $permissions = Auth::user()->_user_permissions()->all();
        if(in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }

    function userHasAccess($user_id, $permission) {
        $user = User::find($user_id);
        $permissions = $user->_user_permissions()->all();
        if(in_array($permission, $permissions)) {
            return true;
        }
        return false;
    }

?>
