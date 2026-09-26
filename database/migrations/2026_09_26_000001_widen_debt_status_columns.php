<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * DebtStatus::PAYMENT_REPORTED = 'payment_reported' (16 karakter), tapi kolom
 * dibuat VARCHAR(12) di 2026_09_22_000012. Di MySQL strict mode INSERT dengan
 * nilai itu jadi data-truncation error, jadi lapor bayar utang tidak bisa
 * dipakai sama sekali di produksi. SQLite (dipakai phpunit) tidak menegakkan
 * panjang VARCHAR, jadi bug ini lolos dari test suite.
 *
 * Kolom dilebar ke 20 supaya muat semua nilai enum dengan sisa ruang.
 */
return new class extends Migration
{
    private const LENGTH = 20;

    public function up(): void
    {
        $this->resize('debts', 'status');
        $this->resize('debt_payments', 'status');
    }

    public function down(): void
    {
        $this->resize('debts', 'status', 12);
        $this->resize('debt_payments', 'status', 12);
    }

    private function resize(string $table, string $column, int $length = self::LENGTH): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        // SQLite tidak menegakkan panjang VARCHAR dan tidak mendukung
        // ALTER COLUMN MODIFY, jadi tidak ada yang perlu diperbaiki di sana.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement(
            "ALTER TABLE `{$table}` MODIFY `{$column}` VARCHAR({$length}) NOT NULL DEFAULT 'pending'"
        );
    }
};
