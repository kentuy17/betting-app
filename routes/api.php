<?php

use App\Http\Controllers\Api\BotController;
use App\Http\Controllers\Api\SecretController;
use App\Http\Controllers\FightController;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
  // Auth
  Route::get('/tokens/user', [SecretController::class, 'getUserTokens']);
  Route::get('/tokens/create/{id?}', [SecretController::class, 'issueToken']);

  // Fight
  Route::get('/fight/current', [FightController::class, 'getCurrentFight']);
  Route::post('/fight/update', [BotController::class, 'updateFight']);
  Route::post('/fight/close', [BotController::class, 'closeFight']);

  Route::post('/bet/add', [BotController::class, 'addBet']);
  Route::post('/bet/cache', [BotController::class, 'addBetCached']);

  Route::get('/encrypt/data', [SecretController::class, 'encryptData']);
});

        // $user = $id ? User::find($id) : Auth::user();
        // $token = $user->createToken('operator');
        // return response()->json([
        //     'token' => $token->plainTextToken
        // ], 200);
