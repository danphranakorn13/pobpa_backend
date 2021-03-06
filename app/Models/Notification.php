<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_conference_id',
        'user_id',
        'email'
    ];

    public function videoConference()
    {
        return $this->belongsTo(VideoConference::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
