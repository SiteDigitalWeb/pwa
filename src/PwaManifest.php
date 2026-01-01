<?php

namespace Sitedigitalweb\Pwa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PwaManifest extends Model
{
    use HasFactory;

    protected $table = 'cms_pwa_manifests';

    protected $fillable = [
        'name',
        'short_name',
        'description',
        'start_url',
        'display',
        'background_color',
        'theme_color',
        'orientation',
        'scope',
        'lang',
        'dir',
        'icons',
        'screenshots',
        'shortcuts',
        'categories',
        'protocol_handlers',
        'enabled'
    ];

    protected $casts = [
        'icons' => 'array',
        'screenshots' => 'array',
        'shortcuts' => 'array',
        'categories' => 'array',
        'protocol_handlers' => 'array',
        'enabled' => 'boolean'
    ];

    // Obtener manifest activo
    public static function getActive()
    {
        return self::where('enabled', true)->first();
    }

    // Generar array para manifest.json
    public function toManifestArray()
    {
        return [
            'name' => $this->name,
            'short_name' => $this->short_name,
            'description' => $this->description,
            'start_url' => $this->start_url,
            'display' => $this->display,
            'background_color' => $this->background_color,
            'theme_color' => $this->theme_color,
            'orientation' => $this->orientation,
            'scope' => $this->scope,
            'lang' => $this->lang,
            'dir' => $this->dir,
            'icons' => $this->icons ?? $this->getDefaultIcons(),
            'screenshots' => $this->screenshots,
            'shortcuts' => $this->shortcuts,
            'categories' => $this->categories,
            'edge_side_panel' => [
                'preferred_width' => 400
            ],
            'launch_handler' => [
                'client_mode' => 'auto'
            ],
            'handle_links' => 'preferred',
            'protocol_handlers' => $this->protocol_handlers
        ];
    }

    // Iconos por defecto
    protected function getDefaultIcons()
    {
        return [
            [
                'src' => '/icons/icon-72x72.png',
                'sizes' => '72x72',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-96x96.png',
                'sizes' => '96x96',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-128x128.png',
                'sizes' => '128x128',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-152x152.png',
                'sizes' => '152x152',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-384x384.png',
                'sizes' => '384x384',
                'type' => 'image/png'
            ],
            [
                'src' => '/icons/icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png'
            ]
        ];
    }
}