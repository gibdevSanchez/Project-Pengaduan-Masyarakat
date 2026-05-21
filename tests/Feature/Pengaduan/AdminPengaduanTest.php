<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPengaduanTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Admin',
            'username'     => 'admin',
            'password'     => bcrypt('pass'),
            'telp'         => '08123456789',
            'level'        => 'admin',
        ]);
    }

    private function makePengaduan(array $attrs = []): Pengaduan
    {
        return Pengaduan::create(array_merge([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => null,
            'isi_laporan'   => 'Laporan test',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
        ], $attrs));
    }

    public function test_admin_can_view_complaint_list_including_soft_deleted(): void
    {
        $admin  = $this->makeAdmin();
        $active = $this->makePengaduan();
        $soft   = $this->makePengaduan();
        $soft->delete();

        $response = $this->actingAs($admin, 'petugas')->get(route('admin.pengaduan.index'));

        $response->assertOk();
        $response->assertSee('Laporan test');
    }

    public function test_admin_can_update_status(): void
    {
        $admin     = $this->makeAdmin();
        $pengaduan = $this->makePengaduan(['status' => 'menunggu']);

        $this->actingAs($admin, 'petugas')
            ->patch(route('admin.pengaduan.status', $pengaduan->id_pengaduan), ['status' => 'proses']);

        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'status'       => 'proses',
        ]);
    }

    public function test_admin_soft_delete(): void
    {
        $admin     = $this->makeAdmin();
        $pengaduan = $this->makePengaduan();

        $this->actingAs($admin, 'petugas')
            ->delete(route('admin.pengaduan.destroy', $pengaduan->id_pengaduan));

        $this->assertSoftDeleted('pengaduan', ['id_pengaduan' => $pengaduan->id_pengaduan]);
    }

    public function test_admin_force_delete_removes_from_db(): void
    {
        $admin     = $this->makeAdmin();
        $pengaduan = $this->makePengaduan();

        $this->actingAs($admin, 'petugas')
            ->delete(route('admin.pengaduan.force-destroy', $pengaduan->id_pengaduan));

        $this->assertDatabaseMissing('pengaduan', ['id_pengaduan' => $pengaduan->id_pengaduan]);
    }

    public function test_selesai_at_set_when_status_updated_to_selesai(): void
    {
        $admin     = $this->makeAdmin();
        $pengaduan = $this->makePengaduan(['status' => 'proses']);

        $this->actingAs($admin, 'petugas')
            ->patch(route('admin.pengaduan.status', $pengaduan->id_pengaduan), ['status' => 'selesai']);

        $this->assertNotNull(Pengaduan::withTrashed()->find($pengaduan->id_pengaduan)->selesai_at);
    }
}
