<?php

namespace App\Models;

use App\Enums\DebtStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'nongkrong_session_id',
        'from_user_id',
        'to_user_id',
        'amount',
        'status',
        'note',
        'settled_at',
        'settled_by_user_id',
    ];

    protected $casts = [
        'amount' => 'integer',
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

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', DebtStatus::PENDING->value);
    }

    public function scopeSettled(Builder $query): Builder
    {
        return $query->where('status', DebtStatus::SETTLED->value);
    }

    public function markSettled(?User $by = null): void
    {
        $this->status = DebtStatus::SETTLED->value;
        $this->settled_at = now();
        $this->settled_by_user_id = $by?->id;
        $this->save();
    }
}
