<?php

namespace Tests\Feature\Admin;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilitasPetaDataTest extends TestCase
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

    public function test_admin_can_get_peta_data(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.peta-data'))
            ->assertOk();
    }

    public function test_peta_data_only_includes_entries_with_coordinates(): void
    {
        $admin = $this->makeAdmin();

        Pengaduan::create([
            'isi_laporan' => 'Ada koordinat',
            'status'      => 'menunggu',
            'kategori'    => 'lainnya',
            'lat'         => '-6.200000',
            'lng'         => '106.816666',
        ]);

        Pengaduan::create([
            'isi_laporan' => 'Tidak ada koordinat',
            'status'      => 'menunggu',
            'kategori'    => 'lainnya',
        ]);

        $response = $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.peta-data'))
            ->assertOk();

        $this->assertCount(1, $response->json());
    }

    public function test_peta_data_entry_has_expected_fields(): void
    {
        $admin = $this->makeAdmin();

        Pengaduan::create([
            'isi_laporan' => 'Laporan dengan koordinat',
            'status'      => 'menunggu',
            'kategori'    => 'infrastruktur',
            'lat'         => '-6.200000',
            'lng'         => '106.816666',
            'lokasi'      => 'Jl. Sudirman No. 1',
        ]);

        $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.peta-data'))
            ->assertOk()
            ->assertJsonStructure([['lat', 'lng', 'kategori', 'status', 'lokasi', 'tgl']]);
    }

    public function test_peta_data_returns_float_coordinates(): void
    {
        $admin = $this->makeAdmin();

        Pengaduan::create([
            'isi_laporan' => 'Laporan dengan koordinat',
            'status'      => 'menunggu',
            'kategori'    => 'lainnya',
            'lat'         => '-6.200000',
            'lng'         => '106.816666',
        ]);

        $response = $this->actingAs($admin, 'petugas')
            ->getJson(route('admin.utilitas.peta-data'))
            ->assertOk();

        $this->assertIsFloat($response->json('0.lat'));
        $this->assertIsFloat($response->json('0.lng'));
    }

    public function test_guest_cannot_access_peta_data(): void
    {
        $this->getJson(route('admin.utilitas.peta-data'))
            ->assertRedirect();
    }
}
