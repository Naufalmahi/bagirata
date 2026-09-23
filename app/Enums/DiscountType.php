<?php

namespace App\Enums;

enum DiscountType: string
{
    case FIXED = 'fixed';
    case PERCENT = 'percent';

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Nominal fix',
            self::PERCENT => 'Persen',
        };
    }
}
