<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Dark mode pakai kelas `dark` di <html> (Tailwind `darkMode: 'class'`), bukan
 * `@media (prefers-color-scheme)`. Alasannya, override manual dari pengguna
 * harus bisa menang atas preferensi sistem.
 *
 * Konsekuensinya ada dua titik rapuh yang diuji di sini:
 *
 *  1. Inline script anti-flash di <head> harus tetap sinkron dan tidak
 *     bergantung Alpine. Kalau hilang, halaman berkedip putih (FOUC) setiap
 *     reload untuk pengguna mode gelap.
 *  2. Kunci localStorage di Blade harus sama dengan THEME_KEY di app.js,
 *     kalau tidak pilihan user hilang tiap refresh.
 */
class ThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    private function html(): string
    {
        return (string) file_get_contents(resource_path('views/layouts/app.blade.php'));
    }

    public function test_layout_memuat_script_anti_flash_sebelum_body(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('prefers-color-scheme: dark', false);
    }

    public function test_script_anti_flash_tidak_bergantung_alpine(): void
    {
        $html = $this->html();

        $this->assertStringContainsString('localStorage.getItem', $html);
        $this->assertStringNotContainsString('@click', substr($html, 0, (int) strpos($html, '</head>')),
            'Script anti-flash tidak boleh memakai Alpine: Alpine dimuat lewat @vite dan sudah terlambat.');
    }

    public function test_kunci_localstorage_blade_sama_dengan_app_js(): void
    {
        $js = (string) file_get_contents(resource_path('js/app.js'));

        $this->assertMatchesRegularExpression(
            "/THEME_KEY\s*=\s*'([^']+)'/",
            $js,
            'app.js harus mendeklarasikan THEME_KEY sebagai literal string.'
        );

        preg_match("/THEME_KEY\s*=\s*'([^']+)'/", $js, $m);

        $this->assertStringContainsString(
            "'{$m[1]}'",
            $this->html(),
            "Kunci localStorage di Blade ({$m[1]}) harus sama dengan THEME_KEY di app.js, "
            .'kalau tidak preferensi tema user hilang tiap refresh.'
        );
    }

    public function test_tombol_tema_tampil_untuk_tamu_dan_pengguna(): void
    {
        // Halaman marketing pun perlu hormati mode gelap, jadi tombolnya di luar
        // blok @auth dan harus muncul untuk tamu juga.
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('$store.theme.toggle()', false);

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('$store.theme.toggle()', false);
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
