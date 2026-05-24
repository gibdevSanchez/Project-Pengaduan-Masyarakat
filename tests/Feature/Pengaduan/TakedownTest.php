<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TakedownTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $petugas;
    private Pengaduan $pengaduan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Test',
            'username'     => 'petugastest',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
        $masyarakat = Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Warga Test',
            'username' => 'wargaTest',
            'password' => bcrypt('password'),
            'telp'     => '089876543210',
        ]);
        $this->pengaduan = Pengaduan::create([
            'masyarakat_id' => $masyarakat->id,
            'isi_laporan'   => 'Laporan yang ternyata tidak valid',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_takedown_complaint(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/takedown", [
                'reason' => 'Laporan tidak memenuhi kriteria pengaduan yang valid sesuai aturan.',
            ])
            ->assertOk()
            ->assertJson(['status' => 'tidak_valid']);

        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan'    => $this->pengaduan->id_pengaduan,
            'status'          => 'tidak_valid',
            'takedown_reason' => 'Laporan tidak memenuhi kriteria pengaduan yang valid sesuai aturan.',
        ]);
    }

    public function test_petugas_cannot_takedown_selesai_complaint(): void
    {
        $this->pengaduan->update(['status' => 'selesai']);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/takedown", [
                'reason' => 'Alasan yang panjang dan valid untuk takedown ini.',
            ])
            ->assertStatus(409);
    }

    public function test_petugas_cannot_takedown_unassigned_complaint(): void
    {
        $other = Pengaduan::create([
            'masyarakat_id' => $this->pengaduan->masyarakat_id,
            'isi_laporan'   => 'Laporan yang belum diambil siapapun',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'id_petugas'    => null,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$other->id_pengaduan}/takedown", [
                'reason' => 'Alasan yang panjang dan valid untuk takedown ini.',
            ])
            ->assertForbidden();
    }

    public function test_petugas_tidak_bisa_takedown_laporan_milik_petugas_lain(): void
    {
        $petugasLain = Petugas::create([
            'nama_petugas' => 'Petugas Lain',
            'username'     => 'petugaslain',
            'password'     => bcrypt('password'),
            'telp'         => '082200000000',
            'level'        => 'petugas',
        ]);

        $milikPetugasLain = Pengaduan::create([
            'masyarakat_id' => $this->pengaduan->masyarakat_id,
            'isi_laporan'   => 'Laporan yang sudah diambil petugas lain',
            'status'        => 'proses',
            'kategori'      => 'lainnya',
            'id_petugas'    => $petugasLain->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$milikPetugasLain->id_pengaduan}/takedown", [
                'reason' => 'Alasan yang panjang dan valid untuk takedown ini.',
            ])
            ->assertForbidden();
    }
}
