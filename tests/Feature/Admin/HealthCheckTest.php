<?php

namespace Tests\Feature\Admin;

use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Admin',
            'username'     => 'admin',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'admin',
        ]);
    }

    public function test_admin_can_access_health_endpoint(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.health'))
            ->assertOk();
    }

    public function test_health_response_has_expected_structure(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.health'))
            ->assertOk()
            ->assertJsonStructure([
                'db'      => ['ok', 'label', 'driver', 'name'],
                'storage' => ['ok', 'used_mb', 'total_gb', 'free_gb', 'used_pct'],
                'queue'   => ['ok', 'pending', 'failed'],
                'app'     => ['php_version', 'laravel_version', 'environment', 'debug', 'recent_errors'],
            ]);
    }

    public function test_health_db_reports_ok(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.health'))
            ->assertJsonPath('db.ok', true);
    }

    public function test_non_admin_cannot_access_health_endpoint(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($petugas, 'petugas')
            ->getJson(route('admin.utilitas.health'))
            ->assertRedirect();
    }

    public function test_guest_cannot_access_health_endpoint(): void
    {
        $this->getJson(route('admin.utilitas.health'))
            ->assertRedirect();
    }
}
