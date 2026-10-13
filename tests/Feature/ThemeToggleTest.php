<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian bahwa fitur mode malam telah dihapus sesuai permintaan pengguna:
 * tombol theme toggle dan script anti-flash tidak lagi dimuat di aplikasi.
 */
class ThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    private function html(): string
    {
        return (string) file_get_contents(resource_path('views/layouts/app.blade.php'));
    }

    public function test_mode_malam_telah_dihapus_dari_layout(): void
    {
        $html = $this->html();

        $this->assertStringNotContainsString('$store.theme.toggle()', $html);
        $this->assertStringNotContainsString('bagirata-theme', $html);
    }

    public function test_tombol_tema_tidak_tampil_untuk_tamu_dan_pengguna(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee('$store.theme.toggle()', false);

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('$store.theme.toggle()', false);
    }

    public function test_halaman_tamu_punya_link_masuk_dan_daftar(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('login'))
            ->assertSee(route('register'));
    }

    public function test_navigasi_aktif_dapat_penanda(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('nongkrong.index'))
            ->assertOk()
            ->assertSee('nav-link-active', false);
    }
}
