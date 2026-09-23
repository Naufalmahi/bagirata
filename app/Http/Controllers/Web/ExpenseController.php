<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Nongkrong\SplitPreviewRequest;
use App\Http\Requests\Nongkrong\StoreExpenseRequest;
use App\Http\Requests\Nongkrong\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\NongkrongSession;
use App\Services\CalculationService;
use App\Services\ExpenseService;
use App\Services\SplitService;
use App\Services\Support\Money;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create(Request $request, NongkrongSession $session)
    {
        $this->authorize('addExpense', $session);

        $session->load('members');

        return view('expenses.create', compact('session'));
    }

    public function store(StoreExpenseRequest $request, NongkrongSession $session)
    {
        $data = $request->validated();
        $receiptPath = $this->storeReceipt($request);

        ExpenseService::create($request->user(), $session, $data, $receiptPath);

        return redirect()->route('nongkrong.show', $session)
            ->with('success', 'Pengeluaran tercatat, bagian masing-masing udah dihitungin.');
    }

    public function edit(Request $request, NongkrongSession $session, Expense $expense)
    {
        $this->authorize('update', $expense);

        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $session->load('members');
        $expense->load('participants', 'splits');

        return view('expenses.edit', compact('session', 'expense'));
    }

    public function update(UpdateExpenseRequest $request, NongkrongSession $session, Expense $expense)
    {
        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        $data = $request->validated();
        $receiptPath = $this->storeReceipt($request);

        ExpenseService::update($request->user(), $expense, $data, $receiptPath);

        return redirect()->route('nongkrong.show', $session)
            ->with('success', 'Pengeluaran ke-update, debt langsung disesuaikan.');
    }

    public function cancel(Request $request, NongkrongSession $session, Expense $expense)
    {
        $this->authorize('delete', $expense);

        if ($expense->nongkrong_session_id !== $session->id) {
            abort(404);
        }

        ExpenseService::cancel($request->user(), $expense);

        return back()->with('success', 'Pengeluaran dibatalin, riwayatnya tetap kesimpen.');
    }

    /**
     * Preview split server-side (Alpine -> JSON) biar frontend nggak pernah ngitung sendiri.
     */
    public function preview(SplitPreviewRequest $request, NongkrongSession $session)
    {
        $input = $request->validated();

        $grandTotal = CalculationService::grandTotal(
            (int) $input['amount'],
            $input['discount_type'],
            (int) $input['discount_value'],
            (int) ($input['service_rate'] ?? 0),
            (int) ($input['tax_rate'] ?? 0)
        );

        $participantIds = array_values(array_unique($input['participant_ids']));
        $splitInput = $input['split_type'] === 'custom'
            ? ($input['custom_amounts'] ?? [])
            : ($input['percentages'] ?? []);

        $shares = SplitService::split($input['split_type'], $grandTotal, $participantIds, $splitInput);

        $base = CalculationService::baseAfterDiscount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']);
        $service = CalculationService::serviceAmount($base, (int) ($input['service_rate'] ?? 0));

        $users = \App\Models\User::whereKey($participantIds)->get()->keyBy('id');

        return ApiResponse::success([
            'grand_total' => $grandTotal,
            'grand_total_formatted' => Money::format($grandTotal),
            'discount_amount' => CalculationService::discountAmount((int) $input['amount'], $input['discount_type'], (int) $input['discount_value']),
            'service_amount' => $service,
            'tax_amount' => CalculationService::taxAmount($base, $service, (int) ($input['tax_rate'] ?? 0)),
            'splits' => collect($participantIds)->map(fn ($id) => [
                'user_id' => $id,
                'name' => $users->get($id)?->name,
                'share' => $shares[$id],
                'share_formatted' => Money::format($shares[$id]),
            ]),
        ], '');
    }

    private function storeReceipt(Request $request): ?string
    {
        if (! $request->hasFile('receipt_photo')) {
            return null;
        }

        return $request->file('receipt_photo')->store('receipts', 'public');
    }
}
