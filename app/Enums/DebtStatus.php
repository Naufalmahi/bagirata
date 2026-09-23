<?php

namespace App\Enums;

enum DebtStatus: string
{
    case PENDING = 'pending';
    case SETTLED = 'settled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Belum beres',
            self::SETTLED => 'Udah beres',
        };
    }
}
