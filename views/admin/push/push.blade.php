<h2>📜 Historial de notificaciones</h2>

<table border="1">
<tr>
    <th>Título</th>
    <th>Mensaje</th>
    <th>Destino</th>
    <th>Fecha</th>
</tr>

@foreach($notifications as $n)
<tr>
    <td>{{ $n->title }}</td>
    <td>{{ $n->body }}</td>
    <td>
        {{ $n->target === 'all' ? 'Todos' : 'Usuarios específicos' }}
    </td>
    <td>{{ $n->created_at }}</td>
</tr>
@endforeach
</table>
