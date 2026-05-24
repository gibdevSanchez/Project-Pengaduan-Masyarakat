<?php

namespace Tests\Feature\Api;

use App\Models\Masyarakat;
use App\Models\Pengaduan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PengaduanApiTest extends TestCase
{
    use RefreshDatabase;

    private Masyarakat $masyarakat;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masyarakat = Masyarakat::create([
            'nik' => '1234567890123456',
            'nama' => 'API User',
            'username' => 'apiuser',
            'password' => Hash::make('password123'),
            'telp' => '08123456789',
        ]);

        $this->token = $this->masyarakat->createToken('android')->plainTextToken;
    }

    public function test_pengguna_terautentikasi_bisa_melihat_daftar_pengaduan(): void
    {
        $this->withHeader('Authorization', 'Bearer ' . $this->token)
             ->getJson('/api/pengaduan')
             ->assertStatus(200)
             ->assertJsonIsArray();
    }

    public function test_siapapun_bisa_kirim_laporan_anonim(): void
    {
        $this->postJson('/api/pengaduan', [
            'isi_laporan' => 'Jalan di RT 05 rusak parah.',
        ])->assertStatus(201);

        $this->assertDatabaseHas('pengaduan', [
            'isi_laporan'   => 'Jalan di RT 05 rusak parah.',
            'masyarakat_id' => null,
        ]);
    }

    public function test_pengguna_terautentikasi_bisa_lihat_detail_pengaduan(): void
    {
        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'masyarakat_id' => $this->masyarakat->id,
            'isi_laporan' => 'Laporan detail test.',
            'status' => 'menunggu',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->token)
             ->getJson('/api/pengaduan/' . $pengaduan->id_pengaduan)
             ->assertStatus(200);
    }

    public function test_pengguna_terautentikasi_bisa_lihat_tanggapan(): void
    {
        $pengaduan = Pengaduan::create([
            'tgl_pengaduan' => now()->toDateString(),
            'masyarakat_id' => $this->masyarakat->id,
            'isi_laporan' => 'Laporan test.',
            'status' => 'menunggu',
        ]);

        $this->withHeader('Authorization', 'Bearer ' . $this->token)
             ->getJson('/api/pengaduan/' . $pengaduan->id_pengaduan . '/tanggapan')
             ->assertStatus(200)
             ->assertJsonIsArray();
    }

    public function test_tanpa_token_tidak_bisa_akses_pengaduan_list(): void
    {
        $this->getJson('/api/pengaduan')->assertStatus(401);
    }
}
