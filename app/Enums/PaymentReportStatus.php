<?php

namespace App\Enums;

enum PaymentReportStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Nunggu konfirmasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED => 'Ditolak',
        };
    }
}
