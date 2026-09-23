<?php

namespace App\Enums;

enum DebtStatus: string
{
    case PENDING = 'pending';
<<<<<<< HEAD
    case PAYMENT_REPORTED = 'payment_reported';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
=======
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    case SETTLED = 'settled';

    public function label(): string
    {
        return match ($this) {
<<<<<<< HEAD
            self::PENDING => 'Belum dibayar',
            self::PAYMENT_REPORTED => 'Nunggu konfirmasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED => 'Ditolak',
=======
            self::PENDING => 'Belum beres',
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
            self::SETTLED => 'Udah beres',
        };
    }
}
