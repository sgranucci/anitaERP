@extends("theme.$theme.layout")
@section("titulo")
Cuentas contables
@endsection

@section("styles")
<link href="{{asset("assets/js/jquery-nestable/jquery.nestable.css")}}" rel="stylesheet" type="text/css" />
@endsection

@section("scriptsPlugins")
<script src="{{asset("assets/js/jquery-nestable/jquery.nestable.js")}}" type="text/javascript"></script>
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/contable/cuentacontable/index.js")}}" type="text/javascript"></script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.mensaje')
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Cuentas contables</h3>
                <div class="card-tools">
                    <a href="{{route('crear_cuentacontable')}}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-fw fa-plus-circle"></i> Crear cuenta
                    </a>
                </div>
            </div>
            <div class="card-body">
                @csrf
                <div class="dd" id="nestable">
                    <ol class="dd-list">
                        @foreach ($cuentacontables as $key => $item)
                            @if ($item["cuentacontable_id"] != 0)
                                @break
                            @endif
                            @include("contable.cuentacontable.cuentacontable-item",["item" => $item])
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection