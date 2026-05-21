<?php

namespace Tests\Feature\Auth;

use App\Models\Masyarakat;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_dapat_diakses(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_admin_login_dan_redirect_ke_admin_dashboard(): void
    {
        Petugas::create([
            'nama_petugas' => 'Admin',
            'username' => 'admintest',
            'password' => Hash::make('admin123'),
            'telp' => '08000000000',
            'level' => 'admin',
        ]);

        $this->post('/login', [
            'username' => 'admintest',
            'password' => 'admin123',
            'guard' => 'petugas',
        ])->assertRedirect('/admin/dashboard');
    }

    public function test_petugas_login_dan_redirect_ke_petugas_dashboard(): void
    {
        Petugas::create([
            'nama_petugas' => 'Petugas',
            'username' => 'petugastest',
            'password' => Hash::make('pass123'),
            'telp' => '08000000000',
            'level' => 'petugas',
        ]);

        $this->post('/login', [
            'username' => 'petugastest',
            'password' => 'pass123',
            'guard' => 'petugas',
        ])->assertRedirect('/petugas/dashboard');
    }

    public function test_masyarakat_login_dan_redirect_ke_masyarakat_dashboard(): void
    {
        Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'Warga',
            'username' => 'warga1',
            'password' => Hash::make('warga123'),
            'telp' => '08123456789',
        ]);

        $this->post('/login', [
            'username' => 'warga1',
            'password' => 'warga123',
            'guard' => 'masyarakat',
        ])->assertRedirect('/masyarakat/dashboard');
    }

    public function test_kredensial_salah_dikembalikan_ke_login(): void
    {
        $this->post('/login', [
            'username' => 'tidakada',
            'password' => 'salah',
            'guard' => 'petugas',
        ])->assertRedirect('/login')
          ->assertSessionHasErrors('username');
    }
}
