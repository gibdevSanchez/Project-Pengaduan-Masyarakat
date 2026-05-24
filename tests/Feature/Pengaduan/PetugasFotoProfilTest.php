<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetugasFotoProfilTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Admin Test',
            'username'     => 'admintest',
            'password'     => bcrypt('pass'),
            'telp'         => '08100000000',
            'level'        => 'admin',
        ]);
    }

    private function makePetugas(array $attrs = []): Petugas
    {
        return Petugas::create(array_merge([
            'nama_petugas' => 'Budi Santoso',
            'username'     => 'budi',
            'password'     => bcrypt('pass'),
            'telp'         => '08111111111',
            'level'        => 'petugas',
        ], $attrs));
    }

    public function test_halaman_admin_petugas_menampilkan_foto_profil_jika_ada(): void
    {
        Storage::fake('public');
        $admin   = $this->makeAdmin();
        $petugas = $this->makePetugas(['foto_profil' => 'profil/budi.jpg']);

        $response = $this->actingAs($admin, 'petugas')
                         ->get(route('admin.petugas.index'));

        $response->assertStatus(200);
        $response->assertSee(Storage::url('profil/budi.jpg'));
    }

    public function test_halaman_admin_petugas_tampilkan_inisial_jika_tidak_ada_foto(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = $this->makePetugas(); // no foto_profil

        $response = $this->actingAs($admin, 'petugas')
                         ->get(route('admin.petugas.index'));

        $response->assertStatus(200);
        // Inisial huruf pertama nama petugas harus muncul di avatar fallback
        $response->assertSee(strtoupper(substr($petugas->nama_petugas, 0, 1)));
        $response->assertDontSee('profil/');
    }

    public function test_foto_profil_tidak_muncul_jika_null(): void
    {
        $admin   = $this->makeAdmin();
        $petugas = $this->makePetugas(['foto_profil' => null]);

        $response = $this->actingAs($admin, 'petugas')
                         ->get(route('admin.petugas.index'));

        $response->assertStatus(200);
        $response->assertDontSee('<img', false);
    }
}
