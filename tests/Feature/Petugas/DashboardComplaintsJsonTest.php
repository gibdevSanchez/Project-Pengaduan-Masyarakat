<?php

namespace Tests\Feature\Petugas;

use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardComplaintsJsonTest extends TestCase
{
    use RefreshDatabase;

    private function makePetugas(): Petugas
    {
        return Petugas::create([
            'nama_petugas' => 'Petugas Satu',
            'username'     => 'petugas1',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
    }

    private function makePengaduan(Petugas $petugas = null, array $attrs = []): Pengaduan
    {
        return Pengaduan::create(array_merge([
            'isi_laporan' => 'Laporan test',
            'status'      => 'menunggu',
            'kategori'    => 'lainnya',
            'id_petugas'  => $petugas?->id_petugas,
        ], $attrs));
    }

    public function test_petugas_can_get_complaints_json(): void
    {
        $petugas = $this->makePetugas();

        $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json'))
            ->assertOk()
            ->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_complaints_response_has_expected_fields(): void
    {
        $petugas = $this->makePetugas();
        $this->makePengaduan($petugas);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json'))
            ->assertOk();

        $item = $response->json('data.0');
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('status', $item);
        $this->assertArrayHasKey('kategori', $item);
        $this->assertArrayHasKey('sla_deadline', $item);
        $this->assertArrayHasKey('is_overdue', $item);
        $this->assertArrayHasKey('isAnonim', $item);
    }

    public function test_mine_filter_returns_only_own_complaints(): void
    {
        $petugas  = $this->makePetugas();
        $petugas2 = Petugas::create([
            'nama_petugas' => 'Petugas Dua',
            'username'     => 'petugas2',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);

        $this->makePengaduan($petugas, ['isi_laporan' => 'Milik Satu']);
        $this->makePengaduan($petugas2, ['isi_laporan' => 'Milik Dua']);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json', ['mine' => 1]))
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Milik Satu', $response->json('data.0.isiLaporan'));
    }

    public function test_status_filter_works(): void
    {
        $petugas = $this->makePetugas();
        $this->makePengaduan($petugas, ['status' => 'menunggu']);
        $this->makePengaduan($petugas, ['status' => 'proses']);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json', ['mine' => 1, 'status' => 'menunggu']))
            ->assertOk();

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('menunggu', $response->json('data.0.status'));
    }

    public function test_sla_deadline_is_null_for_completed_complaints(): void
    {
        $petugas = $this->makePetugas();
        $this->makePengaduan($petugas, ['status' => 'selesai']);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json', ['mine' => 1]))
            ->assertOk();

        $this->assertNull($response->json('data.0.sla_deadline'));
    }

    public function test_anonim_complaint_shows_correctly(): void
    {
        $petugas = $this->makePetugas();
        $this->makePengaduan($petugas, ['masyarakat_id' => null]);

        $response = $this->actingAs($petugas, 'petugas')
            ->getJson(route('petugas.complaints.json', ['mine' => 1]))
            ->assertOk();

        $this->assertTrue($response->json('data.0.isAnonim'));
        $this->assertEquals('Anonim', $response->json('data.0.nama'));
    }

    public function test_guest_cannot_access_complaints_json(): void
    {
        $this->getJson(route('petugas.complaints.json'))
            ->assertRedirect();
    }
}
