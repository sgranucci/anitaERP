<tr>
    <td>{{ date('j/n/Y', strtotime($renglon['fecha'] ?? '')) }}</td>
    <td>{{ $renglon['tipocomprobante'] }}</td>
    <td>{{ $renglon['nrocomprobante'] }}</td>
    <td align="right">{{ number_format($renglon['cantidad'], 0) }}</td>
    <td align="right">{{ number_format($renglon['cantidad_mov'], 0) }} {{ $renglon['unidad'] }}</td>
    <td align="right">$</td>
    <td align="right">({{ number_format(abs($renglon['importe']), 2, '.', ',') }})</td>
    <td>{{ $renglon['codigocliente'] }}</td>
    <td>{{ $renglon['nombrecliente'] }}</td>
</tr>
