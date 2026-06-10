<?php

namespace Tests\Feature\Petugas;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlarifikasiAnonimTest extends TestCase
{
    use RefreshDatabase;

    private function makePetugas(string $username = 'petugas1'): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => $username,
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
    }

    private function makeAnonimPengaduan(Petugas $petugas, string $status = 'proses'): Pengaduan
    {
        return Pengaduan::create([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan anonim',
            'status'        => $status,
            'kategori'      => 'lainnya',
            'id_petugas'    => $petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_index_anonim_respons(): void
    {
        $petugas   = $this->makePetugas();
        $pengaduan = $this->makeAnonimPengaduan($petugas);

        $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.pengaduan.anonim-respons.index', $pengaduan->id_pengaduan))
            ->assertOk();
    }

    public function test_petugas_can_send_anonim_respons(): void
    {
        $petugas   = $this->makePetugas();
        $pengaduan = $this->makeAnonimPengaduan($petugas);

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.pengaduan.anonim-respons', $pengaduan->id_pengaduan), [
                'pesan' => 'Respons dari petugas untuk pengaduan anonim',
            ])
            ->assertCreated()
            ->assertJsonStructure(['dari', 'pesan', 'foto_url', 'created_at']);

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'jenis'        => 'respons_anonim',
            'dari'         => 'petugas',
        ]);
    }

    public function test_anonim_respons_requires_pesan(): void
    {
        $petugas   = $this->makePetugas();
        $pengaduan = $this->makeAnonimPengaduan($petugas);

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.pengaduan.anonim-respons', $pengaduan->id_pengaduan), [
                'pesan' => '',
            ])
            ->assertUnprocessable();
    }

    public function test_non_assigned_petugas_gets_403_on_anonim_respons(): void
    {
        $petugas1  = $this->makePetugas('petugas1');
        $petugas2  = $this->makePetugas('petugas2');
        $pengaduan = $this->makeAnonimPengaduan($petugas1);

        $this->actingAs($petugas2, 'petugas')
            ->postJson(route('petugas.pengaduan.anonim-respons', $pengaduan->id_pengaduan), [
                'pesan' => 'Respons dari petugas yang salah',
            ])
            ->assertForbidden();
    }

    public function test_anonim_respons_rejected_for_non_anonim_complaint(): void
    {
        $petugas    = $this->makePetugas();
        $masyarakat = \App\Models\Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);

        $pengaduan = Pengaduan::create([
            'masyarakat_id' => $masyarakat->id,
            'isi_laporan'   => 'Bukan anonim',
            'status'        => 'proses',
            'kategori'      => 'lainnya',
            'id_petugas'    => $petugas->id_petugas,
        ]);

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.pengaduan.anonim-respons', $pengaduan->id_pengaduan), [
                'pesan' => 'Respons ini seharusnya ditolak',
            ])
            ->assertForbidden();
    }

    public function test_anonim_respons_rejected_for_closed_complaint(): void
    {
        $petugas   = $this->makePetugas();
        $pengaduan = $this->makeAnonimPengaduan($petugas, 'selesai');

        $this->actingAs($petugas, 'petugas')
            ->postJson(route('petugas.pengaduan.anonim-respons', $pengaduan->id_pengaduan), [
                'pesan' => 'Respons untuk yang sudah selesai',
            ])
            ->assertStatus(409);
    }
}
