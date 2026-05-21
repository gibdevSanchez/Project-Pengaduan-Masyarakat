<?php

namespace Tests\Feature\Middleware;

use App\Models\Masyarakat;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MiddlewareAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_tidak_bisa_akses_dashboard_admin(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_tamu_tidak_bisa_akses_dashboard_petugas(): void
    {
        $response = $this->get('/petugas/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_tamu_tidak_bisa_akses_dashboard_masyarakat(): void
    {
        $response = $this->get('/masyarakat/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_bisa_akses_dashboard_admin(): void
    {
        $admin = Petugas::create([
            'nama_petugas' => 'Admin Test',
            'username' => 'admintest',
            'password' => Hash::make('admin123'),
            'telp' => '08000000000',
            'level' => 'admin',
        ]);

        $response = $this->actingAs($admin, 'petugas')->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_petugas_tidak_bisa_akses_dashboard_admin(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Test',
            'username' => 'petugastest',
            'password' => Hash::make('password'),
            'telp' => '08000000000',
            'level' => 'petugas',
        ]);

        $response = $this->actingAs($petugas, 'petugas')->get('/admin/dashboard');
        $response->assertRedirect('/petugas/dashboard');
    }

    public function test_petugas_bisa_akses_dashboard_petugas(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Test',
            'username' => 'petugastest',
            'password' => Hash::make('password'),
            'telp' => '08000000000',
            'level' => 'petugas',
        ]);

        $response = $this->actingAs($petugas, 'petugas')->get('/petugas/dashboard');
        $response->assertStatus(200);
    }

    public function test_masyarakat_bisa_akses_dashboard_masyarakat(): void
    {
        $masyarakat = Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'Warga Test',
            'username' => 'wargaTest',
            'password' => Hash::make('password'),
            'telp' => '08123456789',
        ]);

        $response = $this->actingAs($masyarakat, 'masyarakat')->get('/masyarakat/dashboard');
        $response->assertStatus(200);
    }
}
