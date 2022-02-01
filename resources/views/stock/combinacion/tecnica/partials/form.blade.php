<div id="printableArea">
<div class="card">
	<div class="card-body">
		<div class="row">
    		<div class="col-sm-6">
				<div class="form-group row">
    				<label for="articulo_id" class="col-lg-4 col-form-label requerido">Art&iacute;culo</label>
					<select id="articulo_id" name="articulo_id" class="col-lg-8 form-control" readonly>
                        @foreach($articulo as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->articulo_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->descripcion }}-{{ $value->sku }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group row">
                    <label for="" class="col-lg-4 col-form-label"> Observaciones </label>
                    <div class="py-2"> {{ $combinacion->observacion }} </div>
                </div>
				<div class="form-group row">
    				<label for="plvista_id" class="col-lg-4 col-form-label requerido">Plantilla a la vista</label>
					<select id="plvista_id" name="plvista_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($plvista as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->plvista_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="plarmado_id" class="col-lg-4 col-form-label requerido">Plantilla de armado</label>
					<select id="plarmado_id" name="plarmado_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($plarmado as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->plarmado_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="foto" class="col-lg-3 col-form-label">Foto</label>
    				<div class="col-lg-8">
        				<input type="file" name="foto_up" id="foto" data-initial-preview="{{isset($combinacion->foto) ? asset("storage/imagenes/fotos_articulos/$combinacion->foto") : ''}}" accept="image/*"/>
    				</div>
				</div>
        	</div>
    		<div class="col-sm-6">
				<div class="form-group row">
    				<label for="fondo_id" class="col-lg-4 col-form-label requerido">Fondo</label>
					<select id="fondo_id" name="fondo_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($fondo as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->fondo_id )
                                <option value="{{ $value->id }}" selected="select">{{$value->nombre}}-{{ $value->codigo }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{$value->nombre}}-{{ $value->codigo }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="colorfondo_id" class="col-lg-4 col-form-label requerido">Color del fondo</label>
					<select id="colorfondo_id" name="colorfondo_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($color as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->colorfondo_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="horma_id" class="col-lg-4 col-form-label requerido">Horma</label>
					<select id="horma_id" name="horma_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($horma as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->horma_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
				<div class="form-group row">
    				<label for="serigrafia_id" class="col-lg-4 col-form-label requerido">Serigraf&iacute;a</label>
					<select id="serigrafia_id" name="serigrafia_id" class="col-lg-8 form-control">
                        <option value="">-- Seleccionar --</option>
                        @foreach($serigrafia as $key => $value)
                            @if( isset($combinacion) && (int) $value->id == (int) $combinacion->serigrafia_id )
                                <option value="{{ $value->id }}" selected="select">{{ $value->nombre }}</option>    
                            @else
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>    
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <table class="table" id="capelladas_table">
    	<thead>
    		<tr>
    			<th>Material Capellada</th>
    			<th>Color</th>
    			<th>Piezas</th>
    			<th>16/26</th>
    			<th>27/33</th>
    			<th>34/40</th>
    			<th>41/45</th>
    			<th>Tipo</th>
    		</tr>
    	</thead>
    	<tbody>
		 	@if (count($combinacion->capearts) > 0)
				@foreach (old('capelladas', $combinacion->capearts ? $combinacion->capearts : ['']) as $combinacion_capeart)
               		<tr id="capellada{{ $loop->index }}">
                		<td>
                			<select name="materiales[]" class="form-control">
                				<option value="">-- Elija material --</option>
                				@foreach ($capelladas as $material)
                					<option value="{{ $material->id }}"
                					@if (old('materiales.' . $loop->parent->index, optional($combinacion_capeart)->material_id) == $material->id) selected @endif
                				>{{ $material->descripcion }}</option>
                				@endforeach
                			</select>
                		</td>
						<td>
                			<select name="colores[]" class="form-control">
                				<option value="">-- Elija color --</option>
                				@foreach ($color as $capellada_color)
                					<option value="{{ $capellada_color->id }}"
                					@if (old('colores.' . $loop->parent->index, optional($combinacion_capeart)->color_id) == $capellada_color->id) selected @endif
                				>{{ $capellada_color->nombre }}</option>
                				@endforeach
                			</select>
						</td>
                		<td>
                			<input type="text" name="piezas[]" class="form-control"
                				value="{{ (old('piezas.' . $loop->index) ?? optional($combinacion_capeart)->piezas) ?? '' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo1[]" class="form-control"
                				value="{{ (old('consumo1.' . $loop->index) ?? optional($combinacion_capeart)->consumo1) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo2[]" class="form-control"
                				value="{{ (old('consumo2.' . $loop->index) ?? optional($combinacion_capeart)->consumo2) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo3[]" class="form-control"
                				value="{{ (old('consumo3.' . $loop->index) ?? optional($combinacion_capeart)->consumo3) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo4[]" class="form-control"
                				value="{{ (old('consumo4.' . $loop->index) ?? optional($combinacion_capeart)->consumo4) ?? '0' }}" />
                		</td>
						<td>
                			<select name="tipos[]" class="form-control">
                				<option value="">-- Elija tipo de material --</option>
                				@foreach ($tipos as $tipo)
                					<option value="{{ $tipo['id'] }}"
                					@if (old('tipos.' . $loop->parent->index, optional($combinacion_capeart)->tipo) == $tipo['id']) selected @endif
                				>{{ $tipo['nombre'] }}</option>
                				@endforeach
                			</select>
                		</td>
						<td>
						</td>
                	</tr>
           		@endforeach
				<tr id="capellada{{ count(old('capearts', $combinacion->capearts->count() ? $combinacion->capearts : [''])) }}"></tr>
			@else
            	<tr id="capellada0">
            		<td>
						<select name="materiales[]" class="form-control">
                			<option value="">-- Elija medida/talle --</option>
                			@foreach ($capelladas as $material)
                				<option value="{{ $material->id }}"> {{ $material->descripcion }}
                				</option>
                			@endforeach
                		</select>
               		</td>
					<td>
                		<select name="colores[]" class="form-control">
                			<option value="">-- Elija color --</option>
                			@foreach ($color as $capellada_color)
                				<option value="{{ $capellada_color->id }}" >{{ $capellada_color->nombre }}</option>
                			@endforeach
                		</select>
					</td>
                	<td>
                		<input type="text" name="piezas[]" class="form-control"
                			value="{{ old('piezas.1') ?? '' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo1[]" class="form-control"
                			value="{{ old('consumo1.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo2[]" class="form-control"
                			value="{{ old('consumo2.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo3[]" class="form-control"
                			value="{{ old('consumo3.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo4[]" class="form-control"
                			value="{{ old('consumo4.1') ?? '0' }}" />
                	</td>
					<td>
                		<select name="tipos[]" class="form-control">
                			<option value="">-- Elija tipo de material --</option>
                			@foreach ($tipos as $tipo)
                				<option value="{{ $tipo['id'] }}" >{{ $tipo['nombre'] }}</option>
                			@endforeach
                		</select>
                	</td>
					<td>
					</td>
           		</tr>
            	<tr id="capellada1"></tr>
			@endif
            </tbody>
            </table>
            <div class="row">
                <div class="col-md-12">
                    <button id="add_row" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
                    <button id='delete_row' class="pull-right btn btn-danger">- Borra rengl&oacute;n</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
    <table class="table" id="avios_table">
    	<thead>
    		<tr>
    			<th>Material Avio</th>
    			<th>Color</th>
    			<th>16/26</th>
    			<th>27/33</th>
    			<th>34/40</th>
    			<th>41/45</th>
    			<th>Tipo</th>
    		</tr>
    	</thead>
    	<tbody>
		 	@if (count($combinacion->avioarts) > 0)
				@foreach (old('avios', $combinacion->avioarts ? $combinacion->avioarts : ['']) as $combinacion_avioart)
               		<tr id="avio{{ $loop->index }}">
                		<td>
                			<select name="materiales_avios[]" class="form-control">
                				<option value="">-- Elija material --</option>
                				@foreach ($avios as $material)
                					<option value="{{ $material->id }}"
                					@if (old('materiales_avios.' . $loop->parent->index, optional($combinacion_avioart)->material_id) == $material->id) selected @endif
                				>{{ $material->descripcion }}</option>
                				@endforeach
                			</select>
                		</td>
						<td>
                			<select name="colores_avios[]" class="form-control">
                				<option value="">-- Elija color --</option>
                				@foreach ($color as $capellada_color)
                					<option value="{{ $capellada_color->id }}"
                					@if (old('colores_avios.' . $loop->parent->index, optional($combinacion_avioart)->color_id) == $capellada_color->id) selected @endif
                				>{{ $capellada_color->nombre }}</option>
                				@endforeach
                			</select>
						</td>
                		<td>
                			<input type="number" name="consumo1_avios[]" class="form-control"
                				value="{{ (old('consumo1_avios.' . $loop->index) ?? optional($combinacion_avioart)->consumo1) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo2_avios[]" class="form-control"
                				value="{{ (old('consumo2_avios.' . $loop->index) ?? optional($combinacion_avioart)->consumo2) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo3_avios[]" class="form-control"
                				value="{{ (old('consumo3_avios.' . $loop->index) ?? optional($combinacion_avioart)->consumo3) ?? '0' }}" />
                		</td>
                		<td>
                			<input type="number" name="consumo4_avios[]" class="form-control"
                				value="{{ (old('consumo4_avios.' . $loop->index) ?? optional($combinacion_avioart)->consumo4) ?? '0' }}" />
                		</td>
						<td>
                			<select name="tipos_avios[]" class="form-control">
                				<option value="">-- Elija tipo de avio --</option>
                				@foreach ($tipos_avios as $tipo)
                					<option value="{{ $tipo['id'] }}"
                					@if (old('tipos_avios.' . $loop->parent->index, optional($combinacion_avioart)->tipo) == $tipo['id']) selected @endif
                				>{{ $tipo['nombre'] }}</option>
                				@endforeach
                			</select>
                		</td>
						<td>
						</td>
                	</tr>
           		@endforeach
				<tr id="avio{{ count(old('avioarts', $combinacion->avioarts->count() ? $combinacion->avioarts : [''])) }}"></tr>
			@else
            	<tr id="avio0">
                	<td>
                		<select name="materiales_avios[]" class="form-control">
                			<option value="">-- Elija material --</option>
                			@foreach ($avios as $material)
                				<option value="{{ $material->id }}"
                			>{{ $material->descripcion }}</option>
                			@endforeach
                		</select>
                	</td>
					<td>
                		<select name="colores_avios[]" class="form-control">
                			<option value="">-- Elija color --</option>
                			@foreach ($color as $capellada_color)
                				<option value="{{ $capellada_color->id }}"
                			>{{ $capellada_color->nombre }}</option>
                			@endforeach
                		</select>
					</td>
                	<td>
                		<input type="number" name="consumo1_avios[]" class="form-control"
                			value="{{ old('consumo1_avios.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo2_avios[]" class="form-control"
                			value="{{ old('consumo2_avios.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo3_avios[]" class="form-control"
                			value="{{ old('consumo3_avios.1') ?? '0' }}" />
                	</td>
                	<td>
                		<input type="number" name="consumo4_avios[]" class="form-control"
                			value="{{ old('consumo4_avios.1') ?? '0' }}" />
                	</td>
					<td>
                		<select name="tipos_avios[]" class="form-control">
                			<option value="">-- Elija tipo de avio --</option>
                			@foreach ($tipos_avios as $tipo)
                				<option value="{{ $tipo['id'] }}"
                			>{{ $tipo['nombre'] }}</option>
                			@endforeach
                		</select>
                	</td>
					<td> </td>
                </tr>
            	<tr id="avio1"></tr>
			@endif
            </tbody>
            </table>
            <div class="row">
                <div class="col-md-12">
                    <button id="add_row_avio" class="pull-right btn btn-danger">+ Agrega rengl&oacute;n</button>
                    <button id='delete_row_avio' class="pull-right btn btn-danger">- Borra rengl&oacute;n</button>
                </div>
            </div>
        </div>
    </div>
	<div class="card-footer">
		<div class="row">
		@isset($edit)
			@include('includes.boton-form-editar')
		@else
			@include('includes.boton-form-crear')
		@endisset
		</div>
	</div>
</div>
</div>
