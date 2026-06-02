<?php

namespace Tests\Feature\Admin;

use App\Models\Klarifikasi;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminKlarifikasiTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;
    private Pengaduan $pengaduan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Petugas::create([
            'nama_petugas' => 'Admin Test',
            'username'     => 'admintest',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'admin',
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
            'isi_laporan'   => 'Masalah penting yang perlu admin tangani.',
            'status'        => 'proses',
            'kategori'      => 'sosial',
        ]);
    }

    public function test_admin_can_view_klarifikasi_thread(): void
    {
        Klarifikasi::create([
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'pesan'        => 'Pesan dari masyarakat.',
            'dari'         => 'masyarakat',
            'jenis'        => 'chat',
            'masyarakat_id'=> $this->pengaduan->masyarakat_id,
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->getJson("/admin/klarifikasi/{$this->pengaduan->id_pengaduan}")
            ->assertOk()
            ->assertJsonFragment(['pesan' => 'Pesan dari masyarakat.']);
    }

    public function test_admin_can_send_message_to_klarifikasi(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->postJson("/admin/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Pesan resmi dari admin mengenai laporan ini.',
            ])
            ->assertCreated()
            ->assertJson(['dari' => 'admin']);

        $this->assertDatabaseHas('klarifikasi', [
            'id_pengaduan' => $this->pengaduan->id_pengaduan,
            'dari'         => 'admin',
            'jenis'        => 'chat',
            'pesan'        => 'Pesan resmi dari admin mengenai laporan ini.',
        ]);
    }

    public function test_admin_cannot_message_closed_complaint(): void
    {
        $this->pengaduan->update(['status' => 'selesai']);

        $this->actingAs($this->admin, 'petugas')
            ->postJson("/admin/klarifikasi/{$this->pengaduan->id_pengaduan}", [
                'pesan' => 'Mencoba kirim pesan ke pengaduan yang sudah selesai.',
            ])
            ->assertStatus(409);
    }
}
