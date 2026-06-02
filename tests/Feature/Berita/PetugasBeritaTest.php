<?php

namespace Tests\Feature\Berita;

use App\Models\Berita;
use App\Models\Petugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasBeritaTest extends TestCase
{
    use RefreshDatabase;

    private Petugas $petugas;
    private Petugas $other;

    protected function setUp(): void
    {
        parent::setUp();
        $this->petugas = Petugas::create([
            'nama_petugas' => 'Petugas Berita',
            'username'     => 'petugasberita',
            'password'     => bcrypt('password'),
            'telp'         => '081234567890',
            'level'        => 'petugas',
        ]);
        $this->other = Petugas::create([
            'nama_petugas' => 'Petugas Lain',
            'username'     => 'petugaslain',
            'password'     => bcrypt('password'),
            'telp'         => '081234567891',
            'level'        => 'petugas',
        ]);
    }

    public function test_petugas_can_create_berita(): void
    {
        $this->actingAs($this->petugas, 'petugas')
            ->post(route('petugas.berita.store'), [
                'judul'          => 'Berita dari Petugas',
                'isi'            => 'Isi berita yang cukup panjang.',
                'kategori'       => 'lingkungan',
                'format'         => 'biasa',
                'publikasi_mode' => 'sekarang',
            ])
            ->assertRedirect(route('petugas.berita.index'));

        $this->assertDatabaseHas('berita', [
            'judul'      => 'Berita dari Petugas',
            'petugas_id' => $this->petugas->id_petugas,
        ]);
    }

    public function test_petugas_can_only_edit_own_berita(): void
    {
        $beritaOrang = Berita::create([
            'judul'      => 'Berita Milik Orang Lain',
            'isi'        => 'Isi.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->other->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->put(route('petugas.berita.update', $beritaOrang->id), [
                'judul'          => 'Coba Edit',
                'isi'            => 'Coba edit milik orang lain.',
                'kategori'       => 'lainnya',
                'format'         => 'biasa',
                'publikasi_mode' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_petugas_can_only_delete_own_berita(): void
    {
        $beritaOrang = Berita::create([
            'judul'      => 'Berita Milik Orang Lain',
            'isi'        => 'Isi.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->other->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->delete(route('petugas.berita.destroy', $beritaOrang->id))
            ->assertForbidden();
    }

    public function test_petugas_can_delete_own_berita(): void
    {
        $berita = Berita::create([
            'judul'      => 'Berita Saya Sendiri',
            'isi'        => 'Isi berita saya.',
            'kategori'   => 'lainnya',
            'format'     => 'biasa',
            'petugas_id' => $this->petugas->id_petugas,
        ]);

        $this->actingAs($this->petugas, 'petugas')
            ->delete(route('petugas.berita.destroy', $berita->id))
            ->assertRedirect(route('petugas.berita.index'));

        $this->assertDatabaseMissing('berita', ['id' => $berita->id]);
    }
}
