<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function ownedGroups()
    {
        return $this->hasMany(Group::class, 'user_id');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members', 'user_id', 'group_id')
            ->withPivot('role_id', 'joined_at')
            ->withTimestamps();
    }

    public function sessionsCreated()
    {
        return $this->hasMany(NongkrongSession::class, 'user_id');
    }

    public function nongkrongSessions()
    {
        return $this->belongsToMany(NongkrongSession::class, 'nongkrong_session_members', 'user_id', 'nongkrong_session_id')
            ->withPivot('joined_at')
            ->withTimestamps();
    }
}
