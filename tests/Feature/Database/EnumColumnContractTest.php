<?php

namespace Tests\Feature\Database;

use App\Enums\DebtStatus;
use App\Enums\PaymentReportStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Jaga kontrak antara nilai enum dan panjang kolom yang menampungnya.
 *
 * SQLite (dipakai phpunit) tidak menegakkan panjang VARCHAR, jadi kolom yang
 * terlalu sempit tetap lolos di test suite dan baru meledak saat dipakai
 * MySQL di produksi. Test di bawah menutup celah itu.
 */
class EnumColumnContractTest extends TestCase
{
    /**
     * Panjang minimum yang harus disediakan kolom status.
     */
    private const REQUIRED_LENGTH = 20;

    public static function enumStatusProvider(): array
    {
        return [
            'DebtStatus' => [DebtStatus::class],
            'PaymentReportStatus' => [PaymentReportStatus::class],
        ];
    }

    #[DataProvider('enumStatusProvider')]
    public function test_nilai_enum_status_muat_di_kolom_yang_disiatkan(string $enumClass): void
    {
        $terpanjang = collect($enumClass::cases())
            ->map(fn ($case) => strlen($case->value))
            ->max();

        $this->assertLessThanOrEqual(
            self::REQUIRED_LENGTH,
            $terpanjang,
            "Nilai terpanjang {$enumClass} adalah {$terpanjang} karakter, kolom status harus minimal "
            .self::REQUIRED_LENGTH
            .' karakter.'
        );
    }

    public static function statusColumnProvider(): array
    {
        return [
            'debts.status' => ['debts', 'status'],
            'debt_payments.status' => ['debt_payments', 'status'],
        ];
    }

    /**
     * Hanya jalan di MySQL, karena di situlah panjang VARCHAR benar-benar
     * ditegakkan. Di driver lain test ini dilewati.
     */
    #[DataProvider('statusColumnProvider')]
    public function test_kolom_status_di_mysql_lebarnya_cukup(string $table, string $column): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Panjang VARCHAR hanya ditegakkan di MySQL.');
        }

        $this->assertTrue(
            Schema::hasColumn($table, $column),
            "Kolom {$table}.{$column} tidak ditemukan."
        );

        $panjang = DB::selectOne(
            'SELECT CHARACTER_MAXIMUM_LENGTH AS len FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        )->len;

        $this->assertGreaterThanOrEqual(
            self::REQUIRED_LENGTH,
            (int) $panjang,
            "Kolom {$table}.{$column} hanya {$panjang} karakter, harus minimal "
            .self::REQUIRED_LENGTH
            .' karakter agar semua nilai enum muat.'
        );
    }
}
