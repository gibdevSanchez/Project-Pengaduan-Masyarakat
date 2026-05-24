<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Masyarakat extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'masyarakat';

    protected $fillable = ['nik', 'nama', 'username', 'password', 'telp', 'foto_profil'];
    protected $hidden = ['password'];

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'masyarakat_id', 'id');
    }
}
