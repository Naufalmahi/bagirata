<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case TRANSFER = 'transfer';
    case E_WALLET = 'e_wallet';
    case QRIS = 'qris';
    case CASH = 'cash';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::TRANSFER => 'Transfer bank',
            self::E_WALLET => 'E-wallet',
            self::QRIS => 'QRIS',
            self::CASH => 'Cash',
            self::LAINNYA => 'Lainnya',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function casesAsList(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
