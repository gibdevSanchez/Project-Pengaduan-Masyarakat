<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasStatusUpdateTest extends TestCase
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
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => $masyarakat->nik,
            'isi_laporan'   => 'Jalan rusak parah sekali di depan rumah',
            'status'        => 'menunggu',
            'kategori'      => 'infrastruktur',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_update_status_to_proses(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/status", ['status' => 'proses'])
            ->assertOk()
            ->assertJson(['status' => 'proses']);

        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'status'       => 'proses',
        ]);
    }

    public function test_petugas_can_update_status_to_selesai_and_sets_selesai_at(): void
    {
        $this->pengaduan->update(['status' => 'proses']);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/status", ['status' => 'selesai'])
            ->assertOk()
            ->assertJson(['status' => 'selesai']);

        $this->assertNotNull($this->pengaduan->fresh()->selesai_at);
    }

    public function test_petugas_cannot_update_unassigned_complaint(): void
    {
        $other = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => $this->pengaduan->nik,
            'isi_laporan'   => 'Laporan milik orang lain',
            'status'        => 'menunggu',
            'kategori'      => 'lingkungan',
            'id_petugas'    => null,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$other->id_pengaduan}/status", ['status' => 'proses'])
            ->assertForbidden();
    }
}
