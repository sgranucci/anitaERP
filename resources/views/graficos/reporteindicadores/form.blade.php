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
	<label for="calculobase" class="col-lg-3 col-form-label requerido">C&aacute;lculo base:</label>
	
	<select name="calculobase" class="col-lg-3 form-control" required>
    	<option value="">-- Elija el c&aacute;lculo base --</option>
       	@foreach($calculoBase_enum as $value => $calculobase)
			@if ($value == 1)
       			<option value="{{ $value }}" selected>{{ $calculobase }}</option>    
			@else
				<option value="{{ $value }}">{{ $calculobase }}</option>    
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
			<label for="largovma" class="col-lg-3 col-form-label requerido">Largo Per&iacute;odo VMA:</label>
			<div class="col-lg-4">
				<input type="number" id="largovma" name="largovma" class="form-control" value="{{5}}">
			</div>
		</div>
		<div class="form-group row">
			<label for="largocci" class="col-lg-3 col-form-label requerido">Largo Per&iacute;odo CCI:</label>
			<div class="col-lg-4">
				<input type="number" id="largocci" name="largocci" class="form-control" value="{{30}}">
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="form-group row">
			<label for="largoxtl" class="col-lg-3 col-form-label requerido">Largo Per&iacute;odo XTL:</label>
			<div class="col-lg-4">
				<input type="number" id="largoxtl" name="largoxtl" class="form-control" value="{{35}}">
			</div>
		</div>
		<div class="form-group row">
			<label for="umbralxtl" class="col-lg-3 col-form-label requerido">Umbral XTL:</label>
			<div class="col-lg-4">
				<input type="number" id="umbralxtl" name="umbralxtl" class="form-control" value="{{37}}">
			</div>
		</div>
		<div class="form-group row">
			<label for="swingsize" class="col-lg-3 col-form-label requerido">Swing Size:</label>
			<div class="col-lg-4">
				<input type="number" id="swingsize" name="swingsize" class="form-control" value="{{7}}">
			</div>
		</div>
	</div>
</div>