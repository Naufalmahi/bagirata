<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nongkrong\StoreExpenseRequest;
use App\Http\Requests\Nongkrong\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Models\NongkrongSession;
use App\Services\ExpenseService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request, NongkrongSession $session)
    {
        $this->authorize('view', $session);

        $expenses = $session->expenses()
            ->with(['payer', 'creator', 'splits.user'])
            ->latest()
            ->get();

        return ApiResponse::success(ExpenseResource::collection($expenses), '');
    }

    public function store(StoreExpenseRequest $request, NongkrongSession $session)
    {
        $data = $request->validated();
        $receiptPath = $this->storeReceipt($request);

        $expense = ExpenseService::create($request->user(), $session, $data, $receiptPath);
        $expense->load(['payer', 'creator', 'splits.user', 'session']);

        return ApiResponse::created(
            new ExpenseResource($expense),
            'Pengeluaran tercatat, bagian masing-masing udah dihitungin.'
        );
    }

    public function show(Request $request, NongkrongSession $session, Expense $expense)
    {
        $this->authorize('view', $expense);

        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $expense->load(['payer', 'creator', 'splits.user', 'session']);

        return ApiResponse::success(new ExpenseResource($expense), '');
    }

    public function update(UpdateExpenseRequest $request, NongkrongSession $session, Expense $expense)
    {
        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $data = $request->validated();
        $receiptPath = $this->storeReceipt($request);

        $expense = ExpenseService::update($request->user(), $expense, $data, $receiptPath);
        $expense->load(['payer', 'creator', 'splits.user', 'session']);

        return ApiResponse::success(
            new ExpenseResource($expense),
            'Pengeluaran ke-update, debt langsung disesuaikan.'
        );
    }

    public function destroy(Request $request, NongkrongSession $session, Expense $expense)
    {
        $this->authorize('delete', $expense);

        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        ExpenseService::cancel($request->user(), $expense);

        return ApiResponse::noContent('Pengeluaran dibatalin, riwayatnya tetap kesimpen.');
    }

    private function storeReceipt(Request $request): ?string
    {
        if (! $request->hasFile('receipt_photo')) {
            return null;
        }

        return $request->file('receipt_photo')->store('receipts', 'public');
    }
}
