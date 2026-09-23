<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInvite extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'token',
        'used_count',
        'max_uses',
        'expires_at',
        'active',
        'revoked_at',
        'created_by',
        'revoked_by',
    ];

    protected $casts = [
        'active' => 'boolean',
        'used_count' => 'integer',
        'max_uses' => 'integer',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function revoker()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && now()->gt($this->expires_at);
    }

    public function usageLimitReached(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }

    public function usable(): bool
    {
        return $this->active && ! $this->isExpired() && ! $this->usageLimitReached();
    }

    public function invalidReason(): ?string
    {
        if (! $this->active) {
            return 'Link undangan udah dicabut (revoke) sama admin.';
        }

        if ($this->isExpired()) {
            return 'Link undangan udah kedaluwarsa.';
        }

        if ($this->usageLimitReached()) {
            return 'Batas pemakaian link undangan udah abis.';
        }

        return null;
    }

    public function usesSummary(): string
    {
        if ($this->max_uses !== null) {
            return "{$this->used_count}/{$this->max_uses} dipakai";
        }

        return "dipakai {$this->used_count}×";
    }

    public function lifetimeLabel(): string
    {
        if ($this->max_uses === null && $this->expires_at === null) {
            return 'Tanpa batas';
        }

        $parts = [];
        if ($this->expires_at !== null) {
            $parts[] = 'berlaku sampai '.\Illuminate\Support\Carbon::parse($this->expires_at)->translatedFormat('d M Y, H:i');
        }
        if ($this->max_uses !== null) {
            $parts[] = "maks {$this->max_uses} pemakaian";
        }

        return implode(' · ', $parts);
    }

    public function revoke(?int $byUserId = null): void
    {
        $this->active = false;
        $this->revoked_at = now();
        $this->revoked_by = $byUserId;
        $this->save();
    }
}
