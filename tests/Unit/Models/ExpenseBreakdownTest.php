<?php

namespace Tests\Unit\Models;

use App\Models\Expense;
use App\Services\CalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Expense::serviceAmount() dan taxAmount() pernah manggil CalculationService
 * dengan terlalu banyak argumen. PHP diam-diam membuang sisanya, sehingga
 * argumen terakhir jadi dibaca sebagai "rate" dan rincian yang tampil di layar
 * sama sekali nggak cocok dengan total.
 *
 * Test ini mengunci invarian yang jadi tempat bug ituketahuan: rincian
 * (base + service + pajak) harus selalu sama dengan grand total.
 */
class ExpenseBreakdownTest extends TestCase
{
    use RefreshDatabase;

    public static function addonProvider(): array
    {
        return [
            'tanpa addon' => [100_000, 'fixed', 0, 0, 0],
            'service saja' => [100_000, 'fixed', 0, 5, 0],
            'pajak saja' => [100_000, 'fixed', 0, 0, 11],
            'discount fixed + service + pajak' => [100_000, 'fixed', 10_000, 5, 11],
            'discount persen + service + pajak' => [100_000, 'percent', 10, 5, 11],
            'service besar' => [250_000, 'fixed', 0, 10, 0],
            'pajak besar' => [1_500_000, 'fixed', 0, 0, 11],
            'semua addon tanpa discount' => [89_000, 'fixed', 0, 7, 12],
            'nominal ganjil' => [33_333, 'fixed', 3_333, 3, 7],
        ];
    }

    #[DataProvider('addonProvider')]
    public function test_rincian_rekonsiliasi_dengan_grand_total(
        int $amount,
        string $discountType,
        int $discountValue,
        int $serviceRate,
        int $taxRate
    ): void {
        $expense = $this->makeExpense($amount, $discountType, $discountValue, $serviceRate, $taxRate);

        $expectedTotal = $expense->baseAfterDiscount() + $expense->serviceAmount() + $expense->taxAmount();

        $this->assertSame(
            $expectedTotal,
            $expense->grandTotal(),
            'Rincian tidak cocok dengan grand total.'
        );
    }

    #[DataProvider('addonProvider')]
    public function test_accessor_model_sama_dengan_perhitungan_service(
        int $amount,
        string $discountType,
        int $discountValue,
        int $serviceRate,
        int $taxRate
    ): void {
        $expense = $this->makeExpense($amount, $discountType, $discountValue, $serviceRate, $taxRate);

        $base = CalculationService::baseAfterDiscount($amount, $discountType, $discountValue);

        $this->assertSame(
            CalculationService::serviceAmount($base, $serviceRate),
            $expense->serviceAmount(),
            'serviceAmount() model beda dengan CalculationService.'
        );

        $this->assertSame(
            CalculationService::taxAmount($base, CalculationService::serviceAmount($base, $serviceRate), $taxRate),
            $expense->taxAmount(),
            'taxAmount() model beda dengan CalculationService.'
        );
    }

    public function test_service_charge_dihitung_dari_nominal_setelah_discount(): void
    {
        // 100rb - diskon 10rb = 90rb, service 10% = 9rb.
        // Versi lama salah menulis 100.000.000 karena diskon dipakai sebagai "rate".
        $expense = $this->makeExpense(100_000, 'fixed', 10_000, 10, 0);

        $this->assertSame(10_000, $expense->discountAmount());
        $this->assertSame(90_000, $expense->baseAfterDiscount());
        $this->assertSame(9_000, $expense->serviceAmount());
        $this->assertSame(99_000, $expense->grandTotal());
    }

    public function test_pajak_dihitung_dari_base_plus_service(): void
    {
        // 90rb + service 9rb = 99rb, pajak 10% = 9.900.
        $expense = $this->makeExpense(100_000, 'fixed', 10_000, 10, 10);

        $this->assertSame(9_000, $expense->serviceAmount());
        $this->assertSame(9_900, $expense->taxAmount());
        $this->assertSame(108_900, $expense->grandTotal());
    }

    private function makeExpense(
        int $amount,
        string $discountType,
        int $discountValue,
        int $serviceRate,
        int $taxRate
    ): Expense {
        return Expense::factory()->make([
            'amount' => $amount,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'service_rate' => $serviceRate,
            'tax_rate' => $taxRate,
        ]);
    }
}
