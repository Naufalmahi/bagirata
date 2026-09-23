<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case MAKAN = 'makan';
    case MINUM = 'minum';
    case TRANSPORT = 'transport';
    case SEWA = 'sewa';
    case BELANJA = 'belanja';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::MAKAN => 'Makan',
            self::MINUM => 'Minum',
            self::TRANSPORT => 'Transport',
            self::SEWA => 'Sewa',
            self::BELANJA => 'Belanja',
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
