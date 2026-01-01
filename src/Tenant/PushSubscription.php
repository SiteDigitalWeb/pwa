<?php

namespace Sitedigitalweb\Pwa\Tenant;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PushSubscription extends Model
{

    use UsesTenantConnection;
    protected $fillable = [
        'user_id',
        'endpoint',
        'public_key',
        'auth_token',
    ];

    public function user()
    {
    return $this->belongsTo(User::class);
    }
}
