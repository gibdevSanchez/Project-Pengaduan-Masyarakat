<?php

namespace Tests\Feature\Berita;

use App\Models\Berita;
use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeritaPublicTest extends TestCase
{
    use RefreshDatabase;

    private Masyarakat $user;
    private Petugas $penulis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Masyarakat::create([
            'nik'      => '1234567890123456',
            'nama'     => 'Budi',
            'username' => 'budi',
            'password' => bcrypt('password'),
            'telp'     => '081234567890',
        ]);
        $this->penulis = Petugas::create([
            'nama_petugas' => 'Penulis Berita',
            'username'     => 'penulis',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);
    }

    public function test_berita_page_shows_published_berita_biasa(): void
    {
        Berita::create([
            'judul'       => 'Jalan Raya Diperbaiki',
            'isi'         => 'Dinas PUPR telah menyelesaikan perbaikan.',
            'kategori'    => 'infrastruktur',
            'format'      => 'biasa',
            'petugas_id'  => $this->penulis->id_petugas,
            'is_published'=> true,
        ]);

        $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.berita'))
            ->assertOk()
            ->assertSee('Jalan Raya Diperbaiki');
    }

    public function test_berita_page_hides_unpublished_berita(): void
    {
        Berita::create([
            'judul'       => 'Berita Draft',
            'isi'         => 'Belum dipublikasikan.',
            'kategori'    => 'lainnya',
            'format'      => 'biasa',
            'petugas_id'  => $this->penulis->id_petugas,
            'is_published'=> false,
        ]);

        $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.berita'))
            ->assertOk()
            ->assertDontSee('Berita Draft');
    }

    public function test_berita_besar_appears_when_within_schedule(): void
    {
        Berita::create([
            'judul'          => 'Pengumuman Penting',
            'isi'            => 'Ini berita besar.',
            'kategori'       => 'sosial',
            'format'         => 'besar',
            'petugas_id'     => $this->penulis->id_petugas,
            'is_published'   => true,
            'mulai_tayang'   => now()->subHour(),
            'selesai_tayang' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.berita'));

        $response->assertOk();
        $response->assertViewHas('beritaBesar', fn($b) => $b !== null);
    }

    public function test_berita_besar_hidden_when_outside_schedule(): void
    {
        Berita::create([
            'judul'          => 'Berita Sudah Lewat',
            'isi'            => 'Sudah selesai tayang.',
            'kategori'       => 'sosial',
            'format'         => 'besar',
            'petugas_id'     => $this->penulis->id_petugas,
            'is_published'   => true,
            'mulai_tayang'   => now()->subWeek(),
            'selesai_tayang' => now()->subDay(),
        ]);

        $response = $this->actingAs($this->user, 'masyarakat')
            ->get(route('masyarakat.berita'));

        $response->assertViewHas('beritaBesar', null);
    }

    public function test_berita_can_link_to_pengaduan(): void
    {
        $pengaduan = Pengaduan::create([
            'isi_laporan' => 'Pengaduan terkait berita.',
            'status'      => 'selesai',
            'kategori'    => 'infrastruktur',
        ]);

        $berita = Berita::create([
            'judul'        => 'Berita Terkait Pengaduan',
            'isi'          => 'Tindak lanjut dari pengaduan.',
            'kategori'     => 'infrastruktur',
            'format'       => 'biasa',
            'petugas_id'   => $this->penulis->id_petugas,
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'is_published' => true,
        ]);

        $this->assertEquals($pengaduan->id_pengaduan, $berita->pengaduan->id_pengaduan);
    }
}
