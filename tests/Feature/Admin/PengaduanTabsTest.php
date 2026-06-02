<?php

namespace Tests\Feature\Admin;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaduanTabsTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Petugas::create([
            'nama_petugas' => 'Admin',
            'username'     => 'admin',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'admin',
        ]);
    }

    public function test_default_tab_excludes_tidak_valid(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan normal', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create(['isi_laporan' => 'Laporan ditolak', 'status' => 'tidak_valid', 'kategori' => 'lainnya']);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.pengaduan.index'));

        $response->assertOk();
        $response->assertSee('Laporan normal');
        $response->assertDontSee('Laporan ditolak');
    }

    public function test_invalid_tab_shows_only_tidak_valid(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan normal', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create(['isi_laporan' => 'Laporan ditolak', 'status' => 'tidak_valid', 'kategori' => 'lainnya']);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.pengaduan.index', ['tab' => 'invalid']));

        $response->assertOk();
        $response->assertSee('Laporan ditolak');
        $response->assertDontSee('Laporan normal');
    }

    public function test_menunggu_tab_shows_only_menunggu(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan A', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create(['isi_laporan' => 'Laporan B', 'status' => 'proses', 'kategori' => 'lainnya']);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.pengaduan.index', ['tab' => 'menunggu']));

        $response->assertSee('Laporan A');
        $response->assertDontSee('Laporan B');
    }

    public function test_admin_can_restore_tidak_valid_to_menunggu(): void
    {
        $pengaduan = Pengaduan::create([
            'isi_laporan' => 'Mau di-restore',
            'status'      => 'tidak_valid',
            'kategori'    => 'lainnya',
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->patch(route('admin.pengaduan.status', $pengaduan->id_pengaduan), ['status' => 'menunggu'])
            ->assertRedirect();

        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'status'       => 'menunggu',
        ]);
    }

    public function test_view_receives_current_tab(): void
    {
        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.pengaduan.index', ['tab' => 'proses']));

        $response->assertViewHas('tab', 'proses');
    }
}
