<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DebtController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\GroupController;
use App\Http\Controllers\Web\JoinController;
use App\Http\Controllers\Web\NongkrongController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes di sini buat halaman SSR (Blade). Semua mutasi lewat form biasa
| pake session auth; interaksi dinamis via Alpine + endpoint API yang sama.
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('home');

Route::get('/join/{token}', [JoinController::class, 'show'])->name('join.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::post('/join/{token}', [JoinController::class, 'store'])->name('join.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==== Groups ====
    Route::resource('groups', GroupController::class)->except(['edit']);

    Route::get('/groups/{group}/manage', [GroupController::class, 'manage'])->name('groups.manage');

    Route::post('/groups/{group}/invite', [GroupController::class, 'generateInvite'])->name('groups.invite.generate');
    Route::post('/groups/{group}/invite/{invite}/revoke', [GroupController::class, 'revokeInvite'])->name('groups.invite.revoke');

    Route::post('/groups/{group}/channels', [GroupController::class, 'storeChannel'])->name('groups.channels.store');
    Route::patch('/groups/{group}/channels/{channel}', [GroupController::class, 'updateChannel'])->name('groups.channels.update');
    Route::delete('/groups/{group}/channels/{channel}', [GroupController::class, 'destroyChannel'])->name('groups.channels.destroy');

    Route::post('/groups/{group}/roles', [GroupController::class, 'storeRole'])->name('groups.roles.store');
    Route::patch('/groups/{group}/roles/{role}', [GroupController::class, 'updateRole'])->name('groups.roles.update');
    Route::delete('/groups/{group}/roles/{role}', [GroupController::class, 'destroyRole'])->name('groups.roles.destroy');
    Route::post('/groups/{group}/members/{member}/role', [GroupController::class, 'assignRole'])->name('groups.members.role');

    // ==== Nongkrong (patungan) ====
    Route::get('/nongkrong', [NongkrongController::class, 'index'])->name('nongkrong.index');
    Route::get('/nongkrong/create', [NongkrongController::class, 'create'])->name('nongkrong.create');
    Route::post('/nongkrong', [NongkrongController::class, 'store'])->name('nongkrong.store');
    Route::get('/nongkrong/{session}', [NongkrongController::class, 'show'])->name('nongkrong.show');

    // ==== Expenses ====
    Route::get('/nongkrong/{session}/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::get('/nongkrong/{session}/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::post('/nongkrong/{session}/expenses/preview', [ExpenseController::class, 'preview'])->name('expenses.preview');
    Route::post('/nongkrong/{session}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::patch('/nongkrong/{session}/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/nongkrong/{session}/expenses/{expense}', [ExpenseController::class, 'cancel'])->name('expenses.cancel');

    // ==== Debts ====
<<<<<<< HEAD
    Route::get('/debts', [DebtController::class, 'overview'])->name('debts.overview');
    Route::get('/nongkrong/{session}/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::get('/nongkrong/{session}/debts/{debt}', [DebtController::class, 'show'])->name('debts.show');
    Route::post('/nongkrong/{session}/debts/{debt}/payments', [DebtController::class, 'reportPayment'])->name('debts.payments.store');
    Route::post('/nongkrong/{session}/debts/{debt}/payments/{payment}/confirm', [DebtController::class, 'confirmPayment'])->name('debts.payments.confirm');
    Route::post('/nongkrong/{session}/debts/{debt}/payments/{payment}/reject', [DebtController::class, 'rejectPayment'])->name('debts.payments.reject');
=======
    Route::post('/nongkrong/{session}/debts/{debt}/settle', [DebtController::class, 'settle'])->name('debts.settle');
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
});
