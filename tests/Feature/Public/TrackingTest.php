<?php

namespace Tests\Feature\Public;

use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    private function makeAnonimPengaduan(string $trackingCode = 'ABC123'): Pengaduan
    {
        return Pengaduan::create([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan anonim untuk tracking',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'tracking_code' => $trackingCode,
        ]);
    }

    public function test_tracking_page_loads_without_code(): void
    {
        $this->get(route('track'))
            ->assertOk();
    }

    public function test_tracking_page_accessible_by_guest(): void
    {
        $this->get(route('track'))
            ->assertOk();
    }

    public function test_valid_tracking_code_shows_pengaduan(): void
    {
        $pengaduan = $this->makeAnonimPengaduan('XYZ999');

        $this->get(route('track', ['code' => 'XYZ999']))
            ->assertOk()
            ->assertViewHas('pengaduan', fn($p) => $p?->tracking_code === 'XYZ999');
    }

    public function test_tracking_code_is_case_insensitive(): void
    {
        $this->makeAnonimPengaduan('UPPER1');

        $this->get(route('track', ['code' => 'upper1']))
            ->assertOk()
            ->assertViewHas('pengaduan', fn($p) => $p !== null);
    }

    public function test_invalid_code_sets_not_found_flag(): void
    {
        $this->get(route('track', ['code' => 'INVALID']))
            ->assertOk()
            ->assertViewHas('notFound', true);
    }

    public function test_tracking_does_not_find_non_anonim_complaints(): void
    {
        $masyarakat = \App\Models\Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);

        // Pengaduan with masyarakat_id set should NOT appear in tracking
        Pengaduan::create([
            'masyarakat_id' => $masyarakat->id,
            'isi_laporan'   => 'Pengaduan non-anonim',
            'status'        => 'menunggu',
            'kategori'      => 'lainnya',
            'tracking_code' => 'LINKED1',
        ]);

        $this->get(route('track', ['code' => 'LINKED1']))
            ->assertOk()
            ->assertViewHas('notFound', true);
    }

    public function test_tracking_json_response_when_requested_with_ajax(): void
    {
        $this->makeAnonimPengaduan('JSON001');

        $this->get(route('track', ['code' => 'JSON001']), ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'])
            ->assertOk()
            ->assertJson(['found' => true])
            ->assertJsonStructure([
                'found', 'tracking_code', 'status', 'kategori',
                'created_at', 'lokasi', 'isi_laporan', 'updated_at_diff', 'klarifikasi',
            ]);
    }

    public function test_tracking_json_returns_not_found_for_invalid_code(): void
    {
        $this->get(route('track', ['code' => 'NOTEXIST']), ['Accept' => 'application/json'])
            ->assertNotFound()
            ->assertJson(['found' => false]);
    }

    public function test_tracking_json_returns_not_found_when_no_code(): void
    {
        $this->get(route('track'), ['Accept' => 'application/json'])
            ->assertNotFound()
            ->assertJson(['found' => false]);
    }
}
