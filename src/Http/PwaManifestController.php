<?php

namespace Sitedigitalweb\Pwa\Http;

use Sitedigitalweb\Pwa\PwaManifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;

class PwaManifestController extends Controller
{

  private function resolveUserModel()
    {
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    return $website 
        ? \Sitedigitalweb\Pwa\Tenant\PwaManifest::class
        : \Sitedigitalweb\Pwa\PwaManifest::class;
    }



    private function getPwaModel()
{
    $website = app(\Hyn\Tenancy\Environment::class)->website();
    
    if ($website) {
        // Estamos en tenant
        return \Sitedigitalweb\Pwa\Tenant\PwaManifest::class;
    } else {
        // Estamos en base central
        return \Sitedigitalweb\Pwa\PwaManifest::class;
    }
}
    public function index(){
    
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    // Si estamos en tenant, simplemente usamos la base del tenant
    if ($website) {
         $manifests = \Sitedigitalweb\Pwa\Tenant\PwaManifest::all();
    } else {
        // En la base central, también usamos sin filtro
        $manifests = PwaManifest::all();
    }
        return view('admin.pwa.index', compact('manifests'));
    }

    public function create()
    {
        return view('admin.pwa.create');
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'short_name' => 'required|string|max:50',
        'description' => 'nullable|string',
        'start_url' => 'nullable|string',
        'display' => 'required|in:standalone,fullscreen,minimal-ui,browser',
        'background_color' => 'required|string|max:7',
        'theme_color' => 'required|string|max:7',
        'orientation' => 'required|in:any,natural,landscape,portrait',
        'scope' => 'nullable|string',
        'lang' => 'required|string|max:10',
        'dir' => 'required|in:ltr,rtl,auto',
        'icons' => 'nullable|array',
        'screenshots' => 'nullable|array',
        'shortcuts' => 'nullable|array',
        'categories' => 'nullable|array',
        'protocol_handlers' => 'nullable|array',
        'enabled' => 'boolean'
    ]);

    // Si no se proporcionan iconos, usar valores por defecto
    if (empty($request->icons)) {
        $validated['icons'] = (new \Sitedigitalweb\Pwa\PwaManifest())->getDefaultIcons();
    } else {
        $validated['icons'] = $request->icons;
    }

    // Convertir arrays a JSON
    $validated['icons'] = json_encode($validated['icons']);
    $validated['screenshots'] = $request->screenshots ? json_encode($request->screenshots) : null;
    $validated['shortcuts'] = $request->shortcuts ? json_encode($request->shortcuts) : null;
    $validated['categories'] = $request->categories ? json_encode(explode(',', $request->categories)) : null;
    $validated['protocol_handlers'] = $request->protocol_handlers ? json_encode($request->protocol_handlers) : null;

    // Detectar si estamos en tenant o en base central
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    if ($website) {
        // Estamos en tenant - usar el modelo tenant
        $modelClass = \Sitedigitalweb\Pwa\Tenant\PwaManifest::class;
    } else {
        // Estamos en base central - usar el modelo central
        $modelClass = PwaManifest::class;
    }

    // Deshabilitar otros manifests del mismo entorno (tenant o central)
    if ($request->enabled) {
        $modelClass::where('id', '!=', 0)->update(['enabled' => false]);
    }

    // Crear el nuevo manifest
    $modelClass::create($validated);

    return redirect()->route('admin.pwa.index')
        ->with('success', 'Manifest creado exitosamente.');
}

   public function edit($id)
{
    // Detectar si estamos en tenant o en base central
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    if ($website) {
        // Estamos en tenant - usar el modelo tenant
        $pwaManifest = \Sitedigitalweb\Pwa\Tenant\PwaManifest::findOrFail($id);
    } else {
        // Estamos en base central - usar el modelo central
        $pwaManifest = \Sitedigitalweb\Pwa\PwaManifest::findOrFail($id);
    }
    
    return view('admin.pwa.edit', compact('pwaManifest'));
}

    public function update(Request $request, $id)
{
    // Obtener el modelo correcto según el contexto
    $modelClass = $this->getPwaModel();
    $pwaManifest = $modelClass::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'short_name' => 'required|string|max:50',
        'description' => 'nullable|string',
        'start_url' => 'nullable|string',
        'display' => 'required|in:standalone,fullscreen,minimal-ui,browser',
        'background_color' => 'required|string|max:7',
        'theme_color' => 'required|string|max:7',
        'orientation' => 'required|in:any,natural,landscape,portrait',
        'scope' => 'nullable|string',
        'lang' => 'required|string|max:10',
        'dir' => 'required|in:ltr,rtl,auto',
        'enabled' => 'boolean',
        'icons' => 'nullable|array',
        'screenshots' => 'nullable|array',
        'shortcuts' => 'nullable|array',
        'categories' => 'nullable|string',
        'protocol_handlers' => 'nullable|array'
    ]);

    // Procesar iconos si se proporcionan
    if ($request->has('icons')) {
        $icons = $this->processIcons($request->icons);
        $validated['icons'] = json_encode($icons);
    }

    // Procesar otros campos JSON
    if ($request->filled('screenshots')) {
        $validated['screenshots'] = json_encode($validated['screenshots']);
    }

    if ($request->filled('shortcuts')) {
        $validated['shortcuts'] = json_encode($validated['shortcuts']);
    }

    if ($request->filled('categories')) {
        $validated['categories'] = json_encode(explode(',', $validated['categories']));
    }

    if ($request->filled('protocol_handlers')) {
        $validated['protocol_handlers'] = json_encode($validated['protocol_handlers']);
    }

    // Deshabilitar otros manifests del mismo contexto si este se activa
    if ($request->enabled && !$pwaManifest->enabled) {
        $modelClass::where('id', '!=', $pwaManifest->id)
            ->update(['enabled' => false]);
    }

    $pwaManifest->update($validated);

    return redirect()->route('admin.pwa.index')
        ->with('success', 'Manifest actualizado exitosamente.');
}

