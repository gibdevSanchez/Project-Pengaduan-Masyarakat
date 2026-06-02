<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';

    protected $fillable = [
        'judul', 'isi', 'foto', 'kategori', 'format',
        'petugas_id', 'id_pengaduan', 'is_published',
        'mulai_tayang', 'selesai_tayang',
    ];

    protected $casts = [
        'is_published'   => 'boolean',
        'mulai_tayang'   => 'datetime',
        'selesai_tayang' => 'datetime',
    ];

    public function penulis()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id', 'id_petugas');
    }

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(fn($q) => $q->whereNull('mulai_tayang')->orWhere('mulai_tayang', '<=', now()))
            ->where(fn($q) => $q->whereNull('selesai_tayang')->orWhere('selesai_tayang', '>=', now()));
    }

    public function scopeAktifBesar($query)
    {
        return $query->where('format', 'besar')
            ->published()
            ->where(fn($q) => $q->whereNull('mulai_tayang')->orWhere('mulai_tayang', '<=', now()))
            ->where(fn($q) => $q->whereNull('selesai_tayang')->orWhere('selesai_tayang', '>=', now()));
    }
}
