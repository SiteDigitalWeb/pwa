<?php

namespace Sitedigitalweb\Pwa\Tenant;

use Illuminate\Database\Eloquent\Model;
use Sitedigitalweb\Pwa\Tenant\PushNotificationLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class PushNotification extends Model
{
    use UsesTenantConnection;
    use HasFactory;
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

