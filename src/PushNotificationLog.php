<?php

namespace Sitedigitalweb\Pwa;
use Illuminate\Database\Eloquent\Model;

class PushNotificationLog extends Model
{
    protected $fillable = [
        'push_notification_id',
        'endpoint',
        'success',
        'error'
    ];

    public function notification()
    {
        return $this->belongsTo(PushNotification::class);
    }
}

