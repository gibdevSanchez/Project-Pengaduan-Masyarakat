<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klarifikasi extends Model
{
    protected $table = 'klarifikasi';
    protected $primaryKey = 'id_klarifikasi';

    protected $fillable = ['id_pengaduan', 'pesan', 'dari', 'id_pengirim'];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }
}
