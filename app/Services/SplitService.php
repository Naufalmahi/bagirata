<?php

namespace App\Services;

use App\Enums\SplitType;
use InvalidArgumentException;

/**
 * Pembagian share pakai Largest Remainder Method biar jumlah selalu pas sama grand total.
 * Semua hasil integer & deterministic (urutan array input = urutan peserta).
 */
class SplitService
{
    /**
     * @param  array<int, int>  $participantIds  urutan peserta (tetap, dipakai buat tie-break).
     * @return array<int, int> key = user id, value = share
     */
    public static function equal(int $grandTotal, array $participantIds): array
    {
        $n = count($participantIds);
        if ($n === 0) {
            throw new InvalidArgumentException('Nggak ada peserta buat dibagi.');
        }

        $base = intdiv($grandTotal, $n);
        $remainder = $grandTotal % $n;

        $result = [];
        foreach ($participantIds as $index => $userId) {
            $result[$userId] = $base + ($index < $remainder ? 1 : 0);
        }

        return $result;
    }

    /**
     * @param  array<int, int>  $percentages  key = user id, value = persen (harus total 100)
     * @return array<int, int>
     */
    public static function percentage(int $grandTotal, array $percentages): array
    {
        if (empty($percentages)) {
            throw new InvalidArgumentException('Nggak ada persen yang dikasih.');
        }
        if (array_sum($percentages) !== 100) {
            throw new InvalidArgumentException('Total persen harus tepat 100.');
        }

        $shares = [];
        $leftover = $grandTotal;
        $fraction = [];
        foreach ($percentages as $userId => $pct) {
            $share = intdiv($grandTotal * $pct, 100);
            $shares[$userId] = $share;
            $leftover -= $share;
            $fraction[$userId] = ($grandTotal * $pct) % 100;
        }

        // Sisa rupiah dikasih ke yang punya sisa pecahan terbesar (tie-break: urutan input).
        $order = array_keys($percentages);
        usort($order, fn ($a, $b) => $fraction[$b] <=> $fraction[$a]);

        for ($i = 0; $leftover > 0; $i++, $leftover--) {
            $shares[$order[$i]]++;
        }

        return $shares;
    }

    /**
     * @param  array<int, int>  $amounts  key = user id, value = nominal. Jumlah wajib == grand total.
     * @return array<int, int>
     */
    public static function custom(int $grandTotal, array $amounts): array
    {
        if (empty($amounts)) {
            throw new InvalidArgumentException('Nggak ada nominal yang dikasih.');
        }
        if (array_sum($amounts) !== $grandTotal) {
            throw new InvalidArgumentException('Total nominal harus pas sama grand total.');
        }

        return $amounts;
    }

    /**
     * Dispatcher.
     *
     * @param  array<int, int>  $participantIds
     * @param  array<int, int>  $amountsOrPercent
     * @return array<int, int>
     */
    public static function split(string $splitType, int $grandTotal, array $participantIds, array $amountsOrPercent = []): array
    {
        return match ($splitType) {
            SplitType::EQUAL->value => self::equal($grandTotal, $participantIds),
            SplitType::PERCENTAGE->value => self::percentage($grandTotal, $amountsOrPercent),
            SplitType::CUSTOM->value => self::custom($grandTotal, $amountsOrPercent),
            default => throw new InvalidArgumentException('Metode split nggak dikenal.'),
        };
    }
}
