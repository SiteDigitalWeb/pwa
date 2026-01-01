<?php

namespace Sitedigitalweb\Pwa\Http;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{

 private function resolveUserModel()
    
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PushSubscription::class
        : \Sitedigitalweb\Pwa\PushSubscription::class;
    }

    public function subscribe(Request $request)
    {
        $model = $this->resolveUserModel();
        $user = auth('api')->user(); // 👈 MUY IMPORTANTE

        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $model::updateOrCreate(
            ['endpoint' => $request->endpoint],
            [
                'user_id'   => $user?->id,
                'public_key' => $request->keys['p256dh'],
                'auth_token' => $request->keys['auth'],
            ]
        );

        return response()->json([
            'message' => 'Suscripción guardada correctamente'
        ]);
    }

    public function unsubscribe(Request $request)
    {

    $model = $this->resolveUserModel();
    $request->validate([
        'endpoint' => 'required|string',
    ]);

    $model::where('endpoint', $request->endpoint)->delete();

    return response()->json([
        'message' => 'Suscripción eliminada correctamente'
    ]);
    }
}