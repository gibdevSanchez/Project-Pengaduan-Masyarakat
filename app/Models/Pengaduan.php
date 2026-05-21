<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use SoftDeletes;

    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'tgl_pengaduan', 'nik', 'isi_laporan', 'foto', 'status',
        'kategori', 'lokasi', 'id_petugas', 'selesai_at', 'takedown_reason',
    ];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'nik', 'nik');
    }

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function petugasAssigned()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    public function klarifikasi()
    {
        return $this->hasMany(Klarifikasi::class, 'id_pengaduan', 'id_pengaduan')
                    ->orderBy('created_at');
    }
}
