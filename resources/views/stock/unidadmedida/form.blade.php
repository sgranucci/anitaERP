<div class="form-group row">
    <label for="nombre" class="col-lg-3 col-form-label requerido">Nombre</label>
    <div class="col-lg-8">
    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre', $data->nombre ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="abrev" class="col-lg-3 col-form-label requerido">Abreviatura</label>
    <div class="col-lg-2">
    <input type="text" name="abreviatura" id="abreviatura" class="form-control" value="{{old('abreviatura', $data->abreviatura ?? '')}}" required/>
    </div>
</div>
<div class="form-group row">
    <label for="codigoexterno" class="col-lg-3 col-form-label requerido">C&oacute;digo Externo</label>
    <div class="col-lg-2">
    <input type="text" name="codigo" id="codigo" class="form-control" value="{{old('codigo', $data->codigo ?? '')}}"/>
    </div>
</div>