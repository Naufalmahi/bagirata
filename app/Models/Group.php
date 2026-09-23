<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'avatar_path',
        'category',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, GroupMember::class, 'group_id', 'id', 'id', 'user_id');
    }

    public function roles()
    {
        return $this->hasMany(GroupRole::class);
    }

    public function invites()
    {
        return $this->hasMany(GroupInvite::class);
    }

    public function activeInvite()
    {
        return $this->hasOne(GroupInvite::class)->where('active', true)->latestOfMany();
    }

    public function channels()
    {
        return $this->hasMany(Channel::class)->orderBy('sort_order');
    }

    public function wallet()
    {
        return $this->hasOne(GroupWallet::class);
    }

    public function sessions()
    {
        return $this->hasMany(NongkrongSession::class);
    }

    public function isMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }
}
