<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentReportStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'debt_id',
        'amount',
        'method',
        'note',
        'proof_photo',
        'status',
        'review_note',
        'reported_by_user_id',
        'reviewed_by_user_id',
        'reviewed_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function status(): PaymentReportStatus
    {
        return PaymentReportStatus::tryFrom($this->status) ?? PaymentReportStatus::PENDING;
    }

    public function methodLabel(): ?string
    {
        return PaymentMethod::tryFrom($this->method)?->label();
    }

    public function isPending(): bool
    {
        return $this->status === PaymentReportStatus::PENDING->value;
    }
}
