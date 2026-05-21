<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutoDeleteCommandTest extends TestCase
{
    use RefreshDatabase;

    private function makePengaduan(array $attrs): Pengaduan
    {
        return Pengaduan::create(array_merge([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => null,
            'isi_laporan'   => 'Laporan test',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
        ], $attrs));
    }

    public function test_selesai_older_than_30_days_gets_soft_deleted(): void
    {
        $old = $this->makePengaduan([
            'status'     => 'selesai',
            'selesai_at' => now()->subDays(31),
        ]);

        $this->artisan('pengaduan:auto-delete')->assertSuccessful();

        $this->assertSoftDeleted('pengaduan', ['id_pengaduan' => $old->id_pengaduan]);
    }

    public function test_selesai_within_30_days_is_not_deleted(): void
    {
        $recent = $this->makePengaduan([
            'status'     => 'selesai',
            'selesai_at' => now()->subDays(29),
        ]);

        $this->artisan('pengaduan:auto-delete')->assertSuccessful();

        $this->assertDatabaseHas('pengaduan', ['id_pengaduan' => $recent->id_pengaduan, 'deleted_at' => null]);
    }

    public function test_non_selesai_complaint_is_not_deleted(): void
    {
        $active = $this->makePengaduan([
            'status'     => 'proses',
            'selesai_at' => now()->subDays(60),
        ]);

        $this->artisan('pengaduan:auto-delete')->assertSuccessful();

        $this->assertDatabaseHas('pengaduan', ['id_pengaduan' => $active->id_pengaduan, 'deleted_at' => null]);
    }
}
