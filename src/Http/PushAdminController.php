<?php

namespace Sitedigitalweb\Pwa\Http;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\User;
use App\Models\Notification;


class PushAdminController extends Controller
{
   public function index()
{
    return view('pwa::admin.push.index', [
        'users' => User::select('id','name','email')->get(),
        'cities' => User::select('pais_id')->distinct()->pluck('pais_id'),
    ]);
}

   private function resolveNotificationModel()
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pagina\Tenant\PushNotification::class
        : \Sitedigitalweb\Pagina\PushNotification::class;
    }
private function resolveNotificationLogModel()
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pagina\Tenant\PushNotificationLog::class
        : \Sitedigitalweb\Pagina\PushNotificationLog::class;
    }

 private function resolveUserModel(){
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PushSubscription::class
        : \Sitedigitalweb\Pwa\PushSubscription::class;
  }


 private function resolveNotiModel(){
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PushNotification::class
        : \Sitedigitalweb\Pwa\PushNotification::class;
  }



public function send(Request $request)
{
    $model = $this->resolveUserModel();
    $modellog = $this-> resolveNotificationLogModel();

    $request->validate([
        'title' => 'required',
        'body'  => 'required',
    ]);

    // 1️⃣ Resolver destinatarios
    $subscriptions = $model::query();

    if ($request->filled('users')) {
        $subscriptions->whereIn('user_id', $request->users);
    } else {
        $subscriptions->whereHas('user', function ($q) use ($request) {
            if ($request->filled('rol_id')) {
                $q->where('rol_id', $request->rol_id); // Corregido: 'role' por 'rol_id'
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
    $totalSubscriptions = $subscriptions->count();

    if ($totalSubscriptions === 0) {
        return back()->with('warning', 'No hay destinatarios para enviar la notificación.');
    }

    // 2️⃣ Crear notificación
    $notificationModel = $this->resolveNotificationModel();
    $notification = $notificationModel::create([
        'title' => $request->title,
        'body'  => $request->body,
        'url'   => $request->url ?? '/',
        'total' => $totalSubscriptions,
        'sent'  => 0,
        'failed' => 0,
    ]);

    // 3️⃣ Configurar WebPush
    $webPush = new WebPush([
        'VAPID' => [
            'subject' => config('push.subject'),
            'publicKey' => config('push.vapid_public_key'),
            'privateKey' => config('push.vapid_private_key'),
        ],
    ]);

    $payload = json_encode([
        'title' => $request->title,
        'body'  => $request->body,
        'url'   => $request->url ?? '/',
    ]);

    // 4️⃣ Encolar notificaciones
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

    // 5️⃣ Enviar y procesar resultados
    $successCount = 0;
    $failedCount = 0;

    foreach ($webPush->flush() as $report) {
        $endpoint = $report->getEndpoint();
        
        // Crear log para cada envío
        $modellog::create([
            'push_notification_id' => $notification->id,
            'endpoint' => $endpoint,
            'success' => $report->isSuccess(),
            'error' => !$report->isSuccess() ? $report->getReason() : null,
        ]);

        if ($report->isSuccess()) {
            $successCount++;
        } else {
            $failedCount++;
            // Eliminar suscripción fallida
            $model::where('endpoint', $endpoint)->delete();
        }
    }

    // 6️⃣ Actualizar estadísticas
    $notification->update([
        'sent' => $successCount,
        'failed' => $failedCount,
    ]);

    // 7️⃣ Mensaje de respuesta
    $message = "Notificación enviada. ";
    $message .= "Total: {$totalSubscriptions}, ";
    $message .= "Exitosos: {$successCount}, ";
    $message .= "Fallidos: {$failedCount}";

    return back()->with('success', $message);
}

function getSubscriptionsByTarget(array $target)
{
    $query = \App\Models\PushSubscription::query()
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
    $model = $this->resolveNotiModel();
    return view('pwa::admin.push.history', [
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