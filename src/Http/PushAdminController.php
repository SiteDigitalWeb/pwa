<?php

namespace Sitedigitalweb\Pwa\Http;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\User;
use App\Models\Notification;
use App\Models\VapidKey;
use Hyn\Tenancy\Environment;


class PushAdminController extends Controller{

private function resolveVapid(): array
{
    $website = app(Environment::class)->website();

    // 🏢 CONTEXTO TENANT
    if ($website) {
        $vapid = VapidKey::where('website_id', $website->id)->first();

        if (!$vapid) {
            throw new \Exception('El tenant no tiene VAPID configurado');
        }

        return [
            'publicKey'  => $vapid->public_key,
            'privateKey' => $vapid->private_key,
            'subject'    => $vapid->subject ?? 'mailto:admin@sitekonecta.com',
        ];
    }

    // 🌍 CONTEXTO ROOT (DOMINIO PRINCIPAL)
    if (!config('push.vapid_public_key')) {
        throw new \Exception('No hay VAPID global configurado');
    }

    return [
        'publicKey'  => config('push.vapid_public_key'),
        'privateKey' => config('push.vapid_private_key'),
        'subject'    => config('push.subject'),
    ];
}



 private function resolveUserModel()
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PushSubscription::class
        : \Sitedigitalweb\Pwa\PushSubscription::class;
    }

     private function resolveNotificationModel()
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PushNotification::class
        : \Sitedigitalweb\Pwa\PushNotification::class;
    }



   public function index()
{
    return view('admin.push.index', [
        'users' => User::select('id','name','email')->get(),
        'cities' => User::select('pais_id')->distinct()->pluck('pais_id'),
    ]);
}


public function send(Request $request)
{

    $model = $this->resolveUserModel();

    $request->validate([
        'title' => 'required',
        'body'  => 'required',
    ]);

    // 1️⃣ Resolver destinatarios
    $subscriptions = $model::query();

    // 🎯 Usuarios específicos
    if ($request->filled('users')) {
        $subscriptions->whereIn('user_id', $request->users);
    }
    else {
        // 🧩 Segmentos
        $subscriptions->whereHas('user', function ($q) use ($request) {

            if ($request->filled('rol_id')) {
                $q->where('rol_id', $request->rol_id);
            }

            if ($request->filled('city')) {
                $q->where('city', $request->city);
            }

            if ($request->filled('status')) {
                $q->where('status', $request->status);
            }
        });
    }

    $subscriptions = $subscriptions->get();

    $vapid = $this->resolveVapid();

    $notificationModel = $this->resolveNotificationModel();

$notification = $notificationModel::create([
    'title' => $request->title,
    'body'  => $request->body,
    'url'   => $request->url ?? '/',
    'total' => $subscriptions->count(),
]);


$webPush = new WebPush([
    'VAPID' => [
        'subject'    => $vapid['subject'],
        'publicKey'  => $vapid['publicKey'],
        'privateKey' => $vapid['privateKey'],
    ],
]);

    $payload = json_encode([
        'title' => $request->title,
        'body'  => $request->body,
        'url'   => $request->url ?? '/',
    ]);

    foreach ($subscriptions as $sub) {
        $webPush->queueNotification(
            Subscription::create([
                'endpoint' => $sub->endpoint,
                'publicKey' => $sub->public_key,
                'authToken' => $sub->auth_token,
            ]),
            $payload
        );
    }

    // 3️⃣ Limpieza automática
    $sent = 0;
$failed = 0;

foreach ($webPush->flush() as $report) {

    $success = $report->isSuccess();

    $notification->logs()->create([
        'endpoint' => $report->getEndpoint(),
        'success'  => $success,
        'error'    => $success ? null : $report->getReason(),
    ]);

    if ($success) {
        $sent++;
    } else {
        $failed++;
        $model::where('endpoint', $report->getEndpoint())->delete();
    }
}
    $notification->update([
    'sent'   => $sent,
    'failed' => $failed,
]);

    return back()->with('success', 'Notificación enviada');


}

function getSubscriptionsByTarget(array $target)
{
    $model = $this->resolveUserModel();
    $query = $model::query()
        ->with('user');

    if (!empty($target['users'])) {
        $query->whereIn('user_id', $target['users']);
    }

    if (!empty($target['roles'])) {
        $query->whereHas('user', fn ($q) =>
            $q->whereIn('role', $target['roles'])
        );
    }

    if (!empty($target['cities'])) {
        $query->whereHas('user', fn ($q) =>
            $q->whereIn('city', $target['cities'])
        );
    }

    if (!empty($target['status'])) {
        $query->whereHas('user', fn ($q) =>
            $q->whereIn('status', $target['status'])
        );
    }

    return $query->get();
}



public function history()
{
    $model = $this->resolveNotificationModel();
    return view('admin.push.history', [
        'notifications' => $model::latest()->paginate(20)
    ]);
}

public function show($id)
{
    return view('admin.push.show', [
        'notification' => PushNotification::findOrFail($id),
        'logs' => PushNotificationLog::where('push_notification_id', $id)->paginate(50)
    ]);
}



}