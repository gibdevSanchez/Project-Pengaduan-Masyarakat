<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintCategoryLocationTest extends TestCase
{
    use RefreshDatabase;

    private function makeMasyarakat(): Masyarakat
    {
        return Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);
    }

    public function test_store_complaint_with_kategori_and_lokasi(): void
    {
        $user = $this->makeMasyarakat();

        $response = $this->actingAs($user, 'masyarakat')->post(route('masyarakat.pengaduan.store'), [
            'isi_laporan' => 'Ada jalan berlubang di depan pasar',
            'kategori'    => 'infrastruktur',
            'lokasi'      => 'Jl. Merdeka No. 10',
        ]);

        $response->assertRedirect(route('masyarakat.dashboard'));
        $this->assertDatabaseHas('pengaduan', [
            'isi_laporan' => 'Ada jalan berlubang di depan pasar',
            'kategori'    => 'infrastruktur',
            'lokasi'      => 'Jl. Merdeka No. 10',
            'nik'         => '1234567890123456',
        ]);
    }

    public function test_store_without_kategori_fails_validation(): void
    {
        $user = $this->makeMasyarakat();

        $response = $this->actingAs($user, 'masyarakat')->post(route('masyarakat.pengaduan.store'), [
            'isi_laporan' => 'Ada jalan berlubang',
        ]);

        $response->assertSessionHasErrors('kategori');
    }

    public function test_store_with_invalid_kategori_fails(): void
    {
        $user = $this->makeMasyarakat();

        $response = $this->actingAs($user, 'masyarakat')->post(route('masyarakat.pengaduan.store'), [
            'isi_laporan' => 'Ada jalan berlubang',
            'kategori'    => 'not-valid',
        ]);

        $response->assertSessionHasErrors('kategori');
    }

    public function test_anonim_store_uses_null_nik(): void
    {
        $user = $this->makeMasyarakat();

        $this->actingAs($user, 'masyarakat')->post(route('masyarakat.pengaduan.store'), [
            'isi_laporan' => 'Pengaduan anonim tentang sampah',
            'kategori'    => 'lingkungan',
            'anonim'      => '1',
        ]);

        $this->assertDatabaseHas('pengaduan', [
            'isi_laporan' => 'Pengaduan anonim tentang sampah',
            'nik'         => null,
        ]);
    }
}
