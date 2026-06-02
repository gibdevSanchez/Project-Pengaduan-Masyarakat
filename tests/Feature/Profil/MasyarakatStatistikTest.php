<?php

namespace Tests\Feature\Profil;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasyarakatStatistikTest extends TestCase
{
    use RefreshDatabase;

    private Masyarakat $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);
    }

    public function test_profil_page_shows_total_laporan(): void
    {
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P1', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P2', 'status' => 'selesai', 'kategori' => 'lainnya']);

        $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.profil'))
            ->assertOk()
            ->assertViewHas('stats', fn($s) => $s['total'] === 2 && $s['selesai'] === 1);
    }

    public function test_profil_page_shows_diproses_count(): void
    {
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P1', 'status' => 'selesai', 'kategori' => 'lainnya']);
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P2', 'status' => 'proses', 'kategori' => 'lainnya']);
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P3', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create(['masyarakat_id' => $this->user->id, 'isi_laporan' => 'P4', 'status' => 'tidak_valid', 'kategori' => 'lainnya']);

        $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.profil'))
            ->assertViewHas('stats', fn($s) => $s['diproses'] === 1 && $s['selesai'] === 1 && $s['total'] === 4);
    }
}
