<?php

namespace Sitedigitalweb\Pwa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushNotification extends Model
{
    protected $fillable = [
        'title',
        'body',
        'url',
        'total',
        'sent',
        'failed'
    ];

    public function logs()
    {
        return $this->hasMany(PushNotificationLog::class);
    }
}




