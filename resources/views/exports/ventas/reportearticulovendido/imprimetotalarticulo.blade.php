<tr style="background-color: #FF9900;">
    <td colspan="3">
        <strong>Total {{ $articulo['codigo'] }} {{ \Illuminate\Support\Str::limit($articulo['nombre'], 20, '') }}</strong>
    </td>
    <td align="right"><strong>{{ number_format($articulo['total_cantidad'], 0) }}</strong></td>
    <td align="right"><strong>{{ number_format($articulo['total_cantidad_mov'], 0) }}</strong></td>
    <td align="right"><strong>$</strong></td>
    <td align="right"><strong>({{ number_format(abs($articulo['total_importe']), 2, '.', ',') }})</strong></td>
    <td colspan="2"></td>
</tr>
