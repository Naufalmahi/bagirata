<?php

namespace Tests\Feature\Nongkrong;

use App\Models\User;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Halaman edit membaca $expense->split_type, padahal tabel expenses tidak punya
 * kolom itu — split_type disimpan per baris di expense_splits. Nilai jadi null,
 * Alpine jatuh ke fallback 'equal', dan expense yang asalnya custom/persen
 * diam-diam tersimpan ulang jadi rata-rata saat user edit lalu simpan.
 */
class ExpenseEditSplitTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_expense_persen_prefill_split_type_persen(): void
    {
        [$a, $b, $session] = $this->sessionDenganAnggota();

        $expense = $this->buatExpense($a, $session, $b, [
            'split_type' => 'percentage',
            'percentages' => [$a->id => 60, $b->id => 40],
        ]);

        $this->actingAs($a)
            ->get(route('expenses.edit', [$session, $expense]))
            ->assertOk()
            ->assertSee($this->splitTypeDiHtml('percentage'), false);
    }

    public function test_edit_expense_nominal_bebas_prefill_custom(): void
    {
        [$a, $b, $session] = $this->sessionDenganAnggota();

        $expense = $this->buatExpense($a, $session, $b, [
            'split_type' => 'custom',
            'custom_amounts' => [$a->id => 70_000, $b->id => 30_000],
        ]);

        $this->actingAs($a)
            ->get(route('expenses.edit', [$session, $expense]))
            ->assertOk()
            ->assertSee($this->splitTypeDiHtml('custom'), false);
    }

    public function test_edit_expense_rata_rata_prefill_equal(): void
    {
        [$a, $b, $session] = $this->sessionDenganAnggota();

        $expense = $this->buatExpense($a, $session, $b, ['split_type' => 'equal']);

        $this->actingAs($a)
            ->get(route('expenses.edit', [$session, $expense]))
            ->assertOk()
            ->assertSee($this->splitTypeDiHtml('equal'), false);
    }

    public function test_prefill_tidak_kalah_oleh_nilai_lama_dari_validasi_gagal(): void
    {
        [$a, $b, $session] = $this->sessionDenganAnggota();

        $expense = $this->buatExpense($a, $session, $b, [
            'split_type' => 'percentage',
            'percentages' => [$a->id => 60, $b->id => 40],
        ]);

        // Submit gagal validasi dengan split_type lain: yang tampil harus nilai lama.
        $this->actingAs($a)
            ->from(route('expenses.edit', [$session, $expense]))
            ->post(route('expenses.preview', $session), [
                'nongkrong_session_id' => $session->id,
                'name' => 'x',
                'amount' => -5,
                'paid_by_user_id' => $a->id,
                'category' => 'makan',
                'split_type' => 'equal',
                'participant_ids' => [$a->id, $b->id],
            ]);

        $this->actingAs($a)
            ->get(route('expenses.edit', [$session, $expense]))
            ->assertOk()
            ->assertSee($this->splitTypeDiHtml('equal'), false);
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function buatExpense(User $payer, $session, User $member, array $extra)
    {
        $this->actingAs($payer)->post(route('expenses.store', $session), array_merge([
            'name' => 'Split uji',
            'amount' => 100_000,
            'paid_by_user_id' => $payer->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$payer->id, $member->id],
        ], $extra))->assertRedirect();

        return $session->expenses()->latest('id')->firstOrFail();
    }

    /**
     * Blade @js() membungkus payload sebagai JSON.parse('...') dan mengganti
     * tanda kutip dengan \u0022, jadi pola yang dicari harus mengikuti itu.
     */
    private function splitTypeDiHtml(string $splitType): string
    {
        return '\u0022split_type\u0022:\u0022'.$splitType.'\u0022';
    }

    private function sessionDenganAnggota(): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = SessionService::create($a, [
            'name' => 'Edit split '.\uniqid(),
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$b->id],
        ]);

        return [$a, $b, $session];
    }
}
