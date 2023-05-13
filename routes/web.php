<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\FightController;

use App\Http\Middleware\EnsureUserIsPlayer;
use App\Htpp\Middleware\EnsureUserIsOperator;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     // return view('welcome');
//     return view('home');
// });

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');
  
Auth::routes();
  
Route::get('/home', [HomeController::class, 'index'])->name('home');
  
Route::group(['middleware' => ['auth']], function() {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', UserController::class);


    // Player
    Route::group(['middleware' => ['player']], function () {
        Route::get('/play', [PlayerController::class, 'index'])->name('play');
        Route::get('/play/history', [PlayerController::class, 'bethistory'])->name('player.bethistory');
        Route::get('/reports', [PlayerController::class, 'reports'])->name('player.reports');
        Route::post('/bet/add', [BetController::class, 'addBet']);
        Route::get('/user/profile', [UserController::class, 'profile'])->name('users.profile');
        Route::post('/user/profile', [UserController::class, 'editprofile']);
        Route::get('/deposit', [PlayerController::class, 'deposit'])->name('deposit');
        Route::post('/deposit', [PlayerController::class, 'depositSubmit'])->name('deposit.upload.post');
        Route::get('/withdrawform', [PlayerController::class, 'profileWithdraw'])->name('player.withdraw');  
        Route::post('/withdrawform', [PlayerController::class, 'submitWithdraw']);

        Route::get('/withdraw', [PlayerController::class, 'withdraw'])->name('withdraw');
        Route::post('/withdraw', [PlayerController::class, 'withdrawSubmit'])->name('withdraw.submit');
      
    });

    // Operator
    Route::group(['middleware' => ['operator']], function () {
        Route::post('/fight/update-status', [FightController::class, 'updateFight']);
        Route::get('/event', [OperatorController::class, 'eventList'])->name('operator.derby.event');
        Route::get('/event/lists', [OperatorController::class, 'getEvents']);
        Route::post('/event/create', [OperatorController::class, 'addNewEvent']);
        
        Route::get('/transaction/deposits', [OperatorController::class, 'getDepositTrans']);
        Route::get('/transaction/withdrawals', [OperatorController::class, 'getWithdrawTrans']);
        Route::post('/transaction/deposit', [OperatorController::class, 'processDeposit']);
        Route::post('/transaction/withdraw', [OperatorController::class, 'processWithdraw']);
    });

    Route::get('/fight', [OperatorController::class, 'fight'])->name('operator.fight');
    Route::get('/transactions', [OperatorController::class, 'transactions'])->name('operator.transactions');
    


    // Bets
    Route::get('/bet/total', [BetController::class, 'getTotalBetAmountPerFight']);
    Route::get('/bet/history', [BetController::class, 'getBetHistoryByUserController']);
    Route::get('/profile', [UserController::class, 'getProfileByUserID']);



    //Fight
    Route::get('/fight/current', [FightController::class, 'getCurrentFight']);
    Route::get('/fight/results', [FightController::class, 'fightResults']);

});
