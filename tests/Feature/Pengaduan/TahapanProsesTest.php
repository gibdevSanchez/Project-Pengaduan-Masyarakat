<?php

namespace Tests\Feature\Pengaduan;

use App\Models\AppSetting;
use App\Models\Klarifikasi;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahapanProsesTest extends TestCase
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
            'isi_laporan'   => 'Lampu jalan mati di RT 03',
            'status'        => 'proses',
            'kategori'      => 'infrastruktur',
            'id_petugas'    => $this->petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_add_tahapan(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/tahapan", [
                'pesan' => 'Mengirim tim teknis ke lokasi untuk pengecekan.',
            ])
            ->assertCreated()
            ->assertJson(['jenis' => 'tahapan']);

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'jenis'        => 'tahapan',
            'dari'         => 'petugas',
            'pesan'        => 'Mengirim tim teknis ke lokasi untuk pengecekan.',
        ]);
    }

    public function test_petugas_cannot_add_tahapan_to_others_complaint(): void
    {
        $other = Petugas::create([
            'nama_petugas' => 'Petugas Lain',
            'username'     => 'petugaslain',
            'password'     => bcrypt('password'),
            'telp'         => '082200000000',
            'level'        => 'petugas',
        ]);

        $this->actingAs($other, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/tahapan", [
                'pesan' => 'Coba tambah tahapan di pengaduan milik petugas lain.',
            ])
            ->assertForbidden();
    }

    public function test_petugas_can_selesaikan_with_closing_message(): void
    {
        AppSetting::set('closing_template', 'Terima kasih telah menggunakan layanan M-Lapor.');

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/selesai", [
                'pesan_tambahan' => 'Lampu sudah diperbaiki oleh PLN hari ini.',
            ])
            ->assertOk()
            ->assertJson(['status' => 'selesai']);

        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'status'       => 'selesai',
        ]);

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'jenis'        => 'penutup',
            'dari'         => 'petugas',
        ]);
    }

    public function test_tahapan_pesan_is_required(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/tahapan", [
                'pesan' => '',
            ])
            ->assertUnprocessable();
    }

    public function test_closing_message_contains_template_and_custom(): void
    {
        AppSetting::set('closing_template', 'Terima kasih atas laporan Anda.');

        $this->actingAs($this->petugas, 'petugas')
            ->postJson("/petugas/pengaduan/{$this->pengaduan->id_pengaduan}/selesai", [
                'pesan_tambahan' => 'Perbaikan selesai 2 jam.',
            ]);

        $penutup = Klarifikasi::where('id_pengaduan', $this->pengaduan->id_pengaduan)
            ->where('jenis', 'penutup')
            ->first();

        $this->assertStringContainsString('Terima kasih atas laporan Anda.', $penutup->pesan);
        $this->assertStringContainsString('Perbaikan selesai 2 jam.', $penutup->pesan);
    }
}
