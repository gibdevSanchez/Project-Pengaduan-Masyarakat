<?php

namespace Tests\Feature;

use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrasiSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_tabel_masyarakat_memiliki_kolom_yang_benar(): void
    {
        $this->assertTrue(Schema::hasTable('masyarakat'));
        $this->assertTrue(Schema::hasColumns('masyarakat', [
            'id', 'nik', 'nama', 'username', 'password', 'telp', 'created_at', 'updated_at'
        ]));
    }

    public function test_tabel_petugas_memiliki_kolom_yang_benar(): void
    {
        $this->assertTrue(Schema::hasTable('petugas'));
        $this->assertTrue(Schema::hasColumns('petugas', ['id_petugas', 'nama_petugas', 'username', 'password', 'telp', 'level', 'created_at', 'updated_at']));
    }

    public function test_tabel_pengaduan_memiliki_kolom_yang_benar(): void
    {
        $this->assertTrue(Schema::hasTable('pengaduan'));
        $this->assertTrue(Schema::hasColumns('pengaduan', [
            'id_pengaduan', 'tgl_pengaduan', 'nik', 'isi_laporan', 'foto', 'status',
            'kategori', 'lokasi', 'id_petugas', 'selesai_at', 'deleted_at',
            'created_at', 'updated_at',
        ]));
    }

    public function test_tabel_tanggapan_memiliki_kolom_yang_benar(): void
    {
        $this->assertTrue(Schema::hasTable('tanggapan'));
        $this->assertTrue(Schema::hasColumns('tanggapan', ['id_tanggapan', 'id_pengaduan', 'tgl_tanggapan', 'tanggapan', 'id_petugas', 'created_at', 'updated_at']));
    }

    public function test_pengaduan_has_tidak_valid_status_and_takedown_reason(): void
    {
        $this->assertTrue(Schema::hasColumn('pengaduan', 'takedown_reason'));
        Pengaduan::create([
            'tgl_pengaduan'   => now()->toDateString(),
            'isi_laporan'     => 'Test laporan untuk tidak valid',
            'status'          => 'tidak_valid',
            'kategori'        => 'lainnya',
            'takedown_reason' => 'Alasan takedown yang cukup panjang',
        ]);
        $this->assertDatabaseHas('pengaduan', ['status' => 'tidak_valid']);
    }

    public function test_klarifikasi_table_exists_with_correct_columns(): void
    {
        $this->assertTrue(Schema::hasTable('klarifikasi'));
        foreach (['id_klarifikasi', 'id_pengaduan', 'pesan', 'dari', 'id_pengirim', 'created_at'] as $col) {
            $this->assertTrue(Schema::hasColumn('klarifikasi', $col), "Missing column: {$col}");
        }
    }
}
