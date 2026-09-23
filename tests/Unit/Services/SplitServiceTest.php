<?php

namespace Tests\Unit\Services;

use App\Services\SplitService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SplitServiceTest extends TestCase
{
    public function test_equal_100000_dibagi_3(): void
    {
        // 100.000 / 3 → 33.334 + 33.333 + 33.333 (peserta pertama dapat sisa)
        $result = SplitService::equal(100_000, [1, 2, 3]);

        $this->assertSame([1 => 33_334, 2 => 33_333, 3 => 33_333], $result);
        $this->assertSame(100_000, array_sum($result));
    }

    public function test_equal_total_selalu_sama_dengan_grand_total(): void
    {
        foreach ([1, 2, 3, 5, 13] as $n) {
            $ids = array_map(fn ($i) => $i, range(1, $n));
            $result = SplitService::equal(1_000_001, $ids);
            $this->assertSame(1_000_001, array_sum($result), "gagal untuk n={$n}");
        }
    }

    public function test_percentage_total_harus_100(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SplitService::percentage(100_000, [1 => 50, 2 => 40, 3 => 5]);
    }

    public function test_percentage_sisa_bulat(): void
    {
        // 10.001 * 33% → 3.300; sisa 1 rupiah dikasih ke sisa pecahan terbesar (34%)
        $result = SplitService::percentage(10_001, [1 => 33, 2 => 33, 3 => 34]);

        $this->assertSame(10_001, array_sum($result));
        $this->assertSame(3_401, $result[3]);
    }

    public function test_percentage_total_pas(): void
    {
        $result = SplitService::percentage(1_000_000, [1 => 50, 2 => 30, 3 => 20]);

        $this->assertSame(500_000, $result[1]);
        $this->assertSame(300_000, $result[2]);
        $this->assertSame(200_000, $result[3]);
    }

    public function test_custom_sum_harus_pas_grand_total(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SplitService::custom(100_000, [1 => 40_000, 2 => 50_000]);
    }

    public function test_custom_kembalikan_input(): void
    {
        $result = SplitService::custom(100_000, [1 => 60_000, 2 => 40_000]);

        $this->assertSame([1 => 60_000, 2 => 40_000], $result);
    }

    public function test_split_dispatcher(): void
    {
        $this->assertSame(10_000, SplitService::split('equal', 30_000, [1, 2, 3])[1]);
    }
}
