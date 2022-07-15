<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoConference extends Model
{
    use HasFactory;

    protected $fillable = [
        'recording_file_name',
        'recording_file_size',
        'price',
        'recording_at',
        'recorded_at',
        'number_of_downloads'
    ];
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function satisfactions()
    {
        return $this->hasMany(Satisfaction::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->select(['id', 'email', 'video_conference_id']);
        ;
    }
}
