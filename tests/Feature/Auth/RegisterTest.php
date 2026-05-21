<?php

namespace Tests\Feature\Auth;

use App\Models\Masyarakat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_register_dapat_diakses(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_masyarakat_berhasil_registrasi(): void
    {
        $this->post('/register', [
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'username' => 'budisantoso',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'telp' => '081234567890',
        ])->assertRedirect('/masyarakat/dashboard');

        $this->assertDatabaseHas('masyarakat', ['nik' => '1234567890123456']);
    }

    public function test_registrasi_gagal_nik_duplikat(): void
    {
        Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'Existing',
            'username' => 'existing1',
            'password' => Hash::make('pass'),
            'telp' => '08123',
        ]);

        $this->post('/register', [
            'nik' => '1234567890123456',
            'nama' => 'New User',
            'username' => 'newuser',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'telp' => '08123456789',
        ])->assertSessionHasErrors('nik');
    }

    public function test_registrasi_gagal_username_duplikat(): void
    {
        Masyarakat::create([
            'nik' => '1111111111111111',
            'nama' => 'Existing',
            'username' => 'samausername',
            'password' => Hash::make('pass'),
            'telp' => '08123',
        ]);

        $this->post('/register', [
            'nik' => '2222222222222222',
            'nama' => 'New User',
            'username' => 'samausername',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'telp' => '08123456789',
        ])->assertSessionHasErrors('username');
    }
}
