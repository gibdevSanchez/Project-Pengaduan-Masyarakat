<?php

namespace Tests\Feature\Admin;

use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class LogTest extends TestCase
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

    public function test_admin_can_access_log_endpoint(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.logs'))
            ->assertOk();
    }

    public function test_log_response_is_paginated(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.logs'))
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
                'per_page',
            ]);
    }

    public function test_log_entry_has_expected_fields(): void
    {
        $admin = $this->makeAdmin();

        activity('sistem')->log('Test log entry');

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.logs'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'type', 'description', 'causer', 'ip', 'extra', 'created_at', 'created_diff']],
            ]);
    }

    public function test_log_type_filter_works(): void
    {
        $admin = $this->makeAdmin();

        activity('pengaduan')->log('Pengaduan action');
        activity('sistem')->log('Sistem action');

        $response = $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.logs', ['type' => 'pengaduan']))
            ->assertOk();

        $data = $response->json('data');
        $this->assertTrue(collect($data)->every(fn($item) => $item['type'] === 'pengaduan'));
    }

    public function test_guest_cannot_access_log_endpoint(): void
    {
        $this->getJson(route('admin.utilitas.logs'))
            ->assertRedirect();
    }
}
