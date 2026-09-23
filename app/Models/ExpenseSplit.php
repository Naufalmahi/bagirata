<?php

namespace App\Models;

use App\Enums\SplitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseSplit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'expense_id',
        'user_id',
        'split_type',
        'custom_amount',
        'percentage',
        'share_amount',
    ];

    protected $casts = [
        'custom_amount' => 'integer',
        'percentage' => 'integer',
        'share_amount' => 'integer',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function splitType(): SplitType
    {
        return SplitType::tryFrom($this->split_type) ?? SplitType::EQUAL;
    }
}
