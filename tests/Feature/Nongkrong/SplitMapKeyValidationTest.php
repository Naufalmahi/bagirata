<?php

namespace Tests\Feature\Nongkrong;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Untuk split 'custom' dan 'percentage', nominal/persen dikirim sebagai map
 * key = user id. ExpenseService::create() mengambil participants dari key map
 * itu, BUKAN dari participant_ids. Kalau key-nya nggak dicocokin, validasi
 * "peserta harus anggota session" bisa dilewati dan orang yang bukan anggota
 * pun bisa dapat utang.
 */
class SplitMapKeyValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_split_key_harus_sama_dengan_peserta(): void
    {
        [$a, $b, $c, $session] = $this->sessionDenganTigaAnggota();

        $this->apiAs($a);

        // Key map = b dan c, tapi yang dideklarasikan peserta cuma a dan b.
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'custom',
            'participant_ids' => [$a->id, $b->id],
            'custom_amounts' => [
                $b->id => 30_000,
                $c->id => 20_000,
            ],
        ]))->assertStatus(422)->assertJsonValidationErrors('custom_amounts');

        $this->assertDatabaseCount('expense_splits', 0);
    }

    public function test_custom_split_boleh_pakai_key_orang_luar_session(): void
    {
        [$a, $b, , $session] = $this->sessionDenganTigaAnggota();
        $asing = User::factory()->create();

        $this->apiAs($a);

        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'custom',
            'participant_ids' => [$a->id, $b->id],
            'custom_amounts' => [
                $a->id => 25_000,
                $asing->id => 25_000,
            ],
        ]))->assertStatus(422)->assertJsonValidationErrors('custom_amounts');

        $this->assertDatabaseCount('expense_splits', 0);
    }

    public function test_custom_split_peserta_kurang_dari_map_ditolak(): void
    {
        [$a, $b, $c, $session] = $this->sessionDenganTigaAnggota();

        $this->apiAs($a);

        // 3 key untuk 2 peserta yang dideklarasikan, totalnya tetap pas 50rb.
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'custom',
            'participant_ids' => [$a->id, $b->id],
            'custom_amounts' => [
                $a->id => 20_000,
                $b->id => 10_000,
                $c->id => 20_000,
            ],
        ]))->assertStatus(422)->assertJsonValidationErrors('custom_amounts');

        $this->assertDatabaseCount('expense_splits', 0);
    }

    public function test_percentage_split_key_harus_sama_dengan_peserta(): void
    {
        [$a, $b, $c, $session] = $this->sessionDenganTigaAnggota();

        $this->apiAs($a);

        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'percentage',
            'participant_ids' => [$a->id, $b->id],
            'percentages' => [
                $b->id => 60,
                $c->id => 40,
            ],
        ]))->assertStatus(422)->assertJsonValidationErrors('percentages');

        $this->assertDatabaseCount('expense_splits', 0);
    }

    public function test_custom_split_dengan_key_benar_diterima(): void
    {
        [$a, $b, , $session] = $this->sessionDenganTigaAnggota();

        $this->apiAs($a);

        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'custom',
            'participant_ids' => [$b->id, $a->id],
            'custom_amounts' => [
                $b->id => 30_000,
                $a->id => 20_000,
            ],
        ]))->assertCreated();

        $this->assertDatabaseCount('expense_splits', 2);
        $this->assertDatabaseHas('expense_splits', ['user_id' => $b->id, 'share_amount' => 30_000]);
        $this->assertDatabaseHas('expense_splits', ['user_id' => $a->id, 'share_amount' => 20_000]);
    }

    public function test_percentage_split_dengan_key_benar_diterima(): void
    {
        [$a, $b, , $session] = $this->sessionDenganTigaAnggota();

        $this->apiAs($a);

        $this->postJson("/api/v1/sessions/{$session->id}/expenses", $this->payload($session, [
            'split_type' => 'percentage',
            'participant_ids' => [$b->id, $a->id],
            'percentages' => [
                $b->id => 25,
                $a->id => 75,
            ],
        ]))->assertCreated();

        $this->assertDatabaseCount('expense_splits', 2);
        $this->assertDatabaseHas('expense_splits', ['user_id' => $b->id, 'share_amount' => 12_500]);
        $this->assertDatabaseHas('expense_splits', ['user_id' => $a->id, 'share_amount' => 37_500]);
    }

    private function apiAs(User $user): User
    {
        \Laravel\Sanctum\Sanctum::actingAs($user);

        return $user;
    }

    private function payload($session, array $overrides): array
    {
        return array_merge([
            'name' => 'Split custom',
            'amount' => 50_000,
            'paid_by_user_id' => $session->user_id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
        ], $overrides);
    }

    private function sessionDenganTigaAnggota(): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();

        $this->apiAs($a);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Session #'.uniqid(),
            'date' => '2026-09-23',
            'member_ids' => [$b->id, $c->id],
        ]);
        $response->assertCreated();

        return [$a, $b, $c, \App\Models\NongkrongSession::findOrFail($response->json('data.id'))];
    }
}
