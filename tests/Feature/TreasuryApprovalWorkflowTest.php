<?php

namespace Tests\Feature;

use App\Exceptions\BusinessException;
use App\Models\GroupMember;
use App\Models\User;
use App\Models\WalletEntry;
use App\Services\GroupService;
use App\Services\TreasuryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Saldo wallet harus selalu = sum(approved in) - sum(approved out), berapa pun
 * kali status entry berubah. Reject entry yang sudah approved wajib membalik
 * saldo, kalau tidak uangnya ngaku 'keluar' padahal transactiodibatalkan.
 */
class TreasuryApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_reject_entry_pending_tidak_mengubah_saldo(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::rejectEntry($f['entry'], $f['owner']);

        $this->assertSame('rejected', $f['entry']->fresh()->status);
        $this->assertSame(0, $f['wallet']->fresh()->balance);
    }

    public function test_reject_entry_yang_sudah_approved_membalik_saldo(): void
    {
        $f = $this->setupTreasury();

        // Owner == pencatat, jadi entry auto-approved dan saldo langsung bergerak.
        $auto = TreasuryService::recordEntry($f['wallet'], $f['owner'], [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => 120_000,
            'description' => 'Iuran owner',
        ]);

        $this->assertSame('approved', $auto->status);
        $this->assertSame(120_000, $f['wallet']->fresh()->balance);

        // Owner berarti entah ini belum dicek, jadi saldo harus balik.
        TreasuryService::rejectEntry($auto, $f['owner']);

        $this->assertSame('rejected', $auto->fresh()->status);
        $this->assertSame(0, $f['wallet']->fresh()->balance);
    }

    public function test_reject_dua_kali_tidak_membalik_saldo_ganda(): void
    {
        $f = $this->setupTreasury();

        $auto = TreasuryService::recordEntry($f['wallet'], $f['owner'], [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => 90_000,
            'description' => 'Iuran owner',
        ]);
        $this->assertSame(90_000, $f['wallet']->fresh()->balance);

        TreasuryService::rejectEntry($auto, $f['owner']);
        $this->assertSame(0, $f['wallet']->fresh()->balance);

        // Panggilan kedua harus no-op, bukan nambah saldo atau negate lagi.
        TreasuryService::rejectEntry($auto->fresh(), $f['owner']);
        $this->assertSame(0, $f['wallet']->fresh()->balance);
    }

    public function test_approve_entry_yang_sudah_ditolak_ditolak(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::rejectEntry($f['entry'], $f['owner']);
        $this->assertSame('rejected', $f['entry']->fresh()->status);

        $this->expectException(BusinessException::class);

        TreasuryService::approveEntry($f['entry']->fresh(), $f['owner']);
    }

    public function test_approve_entry_yang_sudah_approved_tidak_menambah_saldo_ganda(): void
    {
        $f = $this->setupTreasury();

        $auto = TreasuryService::recordEntry($f['wallet'], $f['owner'], [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => 60_000,
            'description' => 'Iuran owner',
        ]);
        $this->assertSame(60_000, $f['wallet']->fresh()->balance);

        TreasuryService::approveEntry($auto, $f['owner']);

        $this->assertSame(60_000, $f['wallet']->fresh()->balance);
    }

    public function test_review_metadata_terisi_saat_approve(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::approveEntry($f['entry'], $f['owner'], 'Bukti udah cocok');

        $entry = $f['entry']->fresh();
        $this->assertSame('approved', $entry->status);
        $this->assertSame($f['owner']->id, $entry->approved_by);
        $this->assertSame($f['owner']->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);
        $this->assertSame('Bukti udah cocok', $entry->review_note);
    }

    public function test_review_metadata_terisi_saat_reject_dan_approved_by_kosong(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::rejectEntry($f['entry'], $f['owner'], 'Bukti kurang jelas');

        $entry = $f['entry']->fresh();
        $this->assertSame('rejected', $entry->status);
        $this->assertNull($entry->approved_by, 'approved_by jangan diisi saat entry ditolak.');
        $this->assertSame($f['owner']->id, $entry->reviewed_by);
        $this->assertNotNull($entry->reviewed_at);
        $this->assertSame('Bukti kurang jelas', $entry->review_note);
    }

    public function test_saldo_konsisten_setelah_banyak_perubahan_status(): void
    {
        $f = $this->setupTreasury();

        $entries = [];
        foreach ([['in', 200_000], ['out', 50_000], ['in', 30_000]] as $i => [$type, $amount]) {
            $entries[] = TreasuryService::recordEntry($f['wallet'], $f['member'], [
                'type' => $type,
                'category' => 'iuran',
                'amount' => $amount,
                'description' => 'Entry #'.$i,
            ]);
        }

        foreach ($entries as $entry) {
            TreasuryService::approveEntry($entry, $f['owner']);
        }
        $this->assertSame(180_000, $f['wallet']->fresh()->balance);

        // Tolak yang out 50rb => saldo balik ke 230rb.
        TreasuryService::rejectEntry($entries[1]->fresh(), $f['owner']);
        $this->assertSame(230_000, $f['wallet']->fresh()->balance);

        // Tolak semua => saldo 0.
        TreasuryService::rejectEntry($entries[0]->fresh(), $f['owner']);
        TreasuryService::rejectEntry($entries[2]->fresh(), $f['owner']);
        $this->assertSame(0, $f['wallet']->fresh()->balance);
    }

    /**
     * Simulasi dua request approve yang barengan.
     *
     * Request kedua masih pegang model basahi berstatus 'pending' dari luar
     * transaksi, padahal baris di DB sudah 'approved'. Kalau service cuma
     * percaya model yang masuk, guard terlewati dan saldo nambah dua kali.
     */
    public function test_approve_dengan_model_basah_tidak_menambah_saldo_ganda(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::approveEntry($f['entry'], $f['owner']);
        $this->assertSame(50_000, $f['wallet']->fresh()->balance);

        $basah = WalletEntry::findOrFail($f['entry']->id);
        $basah->status = 'pending';

        TreasuryService::approveEntry($basah, $f['owner']);

        $this->assertSame(50_000, $f['wallet']->fresh()->balance, 'Saldo nambah dua kali.');
        $this->assertSame(1, WalletEntry::where('status', 'approved')->count());
    }

    /**
     * Sama untuk reject: request kedua masih pegang status 'approved' basahi
     * padahal DB sudah 'rejected', jadi saldo jangan dibalik dua kali.
     */
    public function test_reject_dengan_model_basah_tidak_membalik_saldo_ganda(): void
    {
        $f = $this->setupTreasury();

        TreasuryService::approveEntry($f['entry'], $f['owner']);
        $this->assertSame(50_000, $f['wallet']->fresh()->balance);

        TreasuryService::rejectEntry($f['entry']->fresh(), $f['owner']);
        $this->assertSame(0, $f['wallet']->fresh()->balance);

        $basah = WalletEntry::findOrFail($f['entry']->id);
        $basah->status = 'approved';

        TreasuryService::rejectEntry($basah, $f['owner']);

        $this->assertSame(0, $f['wallet']->fresh()->balance, 'Saldo dibalik dua kali.');
    }

    /**
     * @return array<string, mixed>
     */
    private function setupTreasury(): array
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $group = GroupService::create($owner, ['name' => 'Geng Kas']);
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $member->id,
            'role' => 'member',
        ]);

        $wallet = TreasuryService::getOrCreateWallet($group);

        return [
            'owner' => $owner,
            'member' => $member,
            'group' => $group,
            'wallet' => $wallet,
            'entry' => TreasuryService::recordEntry($wallet, $member, [
                'type' => 'in',
                'category' => 'iuran',
                'amount' => 50_000,
                'description' => 'Iuran anggota',
            ]),
        ];
    }
}
