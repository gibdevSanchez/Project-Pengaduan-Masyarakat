<?php

namespace Tests\Unit\Models;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use App\Models\Petugas;
use App\Models\Tanggapan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModelRelationTest extends TestCase
{
    use RefreshDatabase;

    public function test_masyarakat_primary_key_adalah_nik_string(): void
    {
        $m = new Masyarakat();
        $this->assertEquals('id', $m->getKeyName());
    }

    public function test_petugas_primary_key_adalah_id_petugas(): void
    {
        $p = new Petugas();
        $this->assertEquals('id_petugas', $p->getKeyName());
    }

    public function test_pengaduan_primary_key_adalah_id_pengaduan(): void
    {
        $p = new Pengaduan();
        $this->assertEquals('id_pengaduan', $p->getKeyName());
    }

    public function test_masyarakat_memiliki_banyak_pengaduan(): void
    {
        $masyarakat = Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'Budi Santoso',
            'username' => 'budi',
            'password' => Hash::make('password'),
            'telp' => '08123456789',
        ]);

        Pengaduan::create([
            'masyarakat_id' => $masyarakat->id,
            'isi_laporan'   => 'Jalan rusak di RT 03.',
            'status'        => 'menunggu',
        ]);

        $this->assertCount(1, $masyarakat->pengaduan);
        $this->assertInstanceOf(Pengaduan::class, $masyarakat->pengaduan->first());
    }

    public function test_pengaduan_anonim_boleh_tanpa_nik(): void
    {
        $pengaduan = Pengaduan::create([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan anonim.',
            'status'        => 'menunggu',
        ]);

        $this->assertNull($pengaduan->masyarakat_id);
        $this->assertNull($pengaduan->masyarakat);
    }

    public function test_pengaduan_memiliki_banyak_tanggapan(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas A',
            'username' => 'petugasa',
            'password' => Hash::make('password'),
            'telp' => '08123456789',
            'level' => 'petugas',
        ]);

        $pengaduan = Pengaduan::create([
            'masyarakat_id' => null,
            'isi_laporan'   => 'Laporan test.',
            'status'        => 'menunggu',
        ]);

        Tanggapan::create([
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'tgl_tanggapan' => now()->toDateString(),
            'tanggapan' => 'Sedang ditindaklanjuti.',
            'id_petugas' => $petugas->id_petugas,
        ]);

        $this->assertCount(1, $pengaduan->tanggapan);
        $this->assertInstanceOf(Tanggapan::class, $pengaduan->tanggapan->first());
    }

    public function test_tanggapan_milik_petugas_dan_pengaduan(): void
    {
        $petugas = Petugas::create([
            'nama_petugas' => 'Petugas B',
            'username' => 'petugasb',
            'password' => Hash::make('password'),
            'telp' => '08123456789',
            'level' => 'petugas',
        ]);

        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'nik' => null,
            'isi_laporan' => 'Test laporan.',
            'status' => 'menunggu',
        ]);

        $tanggapan = Tanggapan::create([
            'id_pengaduan' => $pengaduan->id_pengaduan,
            'tgl_tanggapan' => now()->toDateString(),
            'tanggapan' => 'Respons petugas.',
            'id_petugas' => $petugas->id_petugas,
        ]);

        $this->assertEquals($petugas->id_petugas, $tanggapan->petugas->id_petugas);
        $this->assertEquals($pengaduan->id_pengaduan, $tanggapan->pengaduan->id_pengaduan);
    }
}
