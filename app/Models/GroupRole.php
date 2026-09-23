<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'name',
        'permissions',
        'is_system',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'role_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
