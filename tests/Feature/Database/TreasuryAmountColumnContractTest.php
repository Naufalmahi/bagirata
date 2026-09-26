<?php

namespace Tests\Feature\Database;

use App\Models\User;
use App\Services\GroupService;
use App\Services\TreasuryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Kolom uang di kas grup tadinya INTEGER, jadi mentok di 2.147.483.647
 * (sekitar Rp 2,1 miliar). Saldo kas grup itu akumulatif, jadi angka segitu
 * gampang dilampaui di grup yang ramai atauicrosiaan sama.
 *
 * Di SQLite test suite nggak akan meledak, makanya tipe kolomnya dicek
 * langsung lewat information_schema MySQL.
 */
class TreasuryAmountColumnContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Di atas batas INTEGER 32-bit.
     */
    private const DI_ATAS_LIMIT_INT = 3_000_000_000;

    public static function amountColumnProvider(): array
    {
        return [
            'wallet_entries.amount' => ['wallet_entries', 'amount'],
            'group_wallets.balance' => ['group_wallets', 'balance'],
        ];
    }

    /**
     * Hanya MySQL yang menegakkan tipe numerik, jadi driver lain dilewati.
     */
    #[DataProvider('amountColumnProvider')]
    public function test_kolom_uang_pakai_bigint_di_mysql(string $table, string $column): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('Tipe numerik hanya ditegakkan di MySQL.');
        }

        $this->assertTrue(Schema::hasColumn($table, $column), "Kolom {$table}.{$column} tidak ditemukan.");

        $tipe = DB::selectOne(
            'SELECT DATA_TYPE AS data_type, COLUMN_TYPE AS column_type FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );

        $this->assertSame(
            'bigint',
            strtolower($tipe->data_type),
            "Kolom {$table}.{$column} bertipe {$tipe->column_type}, harus bigint supaya nggak mentok di 2.147.483.647."
        );
    }

    public function test_nominal_di_atas_limit_int32_justru_bisa_disimpan(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Kas Besar']);
        $wallet = TreasuryService::getOrCreateWallet($group);

        $entry = TreasuryService::recordEntry($wallet, $owner, [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => self::DI_ATAS_LIMIT_INT,
            'description' => 'Iuran tahunan',
        ]);

        $this->assertSame(self::DI_ATAS_LIMIT_INT, $entry->fresh()->amount);
        $this->assertSame(self::DI_ATAS_LIMIT_INT, $wallet->fresh()->balance);

        TreasuryService::rejectEntry($entry->fresh(), $owner);

        $this->assertSame(0, $wallet->fresh()->balance);
    }

    public function test_saldo_kas_bisa_negatif_kalau_pengeluaran_melebihi_pemasukan(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Kas Minus']);
        $wallet = TreasuryService::getOrCreateWallet($group);

        TreasuryService::recordEntry($wallet, $owner, [
            'type' => 'out',
            'category' => 'sewa',
            'amount' => 250_000,
            'description' => 'Sewa dibayar dulu',
        ]);

        // Spec: saldo = sum(in) - sum(out), jadi boleh minus.
        $this->assertSame(-250_000, $wallet->fresh()->balance);
    }
}
