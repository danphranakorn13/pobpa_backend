<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryDownloadLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'video_conference_id',
        'transaction_id',
    ];

    public function videoConference()
    {
        return $this->belongsTo(VideoConference::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
