<h2>{{ $titulo }}</h2>
<h1>
    <strong>Desde: {{ date('d/m/Y', strtotime($desdefecha ?? '')) }}</strong>&nbsp;
    <strong>Hasta: {{ date('d/m/Y', strtotime($hastafecha ?? '')) }}</strong>&nbsp;
    <strong>Marca: {{ $marca }}</strong>
</h1>
<table>
    <tbody>
    @foreach ($articulos as $articulo)
        <tr>
            <td colspan="9">
                <strong>Art.:</strong> {{ $articulo['codigo'] }} {{ $articulo['nombre'] }}
                <strong>Agr.:</strong> {{ $articulo['agrupacion'] }}
            </td>
        </tr>
        @foreach ($articulo['renglones'] as $renglon)
            @include('exports.ventas.reportearticulovendido.imprimeunrenglon', ['renglon' => $renglon])
        @endforeach
        @include('exports.ventas.reportearticulovendido.imprimetotalarticulo', ['articulo' => $articulo])
    @endforeach
    @if (count($articulos) > 0)
        @include('exports.ventas.reportearticulovendido.imprimetotalfinal', ['totales' => $totales])
    @endif
    </tbody>
</table>
