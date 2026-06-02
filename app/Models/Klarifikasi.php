<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klarifikasi extends Model
{
    protected $table = 'klarifikasi';
    protected $primaryKey = 'id_klarifikasi';

    protected $fillable = ['id_pengaduan', 'pesan', 'foto', 'dari', 'jenis', 'petugas_id', 'masyarakat_id'];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id', 'id_petugas');
    }

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'masyarakat_id', 'id');
    }
}
