<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\NongkrongSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * @param  array{
     *   name: string, amount: int, paid_by_user_id: int, category: string,
     *   note?: ?string, discount_type: string, discount_value: int,
     *   service_rate?: int, tax_rate?: int, split_type: string,
     *   participant_ids: array<int>, custom_amounts?: array<int>, percentages?: array<int>
     * }  $data
     */
    public static function create(User $user, NongkrongSession $session, array $data, ?string $receiptPath = null): Expense
    {
        return DB::transaction(function () use ($user, $session, $data, $receiptPath) {
            NongkrongSession::whereKey($session->id)->lockForUpdate()->first();

            $grandTotal = CalculationService::grandTotal(
                $data['amount'],
                $data['discount_type'],
                $data['discount_value'],
                $data['service_rate'] ?? 0,
                $data['tax_rate'] ?? 0
            );

            $shares = SplitService::split(
                $data['split_type'],
                $grandTotal,
                array_values(array_unique($data['participant_ids'])),
                $data['split_type'] === 'custom' ? ($data['custom_amounts'] ?? []) : ($data['percentages'] ?? [])
            );

            $expense = $session->expenses()->create([
                'name' => $data['name'],
                'amount' => $data['amount'],
                'paid_by_user_id' => $data['paid_by_user_id'],
                'category' => $data['category'],
                'note' => $data['note'] ?? null,
                'receipt_photo' => $receiptPath,
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'service_rate' => $data['service_rate'] ?? 0,
                'tax_rate' => $data['tax_rate'] ?? 0,
                'created_by' => $user->id,
            ]);

            $expense->participants()->sync(array_keys($shares));

            foreach ($shares as $userId => $share) {
                $expense->splits()->create([
                    'user_id' => $userId,
                    'split_type' => $data['split_type'],
                    'custom_amount' => $data['split_type'] === 'custom' ? $data['custom_amounts'][$userId] ?? null : null,
                    'percentage' => $data['split_type'] === 'percentage' ? $data['percentages'][$userId] ?? null : null,
                    'share_amount' => $share,
                ]);
            }

            DebtService::regenerateFor($session);

            return $expense;
        });
    }

    public static function update(User $user, Expense $expense, array $data, ?string $receiptPath = null, bool $replaceReceipt = false): Expense
    {
        $session = $expense->session;

        return DB::transaction(function () use ($expense, $session, $data, $receiptPath) {
            NongkrongSession::whereKey($session->id)->lockForUpdate()->first();

            $grandTotal = CalculationService::grandTotal(
                $data['amount'],
                $data['discount_type'],
                $data['discount_value'],
                $data['service_rate'] ?? 0,
                $data['tax_rate'] ?? 0
            );

            $shares = SplitService::split(
                $data['split_type'],
                $grandTotal,
                array_values(array_unique($data['participant_ids'])),
                $data['split_type'] === 'custom' ? ($data['custom_amounts'] ?? []) : ($data['percentages'] ?? [])
            );

            $photo = $expense->receipt_photo;
            if ($receiptPath) {
                $photo = $receiptPath;
            } elseif ($data['remove_receipt'] ?? false) {
                $photo = null;
            }

            $expense->update([
                'name' => $data['name'],
                'amount' => $data['amount'],
                'paid_by_user_id' => $data['paid_by_user_id'],
                'category' => $data['category'],
                'note' => $data['note'] ?? null,
                'receipt_photo' => $photo,
                'discount_type' => $data['discount_type'],
                'discount_value' => $data['discount_value'],
                'service_rate' => $data['service_rate'] ?? 0,
                'tax_rate' => $data['tax_rate'] ?? 0,
            ]);

            $expense->participants()->sync(array_keys($shares));
            $expense->splits()->delete();
            foreach ($shares as $userId => $share) {
                $expense->splits()->create([
                    'user_id' => $userId,
                    'split_type' => $data['split_type'],
                    'custom_amount' => $data['split_type'] === 'custom' ? $data['custom_amounts'][$userId] ?? null : null,
                    'percentage' => $data['split_type'] === 'percentage' ? $data['percentages'][$userId] ?? null : null,
                    'share_amount' => $share,
                ]);
            }

            DebtService::regenerateFor($session);

            return $expense;
        });
    }

    public static function cancel(User $user, Expense $expense): void
    {
        $session = $expense->session;

        DB::transaction(function () use ($expense, $session) {
            NongkrongSession::whereKey($session->id)->lockForUpdate()->first();

            $expense->delete();

            DebtService::regenerateFor($session);
        });
    }
}
