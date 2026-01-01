@extends ('adminsite.layout')

@section('title', 'Gestión PWA Manifest')

@section('ContenidoSite-01')
<div class="card">
    <!--
    <div class="card-header d-flex justify-content-between align-items-center">
        <a href="{{ route('admin.pwa.create') }}" class="btn btn-primary">Nuevo Manifest</a>
    </div>
    -->
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($manifests->isEmpty())
            <div class="alert alert-info">
                No hay manifests configurados. <a href="{{ route('admin.pwa.create') }}">Crea el primero</a>.
            </div>
        @else

        <div class="container">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading"><h3>Configuración PWA </h3></div>
            <div class="panel-body">
                <table class="table table-condensed" style="border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Short Name</th>
                            <th>Start URL</th>
                            <th>Display</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach($manifests as $manifest)
                        <tr>
                        
                            <td>{{ $manifest->name }}</td>
                            <td>{{ $manifest->short_name }}</td>
                            <td>{{ $manifest->start_url }}</td>
                            <td><span class="badge bg-info">{{ $manifest->display }}</span></td>
                            <td>
                                <form action="{{ route('admin.pwa.toggle', $manifest) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-{{ $manifest->enabled ? 'success' : 'secondary' }}">
                                        {{ $manifest->enabled ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td>
                                
                                <div class="btn-group">
                                    <a href="{{ route('admin.pwa.edit', $manifest) }}" class="p-2 btn btn-warning btn-sm" title="Ver contenidos">
                                        <i class="gi gi-imac"></i>
                                    </a>
                                
                                    <a href="{{ route('manifest.json') }}" target="_blank" class="-2 btn btn-info btn-sm" title="Editar página">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>





        @endif
    </div>
</div>
@endsection