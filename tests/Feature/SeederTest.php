<?php

namespace Tests\Feature;

use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_seeder_membuat_akun_admin(): void
    {
        $this->seed(\Database\Seeders\PetugasSeeder::class);

        $admin = Petugas::where('username', 'admin')->first();

        $this->assertNotNull($admin);
        $this->assertEquals('Administrator', $admin->nama_petugas);
        $this->assertEquals('admin', $admin->level);
        $this->assertTrue(Hash::check('admin123', $admin->password));
    }
}
