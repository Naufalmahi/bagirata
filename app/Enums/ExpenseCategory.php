<?php

namespace App\Enums;

enum ExpenseCategory: string
{
    case NONGKRONG = 'nongkrong';
    case MAKANAN = 'makanan';
    case MAKAN = 'makan';
    case MINUM = 'minum';
    case TRANSPORTASI = 'transportasi';
    case TRANSPORT = 'transport';
    case ENTERTAINMENT = 'entertainment';
    case SEWA = 'sewa';
    case BELANJA = 'belanja';
    case EVENT = 'event';
    case LAINNYA = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::NONGKRONG => 'Nongkrong',
            self::MAKANAN => 'Makanan',
            self::MAKAN => 'Makan',
            self::MINUM => 'Minum',
            self::TRANSPORTASI => 'Transportasi',
            self::TRANSPORT => 'Transport',
            self::ENTERTAINMENT => 'Entertainment',
            self::SEWA => 'Sewa',
            self::BELANJA => 'Belanja',
            self::EVENT => 'Event',
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
