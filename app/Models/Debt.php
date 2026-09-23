<?php

namespace App\Models;

use App\Enums\DebtStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
=======

class Debt extends Model
{
    use HasFactory, LogsActivity;
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a

    protected $fillable = [
        'nongkrong_session_id',
        'from_user_id',
        'to_user_id',
        'amount',
<<<<<<< HEAD
        'paid_amount',
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
        'status',
        'note',
        'settled_at',
        'settled_by_user_id',
    ];

    protected $casts = [
        'amount' => 'integer',
<<<<<<< HEAD
        'paid_amount' => 'integer',
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
        'settled_at' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(NongkrongSession::class, 'nongkrong_session_id');
    }

    public function debtor()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function creditor()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function settler()
    {
        return $this->belongsTo(User::class, 'settled_by_user_id');
    }

<<<<<<< HEAD
    public function payments()
    {
        return $this->hasMany(DebtPayment::class)->latest();
    }

    public function confirmedPayments()
    {
        return $this->hasMany(DebtPayment::class)->where('status', \App\Enums\PaymentReportStatus::CONFIRMED->value);
    }

=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', DebtStatus::PENDING->value);
    }

    public function scopeSettled(Builder $query): Builder
    {
        return $query->where('status', DebtStatus::SETTLED->value);
    }

<<<<<<< HEAD
    public function scopeInFlight(Builder $query): Builder
    {
        return $query->whereIn('status', [
            DebtStatus::PAYMENT_REPORTED->value,
            DebtStatus::CONFIRMED->value,
        ]);
    }

    public function status(): DebtStatus
    {
        return DebtStatus::tryFrom($this->status) ?? DebtStatus::PENDING;
    }

    public function outstanding(): int
    {
        return max(0, $this->amount - $this->paid_amount);
    }

    public function isSettled(): bool
    {
        return $this->status === DebtStatus::SETTLED->value;
    }

    public function isReportable(): bool
    {
        return $this->status === DebtStatus::PENDING->value
            || $this->status === DebtStatus::REJECTED->value
            || ($this->status === DebtStatus::CONFIRMED->value && $this->outstanding() > 0);
    }

    public function canDebtorReport(User $user): bool
    {
        return $this->from_user_id === $user->id
            && $this->session->isMember($user)
            && $this->outstanding() > 0
            && $this->isReportable();
    }

    public function canCreditorReview(User $user): bool
    {
        return $this->to_user_id === $user->id
            && $this->session->isMember($user)
            && $this->hasPendingReport();
    }

    public function hasPendingReport(): bool
    {
        return $this->payments()->where('status', \App\Enums\PaymentReportStatus::PENDING->value)->exists();
    }

    /**
     * Mark debt lunas. Only called from DebtPaymentService (bukan self-confirm bebas).
     */
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    public function markSettled(?User $by = null): void
    {
        $this->status = DebtStatus::SETTLED->value;
        $this->settled_at = now();
        $this->settled_by_user_id = $by?->id;
        $this->save();
    }
}
