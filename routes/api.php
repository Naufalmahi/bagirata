<?php

use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\DebtController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\GroupInviteController;
use App\Http\Controllers\Api\NongkrongSessionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — BagiRata v1
|--------------------------------------------------------------------------
|
| Semua endpoint integer rupiah & dikalkulasi server-side. Frontend cuma
| konsumen API (token Sanctum / cookie Sanctum SPA).
|
*/

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());

    Route::get('/users', [UserController::class, 'search']);

    // Group + invite + role + channel
    Route::post('/groups/join/{token}', [GroupInviteController::class, 'join']);

    Route::apiResource('groups', GroupController::class)
        ->only(['index', 'store', 'show', 'update', 'destroy']);

    Route::get('/groups/{group}/invite', [GroupController::class, 'getInvite']);
    Route::post('/groups/{group}/invites', [GroupController::class, 'generateInvite']);
    Route::post('/groups/{group}/invites/{invite}/revoke', [GroupController::class, 'revokeInvite']);

    Route::get('/groups/{group}/roles', [GroupController::class, 'roles']);
    Route::post('/groups/{group}/roles', [GroupController::class, 'storeRole']);
    Route::patch('/groups/{group}/roles/{role}', [GroupController::class, 'updateRole']);
    Route::delete('/groups/{group}/roles/{role}', [GroupController::class, 'destroyRole']);

    Route::patch('/groups/{group}/members/{user}/role', [GroupController::class, 'assignRole']);

    Route::get('/groups/{group}/channels', [ChannelController::class, 'index']);
    Route::post('/groups/{group}/channels', [ChannelController::class, 'store']);
    Route::get('/groups/{group}/channels/{channel}', [ChannelController::class, 'show']);
    Route::patch('/groups/{group}/channels/{channel}', [ChannelController::class, 'update']);
    Route::delete('/groups/{group}/channels/{channel}', [ChannelController::class, 'destroy']);

    // Nongkrong (patungan)
    Route::get('/sessions', [NongkrongSessionController::class, 'index']);
    Route::post('/sessions', [NongkrongSessionController::class, 'store']);
    Route::get('/sessions/{session}', [NongkrongSessionController::class, 'show']);
    Route::patch('/sessions/{session}', [NongkrongSessionController::class, 'update']);
    Route::delete('/sessions/{session}', [NongkrongSessionController::class, 'destroy']);

    Route::post('/sessions/{session}/split-preview', [NongkrongSessionController::class, 'splitPreview']);

    // Expense
    Route::get('/sessions/{session}/expenses', [ExpenseController::class, 'index']);
    Route::post('/sessions/{session}/expenses', [ExpenseController::class, 'store']);
    Route::get('/sessions/{session}/expenses/{expense}', [ExpenseController::class, 'show']);
    Route::patch('/sessions/{session}/expenses/{expense}', [ExpenseController::class, 'update']);
    Route::delete('/sessions/{session}/expenses/{expense}', [ExpenseController::class, 'destroy']);

    // Debt
    Route::get('/sessions/{session}/debts', [DebtController::class, 'index']);
    Route::get('/sessions/{session}/debts/{debt}', [DebtController::class, 'show']);
    Route::post('/sessions/{session}/debts/{debt}/payments', [DebtController::class, 'reportPayment']);
    Route::post('/sessions/{session}/debts/{debt}/payments/{payment}/confirm', [DebtController::class, 'confirmPayment']);
    Route::post('/sessions/{session}/debts/{debt}/payments/{payment}/reject', [DebtController::class, 'rejectPayment']);
    Route::post('/sessions/{session}/debts/{debt}/settle', [DebtController::class, 'settle']);

    Route::post('/calculate-split', [\App\Http\Controllers\Api\QuickCalculatorController::class, 'calculate']);
});
