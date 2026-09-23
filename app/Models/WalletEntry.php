<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletEntry extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'group_wallet_id',
        'user_id',
        'type',
        'category',
        'amount',
        'description',
        'receipt_photo',
        'status',
        'approved_by',
    ];

    public function wallet()
    {
        return $this->belongsTo(GroupWallet::class, 'group_wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
