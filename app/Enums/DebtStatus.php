<?php

namespace App\Enums;

enum DebtStatus: string
{
    case PENDING = 'pending';
    case PAYMENT_REPORTED = 'payment_reported';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case SETTLED = 'settled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Belum dibayar',
            self::PAYMENT_REPORTED => 'Nunggu konfirmasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED => 'Ditolak',
            self::SETTLED => 'Udah beres',
        };
    }
}
