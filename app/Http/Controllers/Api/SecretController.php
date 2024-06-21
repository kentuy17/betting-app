<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class SecretController extends Controller
{
    //
    public function __construct()
    {
        // code here
    }

    public function encryptData(Request $request)
    {
        $encrypted = [
            'iyot' => [
                'bilat', $request->text
            ]
        ];

        return Crypt::encryptString(json_encode($encrypted));
    }
}
