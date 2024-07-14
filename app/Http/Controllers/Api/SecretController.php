<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BetController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redis;
use App\Models\BetHistory;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class SecretController extends Controller
{
    //
    public function __construct(BetController $betController)
    {
        // code here
        $this->betController = $betController;
    }

    public function encryptData(Request $request)
    {
        $encrypted = [
            'iyot' => ['bilat', $request->text],
        ];

        return Crypt::encryptString(json_encode($encrypted));
    }

    public function testPersonal($fight_no): void
    {
        $meron = Redis::get('M') + Redis::get('extra:M');
        $wala = Redis::get('W') + Redis::get('extra:W');
        $secret_fan = User::find(666);
        $maloi_nag_iisa = $meron < $wala ? 'M' : 'W';
        $pusta = $secret_fan->points;

        if ($meron == 0 || $wala == 0)
            throw new Exception('Misdeal');

        $kiyod = BetHistory::where('user_id', 666)
            ->orderBy('bethistory_no', 'desc')
            ->first();

        if ($kiyod->status === 'L') {
            $maloi_nag_iisa = $meron < $wala ? 'M' : 'W';
            $pusta = intval($kiyod->betamount * 2.5) > $secret_fan->points
                ? intval($secret_fan->points)
                : $kiyod->betamount * 2.5;
        }

        if ($kiyod->status === 'W') {
            $pusta =
                $secret_fan->points / 15 >= 10
                ? $secret_fan->points / 15
                : $secret_fan->points;
        }

        if (in_array($kiyod->status, ['C', 'D'])) {
            $pusta = $kiyod->betamount;
        }

        if ($secret_fan->points == 0) {
            $secret_fan->update(['points' => 1000]);
            $pusta = 1000 / 15;
        }

        $bet_request = new Request([
            'side' => $maloi_nag_iisa,
            'fight_no' => (int) $fight_no,
            'amount' => intval($pusta),
            'user_id' => $secret_fan->id,
        ]);

        $bet = $this->betController->addBet($bet_request);
        $log = $bet_request->all();
        $log['bet'] = $bet;

        if ($bet) {
            $secret_fan->points -= $pusta;
            $secret_fan->save();
        }

        \Log::channel('autobet')->debug(json_encode($log, JSON_PRETTY_PRINT));
    }

    public function issueToken($id = null)
    {
        $user = $id ? User::find($id) : Auth::user();
        $token = $user->createToken('operator');
        return response()->json([
            'token' => $token->plainTextToken,
        ], 200);
    }

    public function getUserTokens(Request $request)
    {
        return $request->user()->tokens;
    }
}
