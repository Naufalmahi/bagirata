<?php

namespace App\Models;

use App\Enums\DebtStatus;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NongkrongSession extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'group_id',
        'channel_id',
        'name',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'nongkrong_session_members', 'nongkrong_session_id', 'user_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function debts()
    {
        return $this->hasMany(Debt::class);
    }

    public function isMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->exists();
    }

    /**
     * Status session (derived, nggak disimpan).
     */
    public function status(): string
    {
        if ($this->expenses()->count() === 0) {
            return 'draft';
        }

        return $this->debts()->where('status', '!=', DebtStatus::SETTLED->value)->exists() ? 'active' : 'settled';
    }

    public function statusLabel(): string
    {
        return match ($this->status()) {
            'draft' => 'Belum ada pengeluaran',
            'active' => 'Masih ada yang belum beres',
            'settled' => 'Pembagian beres',
            default => 'Lainnya',
        };
    }
}
