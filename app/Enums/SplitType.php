<?php

namespace App\Enums;

enum SplitType: string
{
    case EQUAL = 'equal';
    case CUSTOM = 'custom';
    case PERCENTAGE = 'percentage';

    public function label(): string
    {
        return match ($this) {
            self::EQUAL => 'Rata-rata',
            self::CUSTOM => 'Nominal bebas',
            self::PERCENTAGE => 'Persen',
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
