<?php

namespace Tests\Feature\Profil;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasStatistikTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $petugas;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Statistik',
            'username'     => 'petugasstats',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
    }

    public function test_profil_petugas_shows_stats(): void
    {
        Pengaduan::create(['id_petugas' => $this->petugas->id_petugas, 'isi_laporan' => 'P1', 'status' => 'selesai', 'kategori' => 'lainnya']);
        Pengaduan::create(['id_petugas' => $this->petugas->id_petugas, 'isi_laporan' => 'P2', 'status' => 'proses', 'kategori' => 'lainnya']);
        Pengaduan::create(['id_petugas' => $this->petugas->id_petugas, 'isi_laporan' => 'P3', 'status' => 'tidak_valid', 'kategori' => 'lainnya']);

        $this->actingAs($this->petugas, 'petugas')
            ->get(route('petugas.profil'))
            ->assertOk()
            ->assertViewHas('stats', fn($s) =>
                $s['total'] === 3 &&
                $s['selesai'] === 1 &&
                $s['ditolak'] === 1
            );
    }
}
