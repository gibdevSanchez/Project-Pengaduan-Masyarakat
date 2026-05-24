<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasAssignTest extends TestCase
{
    use RefreshDatabase;

    private function makePetugas(string $suffix = '1'): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Petugas ' . $suffix,
            'username'     => 'petugas' . $suffix,
            'password'     => bcrypt('pass'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
    }

    private function makePengaduan(array $attrs = []): Pengaduan
    {
        return Pengaduan::create(array_merge([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan test',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
        ], $attrs));
    }

    public function test_petugas_can_self_assign_unassigned_complaint(): void
    {
        $petugas   = $this->makePetugas();
        $pengaduan = $this->makePengaduan();

        $response = $this->actingAs($petugas, 'petugas')
            ->post(route('petugas.pengaduan.assign', $pengaduan->id_pengaduan));

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertDatabaseHas('pengaduan', [
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'id_petugas'   => $petugas->id_petugas,
        ]);
    }

    public function test_assign_already_assigned_complaint_returns_404(): void
    {
        $petugas1  = $this->makePetugas('1');
        $petugas2  = $this->makePetugas('2');
        $pengaduan = $this->makePengaduan(['id_petugas' => $petugas1->id_petugas]);

        $response = $this->actingAs($petugas2, 'petugas')
            ->post(route('petugas.pengaduan.assign', $pengaduan->id_pengaduan));

        $response->assertNotFound();
    }

    public function test_non_assigned_petugas_cannot_reply(): void
    {
        $petugas1  = $this->makePetugas('1');
        $petugas2  = $this->makePetugas('2');
        $pengaduan = $this->makePengaduan(['id_petugas' => $petugas1->id_petugas]);

        $response = $this->actingAs($petugas2, 'petugas')
            ->post(route('petugas.tanggapan.store', $pengaduan->id_pengaduan), [
                'tanggapan' => 'Sedang ditindaklanjuti',
                'status'    => 'proses',
            ]);

        $response->assertForbidden();
    }
}
