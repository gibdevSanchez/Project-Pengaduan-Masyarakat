<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Petugas extends Authenticatable
{
    use Notifiable;

    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';

    protected $fillable = ['nama_petugas', 'username', 'password', 'telp', 'level', 'foto_profil', 'last_seen_at'];
    protected $hidden = ['password'];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function tanggapan()
    {
        return $this->hasMany(Tanggapan::class, 'id_petugas', 'id_petugas');
    }
}
