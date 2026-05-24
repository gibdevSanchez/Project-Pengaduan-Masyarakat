<?php

namespace Tests\Feature\Klarifikasi;

use App\Models\Klarifikasi;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasKlarifikasiTest extends TestCase
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
            'isi_laporan'   => 'Jalan berlubang sangat dalam di depan SD',
            'status'        => 'proses',
            'kategori'      => 'infrastruktur',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_send_klarifikasi_question(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Tolong berikan detail lokasi yang lebih spesifik.',
            ])
            ->assertCreated()
            ->assertJsonStructure(['dari', 'pesan', 'created_at']);

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'dari'         => 'petugas',
            'pesan'        => 'Tolong berikan detail lokasi yang lebih spesifik.',
        ]);
    }

    public function test_petugas_can_fetch_klarifikasi_thread(): void
    {
        Klarifikasi::create([
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'pesan'        => 'Pertanyaan pertama dari petugas',
            'dari'         => 'petugas',
            'petugas_id'   => $this->petugas->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->getJson("/petugas/klarifikasi/{$this->pengaduan->id_pengaduan}")
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.dari', 'petugas');
    }

    public function test_petugas_cannot_send_klarifikasi_to_anonymous_complaint(): void
    {
        $anon = Pengaduan::create([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan dari anonim tidak boleh klarifikasi',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/klarifikasi/{$anon->id_pengaduan}", [
                'pesan' => 'Pertanyaan untuk anonim yang tidak boleh',
            ])
            ->assertForbidden();
    }

    public function test_petugas_cannot_send_klarifikasi_to_locked_complaint(): void
    {
        $this->pengaduan->update(['status' => 'selesai']);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Pertanyaan setelah complaint selesai',
            ])
            ->assertStatus(409);
    }

    public function test_petugas_tidak_bisa_klarifikasi_laporan_yang_belum_diambil(): void
    {
        $belumDiambil = Pengaduan::create([
            'masyarakat_id' => $this->pengaduan->masyarakat_id,
            'isi_laporan'   => 'Laporan yang belum ada petugasnya',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'id_petugas'    => null,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/klarifikasi/{$belumDiambil->id_pengaduan}", [
                'pesan' => 'Coba kirim klarifikasi ke laporan yang belum diambil',
            ])
            ->assertForbidden();
    }

    public function test_petugas_tidak_bisa_klarifikasi_laporan_milik_petugas_lain(): void
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
            ->postJson("/petugas/klarifikasi/{$milikPetugasLain->id_pengaduan}", [
                'pesan' => 'Coba kirim klarifikasi ke laporan orang lain',
            ])
            ->assertForbidden();
    }
}
