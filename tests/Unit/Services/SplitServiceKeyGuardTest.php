<?php

namespace Tests\Unit\Services;

use App\Services\SplitService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SplitServiceKeyGuardTest extends TestCase
{
    public function test_equal_split_tidak_ikut_cek_key(): void
    {
        $shares = SplitService::split('equal', 50_000, [1, 2]);

        $this->assertSame([1 => 25_000, 2 => 25_000], $shares);
    }

    public function test_custom_split_key_beda_dari_peserta_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Peserta 1 & 2, tapi nominal cuma buat 1 & 3.
        SplitService::split('custom', 50_000, [1, 2], [1 => 25_000, 3 => 25_000]);
    }

    public function test_custom_split_key_orang_asing_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SplitService::split('custom', 50_000, [1, 2], [1 => 25_000, 99 => 25_000]);
    }

    public function test_percentage_split_key_beda_dari_peserta_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        SplitService::split('percentage', 50_000, [1, 2], [1 => 60, 3 => 40]);
    }

    public function test_custom_split_dengan_key_benar_diterima(): void
    {
        $shares = SplitService::split('custom', 50_000, [2, 1], [2 => 20_000, 1 => 30_000]);

        $this->assertSame([2 => 20_000, 1 => 30_000], $shares);
    }

    public function test_peserta_bolos_dari_map_ditolak(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // 3 peserta tapi cuma 2 yang dikasih nominal.
        SplitService::split('custom', 50_000, [1, 2, 3], [1 => 25_000, 2 => 25_000]);
    }
}
