<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'group_id',
        'name',
        'description',
        'sort_order',
        'created_by',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function messages()
    {
        return $this->hasMany(ChannelMessage::class);
    }

    public function sessions()
    {
        return $this->hasMany(NongkrongSession::class);
    }
}
