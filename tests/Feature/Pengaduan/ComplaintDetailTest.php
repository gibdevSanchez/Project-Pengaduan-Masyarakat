<?php

namespace Tests\Feature\Pengaduan;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintDetailTest extends TestCase
{
    use RefreshDatabase;

    private function makeMasyarakat(string $nik = '1234567890123456'): Masyarakat
    {
        return Masyarakat::create([
            'nik' => $nik, 'nama' => 'Budi', 'username' => 'budi' . $nik[0],
            'password' => bcrypt('pass'), 'telp' => '08123456789',
        ]);
    }

    private function makePengaduan(string $nik): Pengaduan
    {
        return Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik'           => $nik,
            'isi_laporan'   => 'Jalan berlubang di perempatan',
            'status'        => 'menunggu',
            'kategori'      => 'infrastruktur',
        ]);
    }

    public function test_owner_can_view_detail(): void
    {
        $user = $this->makeMasyarakat();
        $pengaduan = $this->makePengaduan($user->nik);

        $response = $this->actingAs($user, 'masyarakat')
            ->get(route('masyarakat.pengaduan.show', $pengaduan->id_pengaduan));

        $response->assertOk();
        $response->assertSee('Jalan berlubang di perempatan');
    }

    public function test_non_owner_gets_404(): void
    {
        $owner  = $this->makeMasyarakat('1234567890123456');
        $other  = $this->makeMasyarakat('9876543210987654');
        $pengaduan = $this->makePengaduan($owner->nik);

        $response = $this->actingAs($other, 'masyarakat')
            ->get(route('masyarakat.pengaduan.show', $pengaduan->id_pengaduan));

        $response->assertNotFound();
    }

    public function test_soft_deleted_complaint_returns_404(): void
    {
        $user = $this->makeMasyarakat();
        $pengaduan = $this->makePengaduan($user->nik);
        $pengaduan->delete();

        $response = $this->actingAs($user, 'masyarakat')
            ->get(route('masyarakat.pengaduan.show', $pengaduan->id_pengaduan));

        $response->assertNotFound();
    }
}
