<div class="form-group row">
	<label for="desdefecha" class="col-lg-3 col-form-label requerido">Desde fecha</label>
	<div class="col-lg-4">
		<input type="date" name="desdefecha" id="desdefecha" class="form-control" value="{{old('desdefecha', date('d-m-Y'))}}" required/>
	</div>
</div>
<div class="form-group row">
	<label for="desdehora" class="col-lg-3 col-form-label requerido">Desde hora:</label>
	<div class="col-lg-4">
		<input type="time" id="desdehora" name="desdehora" class="form-control">
	</div>
</div>
<div class="form-group row">
	<label for="hastafecha" class="col-lg-3 col-form-label requerido">Hasta fecha</label>
	<div class="col-lg-4">
		<input type="date" name="hastafecha" id="hastafecha" class="form-control" value="{{old('desdefecha', date('d-m-Y'))}}" required/>
	</div>
</div>
<div class="form-group row">
	<label for="hastahora" class="col-lg-3 col-form-label requerido">Hasta hora:</label>
	<div class="col-lg-4">
		<input type="time" id="hastahora" name="hastahora" class="form-control">
	</div>
</div>
<div class="form-group row">
	<label for="especie" class="col-lg-3 col-form-label requerido">Especie:</label>
	<div class="col-lg-4">
		<input type="text" id="especie" name="especie" class="form-control">
	</div>
</div>
<div class="form-group row">
	<label for="cantidadcontratos" class="col-lg-3 col-form-label requerido">Cantidad de contratos:</label>
	<div class="col-lg-4">
		<input type="number" name="cantidadcontratos" class="form-control" value="4">
	</div>
</div>
<div class="form-group row">
	<label for="compresion" class="col-lg-3 col-form-label requerido">Compresi&oacute;n:</label>
	
	<select name="compresion" class="col-lg-3 form-control" required>
    	<option value="">-- Elija la compresi&oacute;n --</option>
       	@foreach($compresion_enum as $value => $compresion)
			@if ($value == 2)
       			<option value="{{ $value }}" selected>{{ $compresion }}</option>    
			@else
			    <option value="{{ $value }}">{{ $compresion }}</option>
			@endif
    	@endforeach
	</select>
	
</div>
<div class="form-group row">
	<label for="administracionposicion" class="col-lg-3 col-form-label requerido">Administración de posición:</label>
	
	<select name="administracionposicion" id="administracionposicion" class="col-lg-3 form-control" required>
    	<option value="">-- Elija tipo de administración --</option>
       	@foreach($administracionPosicion_enum as $value => $administracionposicion)
			@if ($value == 'TP')
       			<option value="{{ $value }}" selected>{{ $administracionposicion }}</option>    
			@else
				<option value="{{ $value }}">{{ $administracionposicion }}</option>    
			@endif
    	@endforeach
	</select>
	<label for="tiempo" id="lavel-tiempo" class="col-lg-2 col-form-label requerido">Tiempo:</label>
	<input type="number" id="tiempo" name="tiempo" class="col-lg-1 form-control" value="{{30}}">
</div>
<div class="form-group row">
	<label for="filtroSetup" class="col-lg-3 col-form-label requerido">Filtro Setup:</label>
	
	<select name="filtroSetup" class="col-lg-3 form-control" required>
    	<option value="">-- Elija la filtro de setup --</option>
       	@foreach($filtroSetup_enum as $value => $filtroSetup)
			@if ($value == 'A')
       			<option value="{{ $value }}" selected>{{ $filtroSetup }}</option>    
			@else
			    <option value="{{ $value }}">{{ $filtroSetup }}</option>
			@endif
    	@endforeach
	</select>
	
</div>
<div class="form-group row">
	<input type="hidden" id="filtrosmatematicos" name="filtrosmatematicos" class="form-control" value="N">
</div>
<div class="form-group row">
	<label for="gatillo" class="col-lg-3 col-form-label requerido">Gatillo:</label>
	<select name="gatillo" class="col-lg-3 form-control" required>
		<option value="">-- Elija gatillo --</option>
		@foreach($gatillo_enum as $value => $gatillo)
			@if ($value == 'A')
				<option value="{{ $value }}" selected>{{ $gatillo }}</option>    
			@else
				<option value="{{ $value }}">{{ $gatillo }}</option>    
			@endif
		@endforeach
	</select>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group row">
			<label for="mmcorta" class="col-lg-3 col-form-label requerido">MM Corta:</label>
			<div class="col-lg-4">
				<input type="number" id="mmcorta" name="mmcorta" class="form-control" value="{{5}}">
			</div>
		</div>
		<div class="form-group row">
			<label for="mmlarga" class="col-lg-3 col-form-label requerido">MM Larga:</label>
			<div class="col-lg-4">
				<input type="number" id="mmlarga" name="mmlarga" class="form-control" value="{{35}}">
			</div>
		</div>
		<div class="form-group row">
			<input type="hidden" id="largovma" name="largovma" class="form-control" value="{{5}}">
		</div>
		<div class="form-group row">
			<input type="hidden" id="largocci" name="largocci" class="form-control" value="{{30}}">
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group row">
			<input type="hidden" id="largoxtl" name="largoxtl" class="form-control" value="{{35}}">
		</div>
		<div class="form-group row">
			<input type="hidden" id="umbralxtl" name="umbralxtl" class="form-control" value="{{37}}">
		</div>
		<div class="form-group row">
			<label for="swingsize" class="col-lg-3 col-form-label requerido">Swing Size:</label>
			<div class="col-lg-4">
				<input type="number" id="swingsize" name="swingsize" class="form-control" value="{{21}}">
			</div>
		</div>
		<div class="form-group row">
			<label for="rangodi" class="col-lg-3 col-form-label requerido">Rango Di:</label>
			<div class="col-lg-4">
				<input type="number" id="rangodi" name="rangodi" class="form-control" value="{{42}}">
			</div>
		</div>		
		<div class="form-group row">
			<input type="hidden" id="calculobase" name="calculobase" class="form-control" value="1">
		</div>
	</div>
</div>