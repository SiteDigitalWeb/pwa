
@extends ('adminsite.layout')

  @section('cabecera')
    @parent


    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    {{-- Select2 CSS --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/select2-bootstrap@1.4.0/select2-bootstrap.min.css">

   <style>
/* ===== SELECT2 TIPO TAGS (como Producto de interés) ===== */

.select2-container--bootstrap .select2-selection--multiple {
    min-height: 38px;
    padding: 4px 6px;
    border: 1px solid #d2d6de;
    border-radius: 4px;
    display: flex;
    align-items: center;
}

.select2-container--bootstrap
.select2-selection--multiple
.select2-selection__rendered {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 0;
}

/* TAG / CHIP */
.select2-container--bootstrap
.select2-selection--multiple
.select2-selection__choice {
    background-color: #00b3a4; /* mismo tono ProUI */
    color: #fff;
    border: none;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 13px;
    display: flex;
    align-items: center;
}

/* X cerrar */
.select2-container--bootstrap
.select2-selection--multiple
.select2-selection__choice__remove {
    color: #fff;
    font-weight: bold;
    margin-right: 6px;
    cursor: pointer;
}

.select2-container--bootstrap
.select2-selection--multiple
.select2-selection__choice__remove:hover {
    color: #ffecec;
}

/* INPUT de búsqueda inline */
.select2-search--inline .select2-search__field {
    height: 28px;
    margin: 0;
    padding: 4px;
    font-size: 13px;
}

/* Focus limpio */
.select2-container--bootstrap.select2-container--focus
.select2-selection {
    border-color: #00b3a4;
    box-shadow: none;
}
</style>


@stop

@section('ContenidoSite-01')

{{-- ALERTAS --}}
<div class="col-xs-10 col-sm-10 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1 col-sm-offset-1 col-xs-offset-1 topper">
    @if(session('success'))
        <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Notificación enviada correctamente</strong>
        </div>
    @endif

    @if(session('status') == 'error')
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <strong>Error al enviar la notificación</strong>
        </div>
    @endif
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12">

            <div class="block">
                <div class="block-title">
                    <h2><strong>Enviar</strong> Notificación PUSH</h2>
                </div>

                <form method="POST"
                      action="/admin/push/send"
                      class="form-horizontal">
                    @csrf

                    {{-- TÍTULO --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">Título</label>
                        <div class="col-md-9">
                            <input type="text"
                                   name="title"
                                   class="form-control"
                                   placeholder="Título de la notificación"
                                   required>
                        </div>
                    </div>

                    {{-- MENSAJE --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">Mensaje</label>
                        <div class="col-md-9">
                            <textarea name="body"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Contenido del mensaje"
                                      required></textarea>
                        </div>
                    </div>

                    {{-- URL --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">URL</label>
                        <div class="col-md-9">
                            <input type="text"
                                   name="url"
                                   class="form-control"
                                   placeholder="/app/pwa">
                        </div>
                    </div>

                    <hr>

                    {{-- SELECT2 USUARIOS --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">
                            Usuarios específicos
                        </label>
                        <div class="col-md-9">
                            <select name="users[]"
                                    id="users-select"
                                    class="form-control"
                                    multiple
                                    style="width:100%">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="help-block">
                                Busca y selecciona uno o varios usuarios
                            </span>
                        </div>
                    </div>

                    <hr>

                    {{-- SEGMENTOS --}}
                    <div class="form-group">
                        <label class="col-md-3 control-label">Rol</label>
                        <div class="col-md-9">
                            <select name="role" class="form-control">
                                <option value="">-- Rol --</option>
                                <option value="admin">Admin</option>
                                <option value="cliente">Cliente</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Ciudad</label>
                        <div class="col-md-9">
                            <select name="city" class="form-control">
                                <option value="">-- Ciudad --</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}">{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Estado</label>
                        <div class="col-md-9">
                            <select name="status" class="form-control">
                                <option value="">-- Estado --</option>
                                <option value="active">Activo</option>
                                <option value="inactive">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    {{-- BOTONES --}}
                    <div class="form-group form-actions">
                        <div class="col-md-9 col-md-offset-3">
                            <button type="submit"
                                    class="btn btn-sm btn-primary">
                                <i class="fa fa-send"></i> Enviar Notificación
                            </button>

                            <button type="reset"
                                    class="btn btn-sm btn-warning">
                                <i class="fa fa-repeat"></i> Limpiar
                            </button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
jQuery(document).ready(function($) {
    $('#users-select').select2({
        placeholder: 'Buscar usuarios...',
        allowClear: true,
        width: '100%',
        theme: 'bootstrap'
    });
});
</script>

  

@stop






