<div class="row">
	<div class="col-sm-12">
		<div class="form-group row">
			<label for="ot" class="col-lg-3 col-form-label">Ordenes de trabajo a imprimir</label>
    		<div class="col-lg-8">
    			<input type="text" name="ordenestrabajo" id="ordenestrabajo" class="form-control" value="{{old('ordenestrabajo')}}" required>
			</div>
		</div>
	
		<div class="form-group row">
    		<label for="tipoetiqueta" class="col-lg-3 col-form-label requerido">Tipo de etiqueta</label>
			<select name="tipoetiqueta" class="col-lg-3 form-control" required>
    			<option value="">-- Elija tipo de etiqueta --</option>
       			@foreach($tipoetiqueta_enum as $value => $tipoetiqueta)
       				@if($value == 'CAJA')
       					<option value="{{ $value }}" selected="select">{{ $tipoetiqueta }}</option>    
       				@else
       					<option value="{{ $value }}">{{ $tipoetiqueta }}</option>    
       				@endif
       			@endforeach
			</select>
		</div>

	</div>
</div>