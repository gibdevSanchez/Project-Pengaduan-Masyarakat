<?php

namespace Tests\Feature\Berita;

use App\Models\Berita;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBeritaTest extends TestCase
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

    public function test_admin_can_view_berita_list(): void
    {
        Berita::create([
            'judul'      => 'Berita Admin',
            'isi'        => 'Isi berita.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->admin->id_petugas,
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->get(route('admin.berita.index'))
            ->assertOk()
            ->assertSee('Berita Admin');
    }

    public function test_admin_can_create_berita(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.berita.store'), [
                'judul'          => 'Berita Baru',
                'isi'            => 'Isi berita yang informatif.',
                'kategori'       => 'infrastruktur',
                'format'         => 'biasa',
                'publikasi_mode' => 'sekarang',
            ])
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('berita', [
            'judul'      => 'Berita Baru',
            'petugas_id' => $this->admin->id_petugas,
        ]);
    }

    public function test_admin_can_create_berita_besar_with_schedule(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.berita.store'), [
                'judul'          => 'Pengumuman Besar',
                'isi'            => 'Pengumuman penting dari pemerintah.',
                'kategori'       => 'sosial',
                'format'         => 'besar',
                'publikasi_mode' => 'jadwalkan',
                'mulai_tayang'   => now()->addHour()->format('Y-m-d\TH:i'),
                'selesai_tayang' => now()->addWeek()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('berita', [
            'judul'  => 'Pengumuman Besar',
            'format' => 'besar',
        ]);
    }

    public function test_admin_can_update_berita(): void
    {
        $berita = Berita::create([
            'judul'      => 'Judul Lama',
            'isi'        => 'Isi lama.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->admin->id_petugas,
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->put(route('admin.berita.update', $berita->id), [
                'judul'          => 'Judul Baru',
                'isi'            => 'Isi baru yang diperbarui.',
                'kategori'       => 'lingkungan',
                'format'         => 'biasa',
                'publikasi_mode' => 'draft',
            ])
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseHas('berita', ['id' => $berita->id, 'judul' => 'Judul Baru']);
    }

    public function test_admin_can_delete_berita(): void
    {
        $berita = Berita::create([
            'judul'      => 'Berita Mau Dihapus',
            'isi'        => 'Isi.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->admin->id_petugas,
        ]);

        $this->actingAs($this->admin, 'petugas')
            ->delete(route('admin.berita.destroy', $berita->id))
            ->assertRedirect(route('admin.berita.index'));

        $this->assertDatabaseMissing('berita', ['id' => $berita->id]);
    }

    public function test_berita_requires_judul_and_isi(): void
    {
        $this->actingAs($this->admin, 'petugas')
            ->post(route('admin.berita.store'), [
                'judul' => '',
                'isi'   => '',
            ])
            ->assertSessionHasErrors(['judul', 'isi']);
    }
}
