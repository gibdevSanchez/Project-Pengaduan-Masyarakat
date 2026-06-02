<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedbackPenugasan extends Model
{
    protected $table = 'feedback_penugasan';

    protected $fillable = [
        'feedback_id', 'tipe', 'petugas_id',
        'status', 'pesan', 'hidden_by_petugas',
    ];

    protected $casts = [
        'hidden_by_petugas' => 'boolean',
    ];

    public function feedback()
    {
        return $this->belongsTo(Feedback::class, 'feedback_id');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'petugas_id', 'id_petugas');
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['selesai', 'invalid']);
    }
}
