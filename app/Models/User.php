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
        'username',
        'email',
        'password',
        'bio',
        'phone',
        'bank_name',
        'bank_account',
        'avatar_path',
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

    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    public function friends()
    {
        $sentIds = Friendship::where('sender_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('receiver_id');

        $receivedIds = Friendship::where('receiver_id', $this->id)
            ->where('status', 'accepted')
            ->pluck('sender_id');

        return User::whereIn('id', $sentIds->merge($receivedIds));
    }

    public function isFriendWith(User $user): bool
    {
        return Friendship::where(function ($q) use ($user) {
            $q->where('sender_id', $this->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $this->id);
        })->where('status', 'accepted')->exists();
    }

    public function friendshipWith(User $user)
    {
        return Friendship::where(function ($q) use ($user) {
            $q->where('sender_id', $this->id)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $this->id);
        })->first();
    }
}
