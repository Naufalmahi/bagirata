<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

/**
 * Blade memanggil /api/v1 lewat cookie sesi, bukan bearer token. Itu hanya
 * bisa jalan kalau EnsureFrontendRequestsAreStateful aktif di middleware group
 * 'api'. Kalau tidak, group 'api' tidak punya StartSession sehingga cookie sesi
 * tidak terbaca dan seluruh panggilan API dari browser dapat 401:
 * friend picker untuk membuat session adhoc tidak bisa dipakai, dan halaman
 * kalkulator selalu gagal.
 *
 * Test suite lama tidak menangkap ini karena memakai Sanctum::actingAs(),
 * yang menyuntik guard secara langsung dan sama sekali tidak melewati jalur
 * cookie. Test di bawah memakai cookie sesi asli hasil login web.
 *
 * Catatan: VerifyCsrfToken dilewati otomatis saat testing (runningUnitTests()),
 * jadi proteksi CSRF pada jalur stateful tidak bisa dibuktikan di sini dan
 * harus diverifikasi manual lewat browser.
 */
class StatefulApiSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_api_memakai_middleware_sanctum_stateful(): void
    {
        $groups = app(\App\Http\Kernel::class)->getMiddlewareGroups();

        $this->assertContains(
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            $groups['api'] ?? [],
            'Group middleware "api" harus memakai EnsureFrontendRequestsAreStateful, '
            .'kalau tidak cookie sesi tidak bisa mengautentikasi /api/v1.'
        );
    }

    public function test_cookie_sesi_dari_login_web_bisa_akses_api(): void
    {
        $user = User::factory()->create(['name' => 'Zulfikar Pratama']);
        User::factory()->create(['name' => 'Wawan Suwandi']);

        $cookie = $this->loginDanAmbilCookieSesi($user);

        $this->withCookie($cookie->getName(), $cookie->getValue())
            ->withHeaders(['Referer' => 'http://localhost/nongkrong/create', 'Accept' => 'application/json'])
            ->getJson('/api/v1/users?q=Wawan')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Wawan Suwandi');
    }

    public function test_kalkulator_split_bisa_dipakai_dari_browser(): void
    {
        $user = User::factory()->create();
        $cookie = $this->loginDanAmbilCookieSesi($user);

        $response = $this->withCookie($cookie->getName(), $cookie->getValue())
            ->withHeaders(['Referer' => 'http://localhost/calculator', 'Accept' => 'application/json'])
            ->postJson('/api/v1/calculate-split', [
                'subtotal' => 100_000,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'service_rate' => 0,
                'tax_rate' => 0,
                'people_count' => 3,
            ]);

        $response->assertOk();

        // shares di-key oleh id peserta, jadi JSON-nya object bukan list.
        $shares = $response->json('data.shares');
        $this->assertCount(3, $shares);
        $this->assertSame(100_000, array_sum($shares));
        $this->assertSame(33_334, $shares['1']);
    }

    public function test_tanpa_cookie_sesi_api_masih_401(): void
    {
        $this->withHeaders(['Referer' => 'http://localhost/nongkrong/create', 'Accept' => 'application/json'])
            ->getJson('/api/v1/users?q=Wawan')
            ->assertUnauthorized();
    }

    /**
     * Dua test di bawah menguji fromFrontend() secara langsung, bukan lewat
     * respons 401.
     *
     * Alasannya: session driver saat testing adalah 'array', jadi store-nya
     * masih berisi user hasil login meski StartSession tidak dijalankan pada
     * request non-stateful. Guard 'web' membaca store itu dan tetap
     * mengautentikasi, sehingga responsnya 200 dan test negatif apa pun yang
     * mengandalkan respons HTTP akan salah. Memaksa store jadi kosong justru
     * membuat cookie tidak bisa diuji. Menguji keputusan "apakah request ini
     * stateful" secara langsung menghindari jebakan itu.
     */
    public function test_origin_asing_tidak_dianggap_stateful(): void
    {
        $this->assertFalse(
            $this->dariFrontend('http://penyerang.example.com/attack'),
            'Origin asing tidak boleh diperlakukan stateful, kalau tidak cookie sesi bisa dicuri lintas domain.'
        );
    }

    public function test_request_tanpa_referer_atau_origin_tidak_stateful(): void
    {
        $this->assertFalse($this->dariFrontend(null));
    }

    public function test_domain_asli_terdaftar_stateful(): void
    {
        $this->assertTrue(
            $this->dariFrontend('http://localhost/nongkrong/create'),
            'Domain aplikasi sendiri harus dianggap stateful, kalau tidak /api/v1 tidak bisa dipanggil dari Blade.'
        );
    }

    public function test_domain_test_terdaftar_sebagai_stateful(): void
    {
        // Kalau domain test tidak masuk daftar stateful, test di atas diam-diam
        // hanya menguji 401 dan tidak pernah benar-benar memverifikasi jalur cookie.
        $stateful = config('sanctum.stateful');

        $this->assertNotEmpty(
            array_filter($stateful, fn ($domain) => str_contains($domain, 'localhost')),
            'Domain test harus terdaftar di SANCTUM_STATEFUL_DOMAINS.'
        );
    }

    private function dariFrontend(?string $origin): bool
    {
        $request = Request::create('/api/v1/users', 'GET');

        if ($origin !== null) {
            $request->headers->set('Referer', $origin);
        }

        return EnsureFrontendRequestsAreStateful::fromFrontend($request);
    }

    private function loginDanAmbilCookieSesi(User $user): Cookie
    {
        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $cookie = $this->ambilCookie($response, (string) config('session.cookie'));

        // Guard masih memegang user dari request login dalam container yang sama.
        // Di browser tiap request adalah proses PHP baru, jadi guard selalu mulai
        // kosong. Tanpa di-reset, test negatif di bawah akan selalu dapat 200
        // karena autentikasi berhasil lewat guard, bukan lewat cookie.
        $this->app['auth']->forgetGuards();

        return $cookie;
    }

    private function ambilCookie(TestResponse $response, string $name): Cookie
    {
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $name) {
                return $cookie;
            }
        }

        $this->fail("Cookie sesi '{$name}' tidak ditemukan di respons login.");
    }
}
