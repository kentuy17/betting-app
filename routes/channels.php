<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Transactions;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('user.{id}', function ($user, $betUserId) {
    return Auth::check() && $user->id == $betUserId;
});

Broadcast::channel('fight', function () {
    return true;
});

Broadcast::channel('bet', function ($user) {
    return true;
});

Broadcast::channel('cashin.{processedBy}', function ($user, $processedBy) {
    $trans = Transactions::where('processedBy', $processedBy)
        ->where('action', 'deposit')
        ->where('status', 'pending')
        ->orderBy('id', 'desc')
        ->first();
    if ($trans) {
        return $user->id == $trans->processedBy;
    }
    return false;
});

Broadcast::channel('secured-bet', function ($user) {
    return Auth::check() && (int)$user->id == Auth::id();
});
