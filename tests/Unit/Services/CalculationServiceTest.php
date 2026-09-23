<?php

namespace Tests\Unit\Services;

use App\Services\CalculationService;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    public function test_discount_fixed_tanpa_addon(): void
    {
        $this->assertSame(90_000, CalculationService::grandTotal(100_000, 'fixed', 10_000, 0, 0));
    }

    public function test_discount_persen(): void
    {
        $this->assertSame(95_000, CalculationService::grandTotal(100_000, 'percent', 5, 0, 0));
    }

    public function test_service_charge_persen_dari_base(): void
    {
        $this->assertSame(105_000, CalculationService::grandTotal(100_000, 'fixed', 0, 5, 0));
    }

    public function test_pajak_persen_di_atas_harga_plus_service(): void
    {
        // 100rb + service 5% (5rb) = 105rb, pajak 11% = 11.550
        $this->assertSame(116_550, CalculationService::grandTotal(100_000, 'fixed', 0, 5, 11));
    }

    public function test_kombinasi_discount_service_dan_pajak(): void
    {
        // 200rb - disc 10% (20rb) = 180rb; service 5% = 9rb; pajak 11% dari 189rb = 20.790
        $this->assertSame(209_790, CalculationService::grandTotal(200_000, 'percent', 10, 5, 11));
    }

    public function test_rounding_half_up(): void
    {
        // 100.000 * 11% = 11.000
        $this->assertSame(11_000, CalculationService::taxAmount(100_000, 0, 11));

        // 99.999 * 3% = 2.999,97 → dibulatkan 3.000
        $this->assertSame(3_000, CalculationService::taxAmount(99_999, 0, 3));
    }
}
