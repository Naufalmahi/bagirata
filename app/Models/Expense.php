<?php

namespace App\Models;

use App\Enums\DiscountType;
use App\Enums\ExpenseCategory;
use App\Services\CalculationService;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'nongkrong_session_id',
        'name',
        'amount',
        'paid_by_user_id',
        'category',
        'note',
        'receipt_photo',
        'discount_type',
        'discount_value',
        'service_rate',
        'tax_rate',
        'created_by',
    ];

    public function session()
    {
        return $this->belongsTo(NongkrongSession::class, 'nongkrong_session_id');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'expense_participants', 'expense_id', 'user_id');
    }

    public function splits()
    {
        return $this->hasMany(ExpenseSplit::class);
    }

    public function discountType(): DiscountType
    {
        return DiscountType::tryFrom($this->discount_type) ?? DiscountType::FIXED;
    }

    public function category(): ExpenseCategory
    {
        return ExpenseCategory::tryFrom($this->category) ?? ExpenseCategory::LAINNYA;
    }

    public function discountAmount(): int
    {
        return CalculationService::discountAmount($this->amount, $this->discount_type, $this->discount_value);
    }

    public function serviceAmount(): int
    {
        return CalculationService::serviceAmount($this->amount, $this->discountAmount(), $this->service_rate);
    }

    public function taxAmount(): int
    {
        return CalculationService::taxAmount($this->amount, $this->discountAmount(), $this->serviceAmount(), $this->tax_rate);
    }

    public function grandTotal(): int
    {
        return CalculationService::grandTotal(
            $this->amount,
            $this->discount_type,
            $this->discount_value,
            $this->service_rate,
            $this->tax_rate
        );
    }
}