/**
 * Procesar el array de iconos desde el formulario
 */
private function processIcons($iconInputs)
{
    $icons = [];
    
    if (isset($iconInputs['src']) && is_array($iconInputs['src'])) {
        foreach ($iconInputs['src'] as $index => $src) {
            if (!empty($src) && !empty($iconInputs['sizes'][$index])) {
                $icons[] = [
                    'src' => $src,
                    'sizes' => $iconInputs['sizes'][$index],
                    'type' => $iconInputs['type'][$index] ?? 'image/png',
                    'purpose' => $iconInputs['purpose'][$index] ?? 'any'
                ];
            }
        }
    }
    
    return $icons;
}


    public function destroy(PwaManifest $pwaManifest)
    {
        $pwaManifest->delete();
        return redirect()->route('admin.pwa.index')
            ->with('success', 'Manifest eliminado exitosamente.');
    }

    public function toggle(PwaManifest $pwaManifest)
    {
        if (!$pwaManifest->enabled) {
            PwaManifest::where('id', '!=', $pwaManifest->id)->update(['enabled' => false]);
        }
        
        $pwaManifest->update(['enabled' => !$pwaManifest->enabled]);
        
        return back()->with('success', 'Estado del manifest actualizado.');
    }


public function manifest()
{
    // Resolver tenant dinámicamente
    $model = $this->resolveUserModel();
    $manifest = $model::getActive();

    /*
     |--------------------------------------------------------------------------
     | Datos dinámicos por tenant
     |--------------------------------------------------------------------------
     */
    $tenantSlug = request()->getHost(); 
    // o: auth()->user()->tenant_id
    // o: tenant()->id
    // ajusta esta línea a tu sistema real
    $website = app(\Hyn\Tenancy\Environment::class)->website();

    if (empty($website->uuid)) {
    $baseIconPath = '/icons';
    } else {
    $baseIconPath = "/saas/{$website->uuid}/icons";
    }
    $defaultIcons = [
        [
            'src' => "{$baseIconPath}/icon-72x72.png",
            'sizes' => '72x72',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-96x96.png",
            'sizes' => '96x96',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        [
            'src' => "{$baseIconPath}/icon-128x128.png",
            'sizes' => '128x128',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        [
            'src' => "{$baseIconPath}/icon-144x144.png",
            'sizes' => '144x144',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-152x152.png",
            'sizes' => '152x152',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-180x180.png",
            'sizes' => '180x180',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-192x192.png",
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ],
        [
            'src' => "{$baseIconPath}/icon-256x256.png",
            'sizes' => '256x256',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-384x384.png",
            'sizes' => '384x384',
            'type' => 'image/png',
            'purpose' => 'any'
        ],
        [
            'src' => "{$baseIconPath}/icon-512x512.png",
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable'
        ]
    ];

    /*
     |--------------------------------------------------------------------------
     | Crear manifest si no existe (por tenant)
     |--------------------------------------------------------------------------
     */
    if (!$manifest) {
        $manifest = $model::create([
            'name' => config('app.name'),
            'short_name' => config('app.name'),
            'description' => 'Aplicación PWA',
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#000000',
            'orientation' => 'any',
            'scope' => '/',
            'lang' => 'es',
            'dir' => 'ltr',
            'enabled' => true,
            'icons' => json_encode($defaultIcons),
            'categories' => json_encode(['business', 'productivity']),
            'handle_links' => 'preferred'
        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | Decodificación segura
     |--------------------------------------------------------------------------
     */
    $icons = is_string($manifest->icons)
        ? json_decode($manifest->icons, true)
        : $manifest->icons;

    $categories = is_string($manifest->categories)
        ? json_decode($manifest->categories, true)
        : $manifest->categories;

    /*
     |--------------------------------------------------------------------------
     | Respuesta final
     |--------------------------------------------------------------------------
     */
    return response()->json([
        'name' => $manifest->name,
        'short_name' => $manifest->short_name,
        'description' => $manifest->description,
        'start_url' => $manifest->start_url,
        'display' => $manifest->display,
        'background_color' => $manifest->background_color,
        'theme_color' => $manifest->theme_color,
        'orientation' => $manifest->orientation,
        'scope' => $manifest->scope,
        'lang' => $manifest->lang,
        'dir' => $manifest->dir,

        // Siempre iconos correctos por tenant
        'icons' =>  $defaultIcons,

        'categories' => $categories ?: ['business', 'productivity'],
        'screenshots' => null,
        'shortcuts' => null,
        'handle_links' => $manifest->handle_links ?? 'preferred'
    ])
    ->header('Content-Type', 'application/manifest+json')
    ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
}
    
}
