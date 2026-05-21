<h2> Maestro de Clientes </h2>
<h1><strong>Desde: {{$desdecliente}} </strong>&nbsp;
	<strong>Hasta: {{$hastacliente}} </strong>&nbsp;
	<strong>Provincia desde: {{$desdeprovincia}} </strong>&nbsp;
	<strong>Provincia hasta: {{$hastaprovincia}} </strong>&nbsp;
	<strong>Estado de clientes: {{$estado}}</strong>
</h1>
<table>
	<thead>
    <tr>
       	<th>Cliente</th>
       	<th>Nombre</th>
		<th>Fantas&iacute;a</th>
       	<th>Direcc&oacute;n</th>
       	<th>Provincia</th>
       	<th>Localidad</th>
       	<th>Tel&eacute;fono</th>
		<th>E-mail</th>
       	<th>Estado</th>
		<th>Tipo de suspensi&oacute;n</th>
		<th>Leyenda</th>
    </tr>
  	</thead>
    <tbody>
	@foreach ($clientes as $data)
		@include('exports.ventas.reportecliente.imprimeunrenglon')
	@endforeach
	</tbody>
</table>
