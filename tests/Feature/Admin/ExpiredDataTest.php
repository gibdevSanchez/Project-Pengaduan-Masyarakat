<?php

namespace Tests\Feature\Admin;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpiredDataTest extends TestCase
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

    public function test_expired_page_shows_soft_deleted_pengaduan(): void
    {
        $p = Pengaduan::create(['isi_laporan' => 'Laporan dihapus', 'status' => 'selesai', 'kategori' => 'lainnya']);
        $p->delete();

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.expired.index'))
            ->assertOk()
            ->assertSee('Laporan dihapus');
    }

    public function test_expired_page_does_not_show_active_pengaduan(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan aktif', 'status' => 'menunggu', 'kategori' => 'lainnya']);

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.expired.index'))
            ->assertOk()
            ->assertDontSee('Laporan aktif');
    }

    public function test_admin_can_hard_delete_single_expired_record(): void
    {
        $p = Pengaduan::create(['isi_laporan' => 'Hapus permanen', 'status' => 'selesai', 'kategori' => 'lainnya']);
        $p->delete();

        $this->actingAs($this->admin, 'petugas')
            ->delete(route('admin.expired.destroy', $p->id_pengaduan))
            ->assertRedirect();

        $this->assertDatabaseMissing('pengaduan', ['id_pengaduan' => $p->id_pengaduan]);
    }

    public function test_admin_can_hard_delete_all_expired(): void
    {
        $p1 = Pengaduan::create(['isi_laporan' => 'P1', 'status' => 'selesai', 'kategori' => 'lainnya']);
        $p2 = Pengaduan::create(['isi_laporan' => 'P2', 'status' => 'selesai', 'kategori' => 'lainnya']);
        $p1->delete();
        $p2->delete();

        $this->actingAs($this->admin, 'petugas')
            ->delete(route('admin.expired.destroy-all'))
            ->assertRedirect();

        $this->assertDatabaseCount('pengaduan', 0);
    }

    public function test_non_admin_petugas_cannot_access_expired_page(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Biasa',
            'username'     => 'petugasbiasa',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->actingAs($petugas, 'petugas')
            ->get(route('admin.expired.index'))
            ->assertRedirect('/petugas/dashboard');
    }
}
