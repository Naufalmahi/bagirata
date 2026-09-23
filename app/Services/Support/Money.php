<?php

namespace App\Services\Support;

use InvalidArgumentException;

/**
 * Helper uang: semua nominal integer rupiah, kalkulasi persen pakai integer math
 * biar deterministic di semua platform (nggak ada binary floating point).
 */
class Money
{
    /**
     * round-half-up(x * pct / 100) dengan integer math.
     */
    public static function percentOf(int $base, int $pct): int
    {
        if ($base < 0 || $pct < 0) {
            throw new InvalidArgumentException('Nominal dan persen nggak boleh negatif.');
        }

        return intdiv($base * $pct + 50, 100);
    }

    public static function format(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    public static function toRupiah(int $amount): string
    {
        return self::format($amount);
    }
}
