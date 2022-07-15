<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satisfaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_conference_id',
        'fullname',
        'email',
        'ease',
        'stability',
        'sharpness',
        'comment',
    ];

    public function videoConference()
    {
        return $this->belongsTo(VideoConference::class);
    }
}
