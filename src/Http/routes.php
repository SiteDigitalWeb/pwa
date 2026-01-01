<?php


Route::post('/push-subscribe', function (Illuminate\Http\Request $request) {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Usuario no autenticado'], 401);
    }
    $data = $request->all();
    $user->updatePushSubscription(
        $data['endpoint'],
        $data['keys']['p256dh'],
        $data['keys']['auth']
    );
    return response()->json(['success' => true]);
});





Route::middleware(['web'])->group(function () {
    Route::get('/admin/push', [Sitedigitalweb\Pwa\Http\PushAdminController::class, 'index']);
    Route::get('/admin/history', [Sitedigitalweb\Pwa\Http\PushAdminController::class, 'history']);
    Route::post('/admin/push/send', [Sitedigitalweb\Pwa\Http\PushAdminController::class, 'send']);
});


Route::prefix('admin')->name('admin.')->group(function () {
  Route::get('/dashboard', function () {
  return view('admin.dashboard');
  })->name('dashboard');
    
  Route::resource('pwa', Sitedigitalweb\Pagina\Http\PwaManifestController::class)
   ->except(['show'])
   ->parameters([
   'pwa' => 'pwaManifest'
  ]);    
    
  Route::patch('/pwa/{pwaManifest}/toggle', [Sitedigitalweb\Pagina\Http\PwaManifestController::class, 'toggle'])
   ->name('pwa.toggle');
});

Route::get('pwa/offline', function() {
    return view('pwa.offline');
})->name('pwa.offline');

Route::get('pwa/install', function() {
    return view('pwa.install');
})->name('pwa.install');

// Para API que pueda necesitar el Service Worker
Route::get('/api/pwa/status', function() {
    return response()->json([
        'status' => 'ok',
        'version' => '1.0.0',
        'timestamp' => now()
    ]);
});

Route::get('pwa/pwa-check', function() {
    // Detectar navegador basado en User-Agent
    $userAgent = request()->header('User-Agent', '');
    $browser = 'Unknown';
    
    if (stripos($userAgent, 'Chrome') !== false && stripos($userAgent, 'Edge') === false) {
        $browser = 'Chrome';
    } elseif (stripos($userAgent, 'Firefox') !== false) {
        $browser = 'Firefox';
    } elseif (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) {
        $browser = 'Safari';
    } elseif (stripos($userAgent, 'Edge') !== false) {
        $browser = 'Edge';
    } elseif (stripos($userAgent, 'Opera') !== false) {
        $browser = 'Opera';
    }
    
    // Verificar archivos
    $manifestExists = file_exists(public_path('manifest.json'));
    $iconsExist = file_exists(public_path('icons/icon-192x192.png'));
    $swExists = file_exists(public_path('sw.js'));
    $isHttps = request()->secure();
    $isLocal = app()->environment('local');
    
    // Calcular score
    $score = 0;
    $total = 4;
    if ($manifestExists) $score++;
    if ($swExists) $score++;
    if ($iconsExist) $score++;
    if ($isHttps || $isLocal) $score++;
    
    return response()->json([
        'status' => 'ok',
        'pwa' => [
            'supported' => in_array($browser, ['Chrome', 'Firefox', 'Safari', 'Edge']),
            'browser' => $browser,
            'requires_https' => $isHttps,
            'local_development' => $isLocal
        ],
        'files' => [
            'manifest' => [
                'exists' => $manifestExists,
                'path' => 'public/manifest.json',
                'size' => $manifestExists ? filesize(public_path('manifest.json')) : 0
            ],
            'service_worker' => [
                'exists' => $swExists,
                'path' => 'public/sw.js',
                'size' => $swExists ? filesize(public_path('sw.js')) : 0
            ],
            'icons' => [
                'exists' => $iconsExist,
                '192x192' => $iconsExist,
                '512x512' => file_exists(public_path('icons/icon-512x512.png'))
            ]
        ],
        'environment' => [
            'secure' => $isHttps,
            'env' => app()->environment(),
            'url' => config('app.url'),
            'host' => request()->getHost()
        ],
        'score' => [
            'current' => $score,
            'total' => $total,
            'percentage' => ($score / $total) * 100,
            'grade' => ($score / $total) * 100 >= 75 ? '✅ Good' : '⚠️ Needs Improvement'
        ],
        'instructions' => [
            '1. Verifica HTTPS: ' . ($isHttps ? '✅' : '❌'),
            '2. Archivo manifest.json: ' . ($manifestExists ? '✅' : '❌'),
            '3. Archivo sw.js: ' . ($swExists ? '✅' : '❌'),
            '4. Iconos: ' . ($iconsExist ? '✅' : '❌')
        ],
        'timestamp' => now()->toIso8601String()
    ]);
})->name('pwa.check');