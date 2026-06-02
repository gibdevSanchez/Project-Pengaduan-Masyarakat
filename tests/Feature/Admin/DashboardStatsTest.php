<?php

namespace Tests\Feature\Admin;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $admin;

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
    }

    public function test_dashboard_has_menunggu_today_count(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan baru hari ini', 'status' => 'menunggu', 'kategori' => 'lainnya']);
        Pengaduan::create([
            'isi_laporan' => 'Laporan lama',
            'status'      => 'menunggu',
            'kategori'    => 'lainnya',
            'created_at'  => now()->subDays(3),
        ]);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('stats', fn($s) => $s['menungguToday'] === 1);
    }

    public function test_dashboard_has_petugas_aktif_count(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas Aktif',
            'username'     => 'petugasaktif',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        Pengaduan::create([
            'isi_laporan' => 'Laporan diproses',
            'status'      => 'proses',
            'kategori'    => 'lainnya',
            'id_petugas'  => $petugas->id_petugas,
        ]);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.dashboard'));

        $response->assertViewHas('stats', fn($s) => $s['petugasAktif'] === 1);
    }

    public function test_dashboard_has_completion_rate_between_0_and_100(): void
    {
        Pengaduan::create([
            'isi_laporan' => 'Selesai kemarin',
            'status'      => 'selesai',
            'kategori'    => 'lainnya',
            'selesai_at'  => now()->subDay(),
        ]);
        Pengaduan::create(['isi_laporan' => 'Masih menunggu', 'status' => 'menunggu', 'kategori' => 'lainnya']);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.dashboard'));

        $response->assertViewHas('stats', fn($s) => $s['completionRate'] >= 0 && $s['completionRate'] <= 100);
    }

    public function test_dashboard_passes_weekly_data_to_view(): void
    {
        Pengaduan::create(['isi_laporan' => 'Laporan minggu ini', 'status' => 'menunggu', 'kategori' => 'lainnya']);

        $response = $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.dashboard'));

        $response->assertViewHas('weeklyData');
    }
}
