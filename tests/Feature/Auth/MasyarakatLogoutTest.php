<?php

namespace Tests\Feature\Auth;

use App\Models\Masyarakat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasyarakatLogoutTest extends TestCase
{
    use RefreshDatabase;

    private function buatMasyarakat(): Masyarakat
    {
        return Masyarakat::create([
            'nik'      => '3201234567890001',
            'nama'     => 'Warga Test',
            'username' => 'wargalogout',
            'password' => Hash::make('password123'),
            'telp'     => '08111222333',
        ]);
    }

    public function test_masyarakat_dapat_logout_dan_redirect_ke_login(): void
    {
        $user = $this->buatMasyarakat();

        $this->actingAs($user, 'masyarakat')
             ->post('/logout')
             ->assertRedirect('/login');
    }

    public function test_setelah_logout_sesi_masyarakat_tidak_aktif(): void
    {
        $user = $this->buatMasyarakat();

        $this->actingAs($user, 'masyarakat')
             ->post('/logout');

        $this->assertGuest('masyarakat');
    }

    public function test_masyarakat_tidak_bisa_akses_dashboard_setelah_logout(): void
    {
        $user = $this->buatMasyarakat();

        $this->actingAs($user, 'masyarakat')->post('/logout');

        $this->get('/masyarakat/dashboard')
             ->assertRedirect();
    }

    public function test_logout_tanpa_login_tetap_redirect_ke_login(): void
    {
        $this->post('/logout')
             ->assertRedirect('/login');
    }
}
