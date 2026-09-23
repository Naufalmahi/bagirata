<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupWallet extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'balance',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function entries()
    {
        return $this->hasMany(WalletEntry::class);
    }
}
