<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom uang di kas grup dibuat INTEGER, jadi mentok di 2.147.483.647
 * (sekitar Rp 2,1 miliar). Saldo kas grup itu akumulatif, jadi angka segitu
 * gampang dilampaui di grup yang ramai atau yang setoran rutin besar.
 * Terbukti di MySQL:
 *   ERROR 1264 Out of range value for column 'balance' at row 1
 *
 * - group_wallets.balance  -> BIGINT (signed, karena spec mendefinisikan saldo
 *   sebagai sum(in) - sum(out), jadi boleh negatif saat pengeluaran duluan).
 * - wallet_entries.amount  -> BIGINT UNSIGNED (spec menyebutnya unsigned;
 *   nominal negatif nggak punya arti, arahnya ditentukan kolom `type`).
 *
 * SQLite tidak menegakkan tipe numerik, jadi test suite nggak bakal menangkap
 * batas ini dan tidak ada yang perlu diubah di sana.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->resize('group_wallets', 'balance', 'BIGINT NOT NULL DEFAULT 0');
        $this->resize('wallet_entries', 'amount', 'BIGINT UNSIGNED NOT NULL');
    }

    public function down(): void
    {
        $this->resize('group_wallets', 'balance', 'INT NOT NULL DEFAULT 0');
        $this->resize('wallet_entries', 'amount', 'INT NOT NULL');
    }

    private function resize(string $table, string $column, string $definition): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        // SQLite tidak mendukung ALTER COLUMN MODIFY, dan INTEGER di sana
        // sudah 64-bit, jadi tidak ada yang perlu diperbaiki.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` {$definition}");
    }
};
