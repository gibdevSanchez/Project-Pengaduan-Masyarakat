<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = ['masyarakat_id', 'isi', 'foto', 'status'];

    public function masyarakat()
    {
        return $this->belongsTo(Masyarakat::class, 'masyarakat_id', 'id');
    }

    public function penugasan()
    {
        return $this->hasMany(FeedbackPenugasan::class, 'feedback_id');
    }
}
