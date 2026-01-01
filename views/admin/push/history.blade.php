@extends('adminsite.layout')

@section('ContenidoSite-01')
<div class="card">
    <div class="card-body">
        <div class="container">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><h3>Configuración PWA</h3></div>
                    <div class="panel-body">
                        <table id="pwaTable" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Mensaje</th>
                                    <th>Destino</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $n)
                                <tr>
                                    <td>{{ $n->title }}</td>
                                    <td>{{ $n->body }}</td>
                                    <td>{{ $n->target === 'all' ? 'Todos' : 'Usuarios específicos' }}</td>
                                    <td><span class="badge bg-success">{{ $n->created_at }}</span></td>
                                    <td>
                                        <!-- Aquí debes agregar el estado si existe -->
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="" class="p-2 btn btn-warning btn-sm" title="Ver contenidos">
                                                <i class="gi gi-imac"></i>
                                            </a>
                                            <a href="{{ route('manifest.json') }}" target="_blank" class="p-2 btn btn-info btn-sm" title="Editar página">
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
    </div>
</div>
@stop

@section('styles')
@parent
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap4.min.css">
@stop

@section('scripts')
@parent
<!-- jQuery debe cargarse primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables con opciones mejoradas
    $('#pwaTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "responsive": true,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
        "pageLength": 10,
        "order": [[3, 'desc']], // Ordenar por la cuarta columna (Fecha) descendente
        "columnDefs": [
            {
                "targets": [5], // Columna de acciones
                "orderable": false,
                "searchable": false
            }
        ]
    });
    
    // Tooltips
    $('[title]').tooltip({
        trigger: 'hover',
        placement: 'top'
    });
    
    // Confirmación para eliminar (si agregas botones de eliminar)
    $('.delete-btn').click(function(e) {
        if(!confirm('¿Estás seguro de eliminar esta notificación?')) {
            e.preventDefault();
        }
    });
});
</script>
@stop
