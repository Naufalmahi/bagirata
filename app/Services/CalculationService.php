<?php

namespace App\Services;

use App\Enums\DiscountType;
use App\Services\Support\Money;

/**
 * Perhitungan grand total: subtotal → discount → service charge (persen) → tax (persen).
 * Semua integer rupiah, deterministic.
 */
class CalculationService
{
    public static function discountAmount(int $amount, string $discountType, int $discountValue): int
    {
        if ($discountValue <= 0) {
            return 0;
        }

        return $discountType === DiscountType::PERCENT->value
            ? Money::percentOf($amount, $discountValue)
            : $discountValue;
    }

    public static function baseAfterDiscount(int $amount, string $discountType, int $discountValue): int
    {
        return $amount - self::discountAmount($amount, $discountType, $discountValue);
    }

    /**
     * Service charge dihitung dari nominal setelah discount.
     */
    public static function serviceAmount(int $baseAfterDiscount, int $serviceRate): int
    {
        return $serviceRate > 0 ? Money::percentOf($baseAfterDiscount, $serviceRate) : 0;
    }

    /**
     * Pajak dihitung dari (subtotal - discount) + service (pola umum PPn di Indonesia).
     */
    public static function taxAmount(int $baseAfterDiscount, int $serviceAmount, int $taxRate): int
    {
        return $taxRate > 0 ? Money::percentOf($baseAfterDiscount + $serviceAmount, $taxRate) : 0;
    }

    public static function grandTotal(
        int $amount,
        string $discountType,
        int $discountValue,
        int $serviceRate,
        int $taxRate
    ): int {
        $base = self::baseAfterDiscount($amount, $discountType, $discountValue);
        $service = self::serviceAmount($base, $serviceRate);
        $tax = self::taxAmount($base, $service, $taxRate);

        return $base + $service + $tax;
    }
}
