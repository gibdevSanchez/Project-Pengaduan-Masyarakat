<?php

namespace Tests\Feature\Api;

use App\Models\Masyarakat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_bisa_login_via_api(): void
    {
        Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'API User',
            'username' => 'apiuser',
            'password' => Hash::make('password123'),
            'telp' => '08123456789',
        ]);

        $this->postJson('/api/login', [
            'username' => 'apiuser',
            'password' => 'password123',
        ])->assertStatus(200)
          ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_api_gagal_dengan_kredensial_salah(): void
    {
        $this->postJson('/api/login', [
            'username' => 'tidakada',
            'password' => 'salah',
        ])->assertStatus(401);
    }

    public function test_masyarakat_bisa_registrasi_via_api(): void
    {
        $this->postJson('/api/register', [
            'nik' => '1234567890123456',
            'nama' => 'API User',
            'username' => 'apiuser',
            'password' => 'password123',
            'telp' => '08123456789',
        ])->assertStatus(201)
          ->assertJsonStructure(['token', 'user']);

        $this->assertDatabaseHas('masyarakat', ['nik' => '1234567890123456']);
    }

    public function test_pengguna_terautentikasi_bisa_logout_via_api(): void
    {
        $masyarakat = Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'API User',
            'username' => 'apiuser',
            'password' => Hash::make('password123'),
            'telp' => '08123456789',
        ]);

        $token = $masyarakat->createToken('android')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
             ->postJson('/api/logout')
             ->assertStatus(200);
    }
}
