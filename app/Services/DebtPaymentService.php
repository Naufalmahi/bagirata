<?php

namespace App\Services;

use App\Enums\DebtStatus;
use App\Enums\PaymentReportStatus;
use App\Exceptions\BusinessException;
use App\Models\ActivityLog;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\NongkrongSession;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Payment flow: debtor lapor bayar → creditor konfirmasi / tolak.
 * Invariants:
 * - Debtor nggak bisa konfirmasi pembayaran sendiri.
 * - Report amount nggak boleh > outstanding (cicilan diperbolehkan).
 * - Satu debt cuma boleh punya satu report pending.
 * - Payment history nggak pernah dihapus.
 */
class DebtPaymentService
{
    /**
     * @param  array{amount: int, method: string, note?: ?string}  $data
     */
    public static function report(User $reporter, Debt $debt, array $data, ?string $proofPath = null): DebtPayment
    {
        return DB::transaction(function () use ($reporter, $debt, $data, $proofPath) {
            NongkrongSession::whereKey($debt->nongkrong_session_id)->lockForUpdate()->first();
            $debt->refresh();

            if (! $debt->canDebtorReport($reporter)) {
                throw new BusinessException('Lu nggak bisa lapor bayar buat utang ini.', 403);
            }

            if ($debt->hasPendingReport()) {
                throw new BusinessException('Masih ada laporan yang nunggu konfirmasi. Sabar yaa.');
            }

            if ($data['amount'] > $debt->outstanding()) {
                throw new BusinessException('Nominalnya lebih besar dari sisa utang, cek lagi yaa.');
            }

            $payment = $debt->payments()->create([
                'amount' => $data['amount'],
                'method' => $data['method'],
                'note' => $data['note'] ?? null,
                'proof_photo' => $proofPath,
                'status' => PaymentReportStatus::PENDING->value,
                'reported_by_user_id' => $reporter->id,
            ]);

            $debt->status = DebtStatus::PAYMENT_REPORTED->value;
            $debt->save();

            self::audit('payment_reported', $debt, [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'method' => $payment->method,
            ]);

            return $payment;
        });
    }

    public static function confirm(User $reviewer, Debt $debt, DebtPayment $payment, ?string $note = null): Debt
    {
        return DB::transaction(function () use ($reviewer, $debt, $payment, $note) {
            NongkrongSession::whereKey($debt->nongkrong_session_id)->lockForUpdate()->first();
            $debt->refresh();
            $payment->refresh();

            self::assertReviewable($reviewer, $debt, $payment);

            $payment->update([
                'status' => PaymentReportStatus::CONFIRMED->value,
                'review_note' => $note ?: null,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
            ]);
            self::audit('payment_confirmed', $debt, ['payment_id' => $payment->id, 'amount' => $payment->amount]);

            $debt->paid_amount += $payment->amount;

            if ($debt->paid_amount >= $debt->amount) {
                $debt->markSettled($reviewer);
                self::audit('debt_settled', $debt, ['settled_by' => $reviewer->id]);
            } else {
                $debt->status = DebtStatus::CONFIRMED->value;
                $debt->save();
            }

            return $debt;
        });
    }

    public static function reject(User $reviewer, Debt $debt, DebtPayment $payment, string $reason): Debt
    {
        return DB::transaction(function () use ($reviewer, $debt, $payment, $reason) {
            NongkrongSession::whereKey($debt->nongkrong_session_id)->lockForUpdate()->first();
            $debt->refresh();
            $payment->refresh();

            self::assertReviewable($reviewer, $debt, $payment);

            $payment->update([
                'status' => PaymentReportStatus::REJECTED->value,
                'review_note' => $reason,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
            ]);

            $debt->status = DebtStatus::REJECTED->value;
            $debt->save();

            self::audit('payment_rejected', $debt, [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'reason' => $reason,
            ]);

            return $debt;
        });
    }

    private static function assertReviewable(User $reviewer, Debt $debt, DebtPayment $payment): void
    {
        if ($payment->debt_id !== $debt->id) {
            throw new BusinessException('Laporan pembayaran bukan punya utang ini.', 404);
        }

        if ($debt->to_user_id !== $reviewer->id || $debt->from_user_id === $reviewer->id) {
            throw new BusinessException('Cuma kreditur yang bisa ngecek & konfirmasi pembayaran.', 403);
        }

        if (! $payment->isPending()) {
            throw new BusinessException('Laporan ini udah diproses sebelumnya.', 422);
        }

        if ($debt->isSettled()) {
            throw new BusinessException('Utang ini udah lunas, nggak perlu konfirmasi lagi.', 422);
        }
    }

    private static function audit(string $event, Debt $debt, array $properties): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'description' => \App\Enums\DebtStatus::tryFrom($debt->status)?->label().' · Debt #'.$debt->id,
            'auditable_type' => Debt::class,
            'auditable_id' => $debt->id,
            'properties' => $properties,
        ]);
    }
}
