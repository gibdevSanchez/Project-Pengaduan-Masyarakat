<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengaduan extends Model
{
    use SoftDeletes;

    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $casts = [
        'selesai_at' => 'datetime',
    ];

    protected $fillable = [
        'masyarakat_id', 'isi_laporan', 'status',
        'kategori', 'lokasi', 'lat', 'lng', 'tracking_code',
        'id_petugas', 'selesai_at', 'takedown_reason',
        'created_at', 'updated_at',
    ];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'masyarakat_id', 'id');
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

    public function fotos()
    {
        return $this->hasMany(PengaduanFoto::class, 'id_pengaduan', 'id_pengaduan');
    }

    public function scopeOverdue($query, array $slaHours): void
    {
        $query->whereIn('status', ['menunggu', 'proses'])
              ->where(function ($q) use ($slaHours) {
                  foreach ($slaHours as $kategori => $hours) {
                      $q->orWhere(fn($sub) =>
                          $sub->where('kategori', $kategori)
                              ->where('created_at', '<=', now()->subHours((int) $hours))
                      );
                  }
              });
    }

    public function isOverdue(array $slaHours): bool
    {
        if (!in_array($this->status, ['menunggu', 'proses'])) {
            return false;
        }
        $hours = $slaHours[$this->kategori] ?? $slaHours['lainnya'] ?? 72;
        return $this->created_at->addHours((int) $hours)->isPast();
    }
}
