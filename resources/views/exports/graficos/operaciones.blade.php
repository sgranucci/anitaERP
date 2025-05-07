<h2> OPERACIONES</h2>
<table> 
	<thead>
    <tr>
		<th>Fecha</th>
       	<th>ID Trade</th>
		<th>Direccion</th>
		<th>Nro.de Contratos</th>
       	<th>Hora in</th>
		<th>Valor Entrada</th>
		<th>Stop Loss</th>
		<th>Target 1</th>
		<th>Target 2</th>
		<th>Target 3</th>
		<th>Target 4</th>
		<th>RRR</th>
		<th>Swing Bars</th>
		<th>Contra Swing Bars</th>
		<th>RV</th>
		<th>Retroceso</th>
		<th>Riesgo (en ticks)</th>
		<th>Retorno (en ticks)</th>
		<th>Rango XTL Swing</th>
		<th>Velas sup.Tgt TQR SW</th>
		<th>Dif. PIVOT Tgt TQR SW</th>
		<th>Velas alcanzan TQR SW</th>
		<th>Peso XTL SW</th>
		<th>EWO Pivot SW</th>
		<th>Banda Sup Pivot SW</th>
		<th>Banda Inf Pivot SW</th>
		<th>Rango XTL Entrada</th>
		<th>Peso XTL Entrada</th>
		<th>EWO Entrada</th>
		<th>Banda Sup Entrada</th>
		<th>Banda Inf Entrada</th>
		<th>Precio de Cierre 1</th>
		<th>Hora Out 1</th>
		<th>Precio de Cierre 2</th>
		<th>Hora Out 2</th>
		<th>Precio de Cierre 3</th>
		<th>Hora Out 3</th>
		<th>Precio de Cierre 4</th>
		<th>Hora Out 4</th>
		<th>Total ticks</th>
		<th>P and L (en $)</th>
		<th>MPC</th>
		<th>MPF</th>
		<th>Salida</th>
	</tr>
  	</thead>
    <tbody>
	@foreach ($operaciones as $data)
	<tr>
		<td>{{$data['fecha']}}</td>	
		<td>{{$data['idTrade']}}</td>
		<td>{{$data['direccion']}}</td>
		<td>{{$data['numeroContratos']}}</td>
		<td>{{$data['desdeHora']}}</td>
		<td align="right">{{number_format(floatval($data['valorEntrada']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['stopLoss']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['t1']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['t2']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['t3']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['t4']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['rrr']), 8, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['swingBars']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['contraSwingBars']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['rv']), 8, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['retroceso']), 5, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['riesgoTicks']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['retornoTicks']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['rangoXTLAnterior']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['cantidadVelasSuperoTGTTQR']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['diferenciaPivotTGTTQR']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['cantidadVelasAlcanzaTQR']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['pesoXTLAnterior']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['ewoAnterior']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['bandaSupAnterior']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['bandaInfAnterior']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['rangoXTLActual']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['pesoXTLActual']), 0, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['ewo']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['bandaSup']), 4, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['bandaInf']), 4, ",", ".")}}</td>	
		<td align="right">{{number_format(floatval($data['precioCierre1']), 2, ",", ".")}}</td>
		<td>{{$data['horaCierre1']}}</td>
		<td align="right">{{number_format(floatval($data['precioCierre2']), 2, ",", ".")}}</td>
		<td>{{$data['horaCierre2']}}</td>
		<td align="right">{{number_format(floatval($data['precioCierre3']), 2, ",", ".")}}</td>
		<td>{{$data['horaCierre3']}}</td>
		<td align="right">{{number_format(floatval($data['precioCierre4']), 2, ",", ".")}}</td>
		<td>{{$data['horaCierre4']}}</td>
		<td align="right">{{number_format(floatval($data['totalTicks']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['plPesos']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['mpc']), 2, ",", ".")}}</td>
		<td align="right">{{number_format(floatval($data['mpf']), 2, ",", ".")}}</td>
		<td>{{$data['operacion']}}</td>
	</tr>
	@endforeach
	</tbody>
</table>
