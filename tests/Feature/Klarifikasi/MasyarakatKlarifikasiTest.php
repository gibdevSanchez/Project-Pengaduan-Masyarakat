<?php

namespace Tests\Feature\Klarifikasi;

use App\Models\Klarifikasi;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasyarakatKlarifikasiTest extends TestCase
{
    use RefreshDatabase;

    private Masyarakat $masyarakat;
    private Petugas $petugas;
    private Pengaduan $pengaduan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->masyarakat = Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Warga Test',
            'username' => 'wargaTest',
            'password' => bcrypt('password'),
            'telp'     => '089876543210',
        ]);
        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Test',
            'username'     => 'petugastest',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
        $this->pengaduan = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => $this->masyarakat->nik,
            'isi_laporan'   => 'Jalan berlubang besar di depan rumah saya',
            'status'        => 'proses',
            'kategori'      => 'infrastruktur',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);
        Klarifikasi::create([
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'pesan'        => 'Di jalan mana tepatnya lubang tersebut?',
            'dari'         => 'petugas',
            'id_pengirim'  => $this->petugas->id_petugas,
        ]);
    }

    public function test_masyarakat_can_reply_to_petugas_question(): void
    {
        $this->actingAs($this->masyarakat, 'masyarakat')
            ->post("/masyarakat/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Di Jalan Merdeka nomor 10, dekat pos satpam.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'dari'         => 'masyarakat',
            'pesan'        => 'Di Jalan Merdeka nomor 10, dekat pos satpam.',
        ]);
    }

    public function test_masyarakat_cannot_reply_when_last_message_is_their_own(): void
    {
        Klarifikasi::create([
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'pesan'        => 'Jawaban saya sebelumnya',
            'dari'         => 'masyarakat',
            'id_pengirim'  => $this->masyarakat->id,
        ]);

        $this->actingAs($this->masyarakat, 'masyarakat')
            ->post("/masyarakat/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Jawaban kedua tanpa pertanyaan baru dari petugas',
            ])
            ->assertForbidden();
    }

    public function test_masyarakat_cannot_reply_on_selesai_complaint(): void
    {
        $this->pengaduan->update(['status' => 'selesai']);

        $this->actingAs($this->masyarakat, 'masyarakat')
            ->post("/masyarakat/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Balasan setelah complaint selesai',
            ])
            ->assertForbidden();
    }

    public function test_masyarakat_cannot_reply_on_tidak_valid_complaint(): void
    {
        $this->pengaduan->update(['status' => 'tidak_valid']);

        $this->actingAs($this->masyarakat, 'masyarakat')
            ->post("/masyarakat/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Balasan setelah complaint ditakedown',
            ])
            ->assertForbidden();
    }
}
