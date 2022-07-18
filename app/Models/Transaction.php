<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_conference_id',
        'user_id',
        'price',
        'status',
        'payment_method',
        'response'
    ];

    public function videoConference()
    {
        return $this->belongsTo(VideoConference::class);
    }

    public function temporaryDownloadLink()
    {
        return $this->hasone(TemporaryDownloadLink::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
