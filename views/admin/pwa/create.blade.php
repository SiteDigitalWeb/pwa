@extends('layouts.admin')

@section('title', 'Crear PWA Manifest')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo PWA Manifest</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.pwa.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="short_name" class="form-label">Nombre corto * (máx 50 chars)</label>
                        <input type="text" class="form-control" id="short_name" name="short_name" 
                               value="{{ old('short_name') }}" required maxlength="50">
                        @error('short_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                @error('description')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_url" class="form-label">Start URL *</label>
                        <input type="text" class="form-control" id="start_url" name="start_url" 
                               value="{{ old('start_url', '/') }}" required>
                        @error('start_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="display" class="form-label">Display Mode *</label>
                        <select class="form-select" id="display" name="display" required>
                            <option value="standalone" {{ old('display', 'standalone') == 'standalone' ? 'selected' : '' }}>Standalone</option>
                            <option value="fullscreen" {{ old('display') == 'fullscreen' ? 'selected' : '' }}>Fullscreen</option>
                            <option value="minimal-ui" {{ old('display') == 'minimal-ui' ? 'selected' : '' }}>Minimal UI</option>
                            <option value="browser" {{ old('display') == 'browser' ? 'selected' : '' }}>Browser</option>
                        </select>
                        @error('display')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="orientation" class="form-label">Orientación *</label>
                        <select class="form-select" id="orientation" name="orientation" required>
                            <option value="any" {{ old('orientation', 'any') == 'any' ? 'selected' : '' }}>Cualquiera</option>
                            <option value="natural" {{ old('orientation') == 'natural' ? 'selected' : '' }}>Natural</option>
                            <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>Horizontal</option>
                            <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>Vertical</option>
                        </select>
                        @error('orientation')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="background_color" class="form-label">Color de fondo *</label>
                        <div class="input-group">
                            <span class="input-group-text color-preview" id="bg-preview"></span>
                            <input type="color" class="form-control form-control-color" id="background_color" name="background_color" 
                                   value="{{ old('background_color', '#ffffff') }}" required>
                            <input type="text" class="form-control" id="background_color_hex" 
                                   value="{{ old('background_color', '#ffffff') }}" maxlength="7">
                        </div>
                        @error('background_color')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="theme_color" class="form-label">Color de tema *</label>
                        <div class="input-group">
                            <span class="input-group-text color-preview" id="theme-preview"></span>
                            <input type="color" class="form-control form-control-color" id="theme_color" name="theme_color" 
                                   value="{{ old('theme_color', '#000000') }}" required>
                            <input type="text" class="form-control" id="theme_color_hex" 
                                   value="{{ old('theme_color', '#000000') }}" maxlength="7">
                        </div>
                        @error('theme_color')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="lang" class="form-label">Idioma *</label>
                        <select class="form-select" id="lang" name="lang" required>
                            <option value="es" {{ old('lang', 'es') == 'es' ? 'selected' : '' }}>Español</option>
                            <option value="en" {{ old('lang') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="fr" {{ old('lang') == 'fr' ? 'selected' : '' }}>Français</option>
                        </select>
                        @error('lang')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="dir" class="form-label">Dirección texto *</label>
                        <select class="form-select" id="dir" name="dir" required>
                            <option value="ltr" {{ old('dir', 'ltr') == 'ltr' ? 'selected' : '' }}>Izquierda a derecha</option>
                            <option value="rtl" {{ old('dir') == 'rtl' ? 'selected' : '' }}>Derecha a izquierda</option>
                            <option value="auto" {{ old('dir') == 'auto' ? 'selected' : '' }}>Automático</option>
                        </select>
                        @error('dir')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="scope" class="form-label">Scope</label>
                        <input type="text" class="form-control" id="scope" name="scope" 
                               value="{{ old('scope', '/') }}">
                        @error('scope')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="categories" class="form-label">Categorías (separadas por comas)</label>
                <input type="text" class="form-control" id="categories" name="categories" 
                       value="{{ old('categories', 'productivity, business') }}" 
                       placeholder="productivity, business, utilities">
                <div class="form-text">Ejemplo: productivity, business, utilities, education</div>
                @error('categories')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="enabled" name="enabled" 
                       value="1" {{ old('enabled') ? 'checked' : '' }}>
                <label class="form-check-label" for="enabled">
                    Activar este manifest
                </label>
                @error('enabled')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.pwa.index') }}" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Manifest</button>
            </div>
        </form>
    </div>
</div>

<!-- Vista previa JSON -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">Vista previa del JSON</h6>
    </div>
    <div class="card-body">
        <pre id="json-preview" class="json-editor bg-light p-3 rounded"></pre>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Actualizar vista previa JSON
    function updateJsonPreview() {
        const formData = {
            name: $('#name').val(),
            short_name: $('#short_name').val(),
            description: $('#description').val(),
            start_url: $('#start_url').val(),
            display: $('#display').val(),
            background_color: $('#background_color').val(),
            theme_color: $('#theme_color').val(),
            orientation: $('#orientation').val(),
            scope: $('#scope').val(),
            lang: $('#lang').val(),
            dir: $('#dir').val(),
            categories: $('#categories').val().split(',').map(c => c.trim()).filter(c => c),
            enabled: $('#enabled').is(':checked'),
            icons: [
                { src: "/icons/icon-72x72.png", sizes: "72x72", type: "image/png" },
                { src: "/icons/icon-96x96.png", sizes: "96x96", type: "image/png" },
                { src: "/icons/icon-128x128.png", sizes: "128x128", type: "image/png" },
                { src: "/icons/icon-144x144.png", sizes: "144x144", type: "image/png" },
                { src: "/icons/icon-152x152.png", sizes: "152x152", type: "image/png" },
                { src: "/icons/icon-192x192.png", sizes: "192x192", type: "image/png" },
                { src: "/icons/icon-384x384.png", sizes: "384x384", type: "image/png" },
                { src: "/icons/icon-512x512.png", sizes: "512x512", type: "image/png" }
            ],
            edge_side_panel: { preferred_width: 400 },
            launch_handler: { client_mode: 'auto' },
            handle_links: 'preferred'
        };

        $('#json-preview').text(JSON.stringify(formData, null, 2));
    }

    // Actualizar en tiempo real
    $('input, select, textarea').on('input change', updateJsonPreview);
    
    // Inicializar colores
    function updateColorPreview(inputId, previewId) {
        const color = $(inputId).val();
        $(previewId).css('background-color', color);
        $(inputId + '_hex').val(color);
    }
    
    // Inicializar
    updateColorPreview('#background_color', '#bg-preview');
    updateColorPreview('#theme_color', '#theme-preview');
    updateJsonPreview();
    
    // Eventos para colores
    $('#background_color, #theme_color').on('input', function() {
        const id = '#' + $(this).attr('id');
        const previewId = id === '#background_color' ? '#bg-preview' : '#theme-preview';
        updateColorPreview(id, previewId);
        updateJsonPreview();
    });
    
    $('#background_color_hex, #theme_color_hex').on('input', function() {
        const isBg = $(this).attr('id') === 'background_color_hex';
        const colorInput = isBg ? '#background_color' : '#theme_color';
        const previewId = isBg ? '#bg-preview' : '#theme-preview';
        $(colorInput).val($(this).val());
        updateColorPreview(colorInput, previewId);
        updateJsonPreview();
    });
});
</script>
@endpush