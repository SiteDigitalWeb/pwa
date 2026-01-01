@extends('adminsite.layout')

@section('title', 'Editar PWA Manifest')

@php
    // Inicializar $errors si no existe
    if (!isset($errors)) {
        $errors = new \Illuminate\Support\ViewErrorBag;
    }
@endphp

@section('ContenidoSite-01')

<style>
    .invalid-feedback {
        color: #dc3545;
        display: block;
        margin-top: 5px;
    }
    
    .is-invalid {
        border-color: #dc3545;
    }
    
    .json-editor {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        max-height: 400px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    
    .color-preview {
        width: 40px;
        height: 38px;
        border: 1px solid #ced4da;
    }
</style>

<div class="content-header">
    <ul class="nav-horizontal text-center">
        <li><a href="{{ route('admin.pwa.index') }}"><i class="fa fa-list"></i> Ver PWA Manifests</a></li>
        <li class="active"><a href="{{ route('admin.pwa.edit', $pwaManifest) }}"><i class="fa fa-edit"></i> Editar PWA Manifest</a></li>
        <li><a href="{{ route('admin.pwa.create') }}"><i class="fa fa-plus"></i> Crear PWA Manifest</a></li>
    </ul>
</div>

@if(Session::has('status'))
<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1 col-sm-offset-1 col-xs-offset-1 topper">
    @if(Session::get('status') == 'ok_create')
      <div class="alert alert-success">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
       <strong>{{ Session::get('message') }}</strong>
      </div>
    @endif
    
    @if(Session::get('status') == 'ok_update')
      <div class="alert alert-info">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
       <strong>{{ Session::get('message') }}</strong>
      </div>
    @endif
    
    @if(Session::get('status') == 'ok_delete')
      <div class="alert alert-warning">
       <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
       <strong>{{ Session::get('message') }}</strong>
      </div>
    @endif
</div>
@endif

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="block">
                <div class="block-title">
                    <h2><strong>Editar</strong> PWA Manifest: {{ $pwaManifest->name }}</h2>
                </div>
                
                <form action="{{ route('admin.pwa.update', $pwaManifest) }}" method="POST" class="form-horizontal" id="pwaForm">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre completo *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name', $pwaManifest->name) }}" 
                                   placeholder="Ingrese nombre completo" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre corto *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control @error('short_name') is-invalid @enderror" 
                                   id="short_name" name="short_name" 
                                   value="{{ old('short_name', $pwaManifest->short_name) }}" 
                                   placeholder="Ingrese nombre corto (máx 50 caracteres)" 
                                   required maxlength="50">
                            @error('short_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Descripción</label>
                        <div class="col-md-9">
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" 
                                      rows="3" placeholder="Ingrese descripción de la aplicación">{{ old('description', $pwaManifest->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Start URL *</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control @error('start_url') is-invalid @enderror" 
                                   id="start_url" name="start_url" 
                                   value="{{ old('start_url', $pwaManifest->start_url) }}" 
                                   placeholder="Ej: /index.html" required>
                            @error('start_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Display Mode *</label>
                        <div class="col-md-9">
                            <select class="form-select form-control @error('display') is-invalid @enderror" 
                                    id="display" name="display" required>
                                <option value="standalone" {{ old('display', $pwaManifest->display) == 'standalone' ? 'selected' : '' }}>Standalone</option>
                                <option value="fullscreen" {{ old('display', $pwaManifest->display) == 'fullscreen' ? 'selected' : '' }}>Fullscreen</option>
                                <option value="minimal-ui" {{ old('display', $pwaManifest->display) == 'minimal-ui' ? 'selected' : '' }}>Minimal UI</option>
                                <option value="browser" {{ old('display', $pwaManifest->display) == 'browser' ? 'selected' : '' }}>Browser</option>
                            </select>
                            @error('display')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Orientación *</label>
                        <div class="col-md-9">
                            <select class="form-select form-control @error('orientation') is-invalid @enderror" 
                                    id="orientation" name="orientation" required>
                                <option value="any" {{ old('orientation', $pwaManifest->orientation) == 'any' ? 'selected' : '' }}>Cualquiera</option>
                                <option value="natural" {{ old('orientation', $pwaManifest->orientation) == 'natural' ? 'selected' : '' }}>Natural</option>
                                <option value="landscape" {{ old('orientation', $pwaManifest->orientation) == 'landscape' ? 'selected' : '' }}>Horizontal</option>
                                <option value="portrait" {{ old('orientation', $pwaManifest->orientation) == 'portrait' ? 'selected' : '' }}>Vertical</option>
                            </select>
                            @error('orientation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Color de fondo *</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text color-preview" id="bg-preview" 
                                      style="background-color: {{ old('background_color', $pwaManifest->background_color) }}"></span>
                                <input type="color" class="form-control form-control-color @error('background_color') is-invalid @enderror" 
                                       id="background_color" name="background_color" 
                                       value="{{ old('background_color', $pwaManifest->background_color) }}" required>
                                <input type="text" class="form-control" id="background_color_hex" 
                                       value="{{ old('background_color', $pwaManifest->background_color) }}" 
                                       maxlength="7" placeholder="#FFFFFF">
                            </div>
                            @error('background_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Color de tema *</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text color-preview" id="theme-preview" 
                                      style="background-color: {{ old('theme_color', $pwaManifest->theme_color) }}"></span>
                                <input type="color" class="form-control form-control-color @error('theme_color') is-invalid @enderror" 
                                       id="theme_color" name="theme_color" 
                                       value="{{ old('theme_color', $pwaManifest->theme_color) }}" required>
                                <input type="text" class="form-control" id="theme_color_hex" 
                                       value="{{ old('theme_color', $pwaManifest->theme_color) }}" 
                                       maxlength="7" placeholder="#000000">
                            </div>
                            @error('theme_color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Idioma *</label>
                        <div class="col-md-9">
                            <select class="form-select form-control @error('lang') is-invalid @enderror" 
                                    id="lang" name="lang" required>
                                <option value="es" {{ old('lang', $pwaManifest->lang) == 'es' ? 'selected' : '' }}>Español</option>
                                <option value="en" {{ old('lang', $pwaManifest->lang) == 'en' ? 'selected' : '' }}>English</option>
                                <option value="fr" {{ old('lang', $pwaManifest->lang) == 'fr' ? 'selected' : '' }}>Français</option>
                            </select>
                            @error('lang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Dirección texto *</label>
                        <div class="col-md-9">
                            <select class="form-select form-control @error('dir') is-invalid @enderror" 
                                    id="dir" name="dir" required>
                                <option value="ltr" {{ old('dir', $pwaManifest->dir) == 'ltr' ? 'selected' : '' }}>Izquierda a derecha</option>
                                <option value="rtl" {{ old('dir', $pwaManifest->dir) == 'rtl' ? 'selected' : '' }}>Derecha a izquierda</option>
                                <option value="auto" {{ old('dir', $pwaManifest->dir) == 'auto' ? 'selected' : '' }}>Automático</option>
                            </select>
                            @error('dir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Scope</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control @error('scope') is-invalid @enderror" 
                                   id="scope" name="scope" 
                                   value="{{ old('scope', $pwaManifest->scope) }}" 
                                   placeholder="Ej: /app/">
                            @error('scope')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Categorías</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control @error('categories') is-invalid @enderror" 
                                   id="categories" name="categories" 
                                   value="{{ old('categories', $pwaManifest->categories ? implode(', ', json_decode($pwaManifest->categories, true)) : 'productivity, business') }}" 
                                   placeholder="productivity, business, utilities">
                            <span class="help-block">Separadas por comas (ej: productivity, business, utilities)</span>
                            @error('categories')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Sección de Iconos --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">Iconos PWA</label>
                        <div class="col-md-9">
                            <div id="icons-container">
                                @php
                                    $icons = json_decode($pwaManifest->icons, true) ?: [];
                                    if (empty($icons)) {
                                        $icons = [
                                            ['src' => '/icons/icon-72x72.png', 'sizes' => '72x72', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-96x96.png', 'sizes' => '96x96', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-128x128.png', 'sizes' => '128x128', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-144x144.png', 'sizes' => '144x144', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-152x152.png', 'sizes' => '152x152', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-192x192.png', 'sizes' => '192x192', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-384x384.png', 'sizes' => '384x384', 'type' => 'image/png'],
                                            ['src' => '/icons/icon-512x512.png', 'sizes' => '512x512', 'type' => 'image/png']
                                        ];
                                    }
                                @endphp
                                
                                @foreach($icons as $index => $icon)
                                <div class="row mb-3 icon-row">
                                    <div class="col-md-4">
                                        <label class="control-label">Ruta del icono *</label>
                                        <input type="text" class="form-control icon-src" name="icons[src][]" 
                                               value="{{ old('icons.src.' . $index, $icon['src']) }}" 
                                               placeholder="/icons/icon-72x72.png" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Tamaño *</label>
                                        <input type="text" class="form-control icon-size" name="icons[sizes][]" 
                                               value="{{ old('icons.sizes.' . $index, $icon['sizes']) }}" 
                                               placeholder="72x72" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label">Tipo MIME</label>
                                        <select class="form-select form-control icon-type" name="icons[type][]">
                                            <option value="image/png" {{ ($icon['type'] ?? 'image/png') == 'image/png' ? 'selected' : '' }}>PNG</option>
                                            <option value="image/jpeg" {{ ($icon['type'] ?? 'image/png') == 'image/jpeg' ? 'selected' : '' }}>JPEG</option>
                                            <option value="image/svg+xml" {{ ($icon['type'] ?? 'image/png') == 'image/svg+xml' ? 'selected' : '' }}>SVG</option>
                                            <option value="image/webp" {{ ($icon['type'] ?? 'image/png') == 'image/webp' ? 'selected' : '' }}>WebP</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        @if($index > 0)
                                            <button type="button" class="btn btn-danger btn-sm remove-icon">
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <button type="button" id="add-icon" class="btn btn-secondary btn-sm mt-2">
                                <i class="fa fa-plus"></i> Agregar otro icono
                            </button>
                            
                            <div class="alert alert-info mt-3">
                                <strong>Tamaños recomendados:</strong> 72x72, 96x96, 128x128, 144x144, 152x152, 192x192, 384x384, 512x512
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Estado</label>
                        <div class="col-md-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="enabled" name="enabled" 
                                           value="1" {{ old('enabled', $pwaManifest->enabled) ? 'checked' : '' }}>
                                    Activar este manifest
                                </label>
                            </div>
                            @error('enabled')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group form-actions">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fa fa-save"></i> Actualizar
                            </button>
                            <a href="{{ route('admin.pwa.index') }}" class="btn btn-sm btn-warning">
                                <i class="fa fa-times"></i> Cancelar
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Vista previa JSON -->
            <div class="block">
                <div class="block-title">
                    <h2><strong>Vista previa</strong> del JSON</h2>
                </div>
                <div class="block-content">
                    <pre id="json-preview" class="json-editor bg-light p-3 rounded">
                        {{-- Este contenido será reemplazado por JavaScript --}}
                    </pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando vista previa JSON...');
    
    // Función para obtener iconos del formulario
    function getIconsFromForm() {
        const icons = [];
        document.querySelectorAll('.icon-row').forEach(function(row) {
            const src = row.querySelector('.icon-src')?.value;
            const size = row.querySelector('.icon-size')?.value;
            const type = row.querySelector('.icon-type')?.value;
            
            if (src && size) {
                icons.push({
                    src: src,
                    sizes: size,
                    type: type || 'image/png'
                });
            }
        });
        return icons;
    }

    // Función para actualizar la vista previa del JSON
    function updateJsonPreview() {
        const categoriesValue = document.getElementById('categories').value;
        const categoriesArray = categoriesValue 
            ? categoriesValue.split(',').map(c => c.trim()).filter(c => c)
            : [];

        const formData = {
            name: document.getElementById('name').value,
            short_name: document.getElementById('short_name').value,
            description: document.getElementById('description').value,
            start_url: document.getElementById('start_url').value,
            display: document.getElementById('display').value,
            background_color: document.getElementById('background_color').value,
            theme_color: document.getElementById('theme_color').value,
            orientation: document.getElementById('orientation').value,
            scope: document.getElementById('scope').value,
            lang: document.getElementById('lang').value,
            dir: document.getElementById('dir').value,
            categories: categoriesArray,
            enabled: document.getElementById('enabled').checked,
            icons: getIconsFromForm(),
            edge_side_panel: { preferred_width: 400 },
            launch_handler: { client_mode: 'auto' },
            handle_links: 'preferred'
        };

        const jsonPreview = document.getElementById('json-preview');
        jsonPreview.textContent = JSON.stringify(formData, null, 2);
    }

    // Actualizar colores
    function updateColorPreview(colorInputId, previewId, hexInputId) {
        const colorInput = document.getElementById(colorInputId);
        const preview = document.getElementById(previewId);
        const hexInput = document.getElementById(hexInputId);
        
        if (colorInput && preview && hexInput) {
            const color = colorInput.value;
            preview.style.backgroundColor = color;
            hexInput.value = color;
        }
    }

    // Configurar eventos para actualizar JSON en tiempo real
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(function(input) {
        input.addEventListener('input', updateJsonPreview);
        input.addEventListener('change', updateJsonPreview);
    });

    // Eventos específicos para colores
    const backgroundColorInput = document.getElementById('background_color');
    const themeColorInput = document.getElementById('theme_color');
    const bgHexInput = document.getElementById('background_color_hex');
    const themeHexInput = document.getElementById('theme_color_hex');

    if (backgroundColorInput) {
        backgroundColorInput.addEventListener('input', function() {
            updateColorPreview('background_color', 'bg-preview', 'background_color_hex');
            updateJsonPreview();
        });
    }

    if (themeColorInput) {
        themeColorInput.addEventListener('input', function() {
            updateColorPreview('theme_color', 'theme-preview', 'theme_color_hex');
            updateJsonPreview();
        });
    }

    if (bgHexInput) {
        bgHexInput.addEventListener('input', function() {
            let color = this.value;
            if (color && !color.startsWith('#')) {
                color = '#' + color;
            }
            if (backgroundColorInput) {
                backgroundColorInput.value = color;
                updateColorPreview('background_color', 'bg-preview', 'background_color_hex');
                updateJsonPreview();
            }
        });
    }

    if (themeHexInput) {
        themeHexInput.addEventListener('input', function() {
            let color = this.value;
            if (color && !color.startsWith('#')) {
                color = '#' + color;
            }
            if (themeColorInput) {
                themeColorInput.value = color;
                updateColorPreview('theme_color', 'theme-preview', 'theme_color_hex');
                updateJsonPreview();
            }
        });
    }

    // Manejar iconos dinámicos
    const addIconBtn = document.getElementById('add-icon');
    if (addIconBtn) {
        addIconBtn.addEventListener('click', function() {
            const iconsContainer = document.getElementById('icons-container');
            if (iconsContainer) {
                const newRow = document.createElement('div');
                newRow.className = 'row mb-3 icon-row';
                newRow.innerHTML = `
                    <div class="col-md-4">
                        <label class="control-label">Ruta del icono *</label>
                        <input type="text" class="form-control icon-src" name="icons[src][]" 
                               placeholder="/icons/icon-512x512.png" required>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Tamaño *</label>
                        <input type="text" class="form-control icon-size" name="icons[sizes][]" 
                               placeholder="512x512" required>
                    </div>
                    <div class="col-md-3">
                        <label class="control-label">Tipo MIME</label>
                        <select class="form-select form-control icon-type" name="icons[type][]">
                            <option value="image/png">PNG</option>
                            <option value="image/jpeg">JPEG</option>
                            <option value="image/svg+xml">SVG</option>
                            <option value="image/webp">WebP</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-icon">
                            <i class="fa fa-trash"></i> Eliminar
                        </button>
                    </div>
                `;
                
                iconsContainer.appendChild(newRow);
                
                // Agregar eventos a los nuevos inputs
                const newInputs = newRow.querySelectorAll('input, select');
                newInputs.forEach(function(input) {
                    input.addEventListener('input', updateJsonPreview);
                    input.addEventListener('change', updateJsonPreview);
                });
                
                updateJsonPreview();
            }
        });
    }

    // Delegación de eventos para eliminar iconos
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-icon') || 
            e.target && e.target.closest('.remove-icon')) {
            const removeBtn = e.target.classList.contains('remove-icon') ? e.target : e.target.closest('.remove-icon');
            const iconRow = removeBtn.closest('.icon-row');
            if (iconRow) {
                iconRow.remove();
                updateJsonPreview();
            }
        }
    });

    // Eventos para iconos existentes
    document.addEventListener('input', function(e) {
        if (e.target && (e.target.classList.contains('icon-src') || 
                         e.target.classList.contains('icon-size') || 
                         e.target.classList.contains('icon-type'))) {
            updateJsonPreview();
        }
    });

    // Inicializar vista previa JSON
    updateJsonPreview();
    console.log('Vista previa JSON inicializada');
});
</script>
@endpush