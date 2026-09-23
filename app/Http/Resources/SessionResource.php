<?php

namespace App\Http\Resources;

use App\Enums\DebtStatus;
use App\Models\NongkrongSession;
use App\Services\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    protected bool $withSplit = false;

    protected ?int $viewerId = null;

    public function withSplit(bool $value = true): static
    {
        $this->withSplit = $value;

        return $this;
    }

    public function viewer(int $userId): static
    {
        $this->viewerId = $userId;

        return $this;
    }

    public function toArray(Request $request): array
    {
        $session = $this->resource;

        $summary = match (true) {
            $this->withSplit => $this->splitSummary($session),
            default => $this->basicSummary($session),
        };

        return [
            'id' => $session->id,
            'name' => $session->name,
            'description' => $session->description,
            'date' => $session->date?->format('Y-m-d'),
            'status' => $session->status(),
            'status_label' => $session->statusLabel(),
            'creator' => new UserResource($session->creator),
            'group_id' => $session->group_id,
            'channel_id' => $session->channel_id,
            'members' => UserResource::collection($session->members),
            'expenses' => ExpenseResource::collection($session->expenses),
            'debts' => DebtResource::collection($session->debts),
            'summary' => $summary,
            'created_at' => $session->created_at?->toIso8601String(),
        ];
    }

    private function basicSummary(NongkrongSession $session): array
    {
        $allExpenses = $session->expenses;
<<<<<<< HEAD
        $activeDebtTotal = $this->activeDebtTotal($session);
=======
        $debts = $session->debts;
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a

        return [
            'total_expense_count' => $allExpenses->count(),
            'total_spent' => $allExpenses->sum(fn ($e) => $e->grandTotal()),
            'total_spent_formatted' => Money::format($allExpenses->sum(fn ($e) => $e->grandTotal())),
<<<<<<< HEAD
            'pending_debt_total' => $activeDebtTotal,
            'pending_debt_total_formatted' => Money::format($activeDebtTotal),
=======
            'pending_debt_total' => $debts->where('status', DebtStatus::PENDING->value)->sum('amount'),
            'pending_debt_total_formatted' => Money::format($debts->where('status', DebtStatus::PENDING->value)->sum('amount')),
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
        ];
    }

    private function splitSummary(NongkrongSession $session): array
    {
        $balances = \App\Services\DebtService::netBalances($session);
        $viewerBalance = $balances[$this->viewerId] ?? 0;
<<<<<<< HEAD
        $activeDebtTotal = $this->activeDebtTotal($session);
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a

        return [
            'total_spent' => $session->expenses->sum(fn ($e) => $e->grandTotal()),
            'total_spent_formatted' => Money::format($session->expenses->sum(fn ($e) => $e->grandTotal())),
<<<<<<< HEAD
            'total_pending' => $activeDebtTotal,
            'total_pending_formatted' => Money::format($activeDebtTotal),
=======
            'total_pending' => $session->debts->where('status', DebtStatus::PENDING->value)->sum('amount'),
            'total_pending_formatted' => Money::format($session->debts->where('status', DebtStatus::PENDING->value)->sum('amount')),
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
            'viewer_balance' => $viewerBalance,
            'viewer_balance_formatted' => Money::format(abs($viewerBalance)),
            'viewer_role' => $viewerBalance > 0 ? 'creditor' : ($viewerBalance < 0 ? 'debtor' : 'neutral'),
        ];
    }
<<<<<<< HEAD

    private function activeDebtTotal(NongkrongSession $session): int
    {
        return (int) $session->debts
            ->where('status', '!=', DebtStatus::SETTLED->value)
            ->sum(fn ($debt) => $debt->outstanding());
    }
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
}
