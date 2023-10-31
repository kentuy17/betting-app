<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BetController;
use App\Http\Controllers\FightController;
use App\Http\Controllers\AuditorController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\GhostController;
use App\Http\Controllers\Auth\ResetPasswordController;

// use App\Http\Controllers\Controller;

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


Route::get('/', function () {
    return redirect('/login');
})->middleware('guest');

Auth::routes();
Route::get('/password_reset', [ResetPasswordController::class, 'showresetpasswordview'])->name('auth.dark-reset');
Route::post('/password_reset', [ResetPasswordController::class, 'submitresetpassword']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/admin/access/{id}', [AdminController::class, 'accessUser']);

Route::group(['middleware' => ['auth']], function () {
    // TEST
    Route::get('/dev/test', function () {
        $mac = exec('getmac');
        print_r($mac);
    });

    Route::get('/changepassword', [HomeController::class, 'showChangePasswordGet'])->name('auth.change-password');
    Route::post('/changepassword', [HomeController::class, 'changePasswordPost'])->name('changePasswordPost');

    Route::resource('roles', RoleController::class);
    // Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    // Route::resource('transactions', UserController::class);

    Route::get('/user/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/user/profile', [UserController::class, 'editprofile']);

    Route::group(['middleware' => ['bossing']], function () {
        Route::post('/commission/convert', [UserController::class, 'convertCommission']);
    });

    // Admin
    Route::group(['middleware' => ['admin']], function () {
        Route::get('/admin/share-allocation', [AdminController::class, 'shareHolders'])->name('admin.shares');
        Route::get('/visitor', [AdminController::class, 'getOnlineUsers']);
        Route::get('/admin/users', [AdminController::class, 'getUsers']);
        Route::get('/admin', [AdminController::class, 'index']);
        Route::post('/admin/users-create', [AdminController::class, 'createUser']);
        Route::get('/admin/user-permissions/{id}', [AdminController::class, 'getUserPagePermissions']);
        Route::post('/admin/user', [AdminController::class, 'updateUser']);
        Route::get('/admin/agents', [AdminController::class, 'getAgents'])->name('admin.agents');
        Route::get('/admin/agent-list', [AdminController::class, 'agentList']);
        Route::post('/admin/add-agent', [AdminController::class, 'addAgent']);
        Route::get('/admin/non-agents', [AdminController::class, 'getNotAgents']);

        // Corpo
        Route::get('/admin/incorpo', [AdminController::class, 'incorpo']);
        Route::get('/admin/incorpo-list', [AdminController::class, 'getCorpos']);
        Route::post('/admin/add-corpo-agent', [AdminController::class, 'addCorpoAgent']);
        Route::post('/admin/add-agents', [AdminController::class, 'addCorpSubAgents']);
        Route::get('admin/sub-agents/{id}', [AdminController::class, 'getSubAgentsByAgentId']);

        // budol2x
        Route::post('/admin/load-user', [AdminController::class, 'manualCashIn']);
    });

    // Player
    Route::group(['middleware' => ['player']], function () {
        Route::get('/play', [PlayerController::class, 'index'])->name('play');
        Route::get('/play/history', [PlayerController::class, 'bethistory'])->name('player.bethistory');
        Route::get('/reports', [PlayerController::class, 'reports'])->name('player.reports');
        Route::post('/bet/add', [BetController::class, 'addBet'])->name('player.bet');
        Route::get('/deposit', [PlayerController::class, 'deposit'])->name('deposit');
        Route::post('/deposit', [PlayerController::class, 'depositSubmit'])->name('deposit.upload.post');
        Route::get('/withdrawform', [PlayerController::class, 'profileWithdraw'])->name('player.withdraw');
        Route::post('/withdrawform', [PlayerController::class, 'submitWithdraw']);

        Route::get('/withdraw', [PlayerController::class, 'withdraw'])->name('withdraw');
        Route::post('/withdraw', [PlayerController::class, 'withdrawSubmit'])->name('withdraw.submit');
        Route::post('/withdraw/cancel', [PlayerController::class, 'cancelWithdraw'])->name('withdraw.cancel');

        Route::get('/playertransaction', [PlayerController::class, 'playerTransaction'])->name('player.player-transaction');
        Route::get('/player/transaction/{action}', [PlayerController::class, 'getTransactionByPlayerController']);

        Route::get('/chat/messages', [PlayerController::class, 'getUserMsg']);
        Route::post('/chat/send-message', [PlayerController::class, 'sendUserMsg']);
        Route::post('/chat/seen-message', [PlayerController::class, 'seenMessage']);
    });

    // Operator
    Route::group(['middleware' => ['operator']], function () {
        Route::post('/fight/update-status', [FightController::class, 'updateFight']);
        Route::post('/fight/revertresult', [FightController::class, 'revertResult']);
        Route::get('/event', [OperatorController::class, 'eventList'])->name('operator.derby.event');
        Route::get('/event/lists', [OperatorController::class, 'getEvents']);
        Route::post('/event/create', [OperatorController::class, 'addNewEvent']);
        Route::get('/fight', [OperatorController::class, 'fight'])->name('operator.fight');
        Route::post('/event/activate', [FightController::class, 'setGameEvent']);
    });

    Route::group(['middleware' => ['auditor']], function () {
        Route::get('/transaction/refill', [AuditorController::class, 'getRefillTrans']);
        Route::post('/transaction/refill', [AuditorController::class, 'processRefill']);
        Route::get('/transaction/remit', [AuditorController::class, 'getRemitTrans']);
        Route::post('/transaction/remit', [AuditorController::class, 'processRemit']);
        Route::get('/transactions-auditor', [AuditorController::class, 'transactions'])->name('auditor.transactions-operator');
        Route::get('/summary-bet', [AuditorController::class, 'betSummary'])->name('auditor.bet-summary');
        Route::get('/summary-bet/event', [AuditorController::class, 'betSummaryEvent']);
        Route::get('/summary-bet/filter-date', [AuditorController::class, 'getBetSummaryByDate']);
    });

    Route::group(['middleware' => ['csr']], function () {
        Route::get('/remitpoints', [OperatorController::class, 'remit'])->name('remit');
        Route::post('/remitpoints', [OperatorController::class, 'remitSubmit']);
        Route::get('/refillpoints', [OperatorController::class, 'refill'])->name('refill');
        Route::post('/refillpoints', [OperatorController::class, 'refillSubmit'])->name('refill.upload.post');
        Route::get('/requests', [OperatorController::class, 'viewRequests'])->name('requests');
        Route::get('/requests/data', [OperatorController::class, 'getRequests'])->name('requests.data');
        Route::get('/passwordreset-request/data', [OperatorController::class, 'getresetpassword']);
        Route::get('/passwordreset-request', [OperatorController::class, 'viewResetPassword'])->name('operator.password-reset');
        Route::post('/passwordreset-approve', [OperatorController::class, 'changePasswordApprove'])->name('changePasswordApprove');

        Route::get('/transaction/user-bet-history/{id}', [OperatorController::class, 'getBetHistoryByUserId']);
    });

    Route::group(['middleware' => ['auditor_csr']], function () {
        Route::get('/transactions', [OperatorController::class, 'transactions'])->name('operator.transactions');
        Route::get('/transaction/deposits', [OperatorController::class, 'getDepositTrans']);
        Route::post('/transaction/deposit/revert', [OperatorController::class, 'processDepositRevert']);

        Route::get('/transaction/withdrawals', [OperatorController::class, 'getWithdrawTrans']);
        Route::post('/transaction/deposit', [OperatorController::class, 'processDeposit']);
        Route::post('/transaction/withdraw', [OperatorController::class, 'processWithdraw']);

        Route::get('/player/bets/{id}', [PlayerController::class, 'getBetsByUserId']);
    });


    Route::group(['middleware' => ['agent']], function () {
        Route::post('/agent/commission-convert', [AgentController::class, 'commissionConvert']);
        Route::get('/agent/players', [AgentController::class, 'playersUnder'])->name('agent.players');
        Route::get('/agent/players-list', [AgentController::class, 'playerLists']);
        Route::get('/master-agent', [AgentController::class, 'masterAgent']);
        Route::get('/master-agent/points', [AgentController::class, 'getMasterAgentPoints']);
        Route::get('/master-agent/player-list', [AgentController::class, 'getPlayerList']);
        Route::post('/master-agent/topup', [AgentController::class, 'topUpPoints']);

        Route::view('/master-agent/{path?}', 'agents.master-agent')->where('path', '.*');
    });

    Route::group(['middleware' => ['ghost']], function () {
        Route::get('/ghost', [GhostController::class, 'index'])->name('ghost');
    });

    // Bets
    Route::get('/bet/total', [BetController::class, 'getTotalBetAmountPerFight']);
    Route::get('/bet/history', [BetController::class, 'getBetHistoryByUserController']);
    Route::get('/profile', [UserController::class, 'getProfileByUserID']);

    Route::post('/settings/video-display', [UserController::class, 'setVideoDisplay']);
    Route::get('/user/points', [PlayerController::class, 'getUserPoints']);

    //Fight
    Route::get('/fight/current', [FightController::class, 'getCurrentFight']);
    Route::get('/fight/results', [FightController::class, 'fightResults']);

    Route::get('/video', [PlayerController::class, 'video']);

    Route::get('/landing', [PlayerController::class, 'landing']);
    Route::get('/watch/movie', [PlayerController::class, 'watchMovie']);
});
