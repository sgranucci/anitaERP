@extends("theme.$theme.layout")
@section('titulo')
    Clientes
@endsection

@section("scripts")
<script src="{{asset("assets/pages/scripts/admin/crear.js")}}" type="text/javascript"></script>
<script src="{{asset("assets/pages/scripts/admin/domicilio.js")}}" type="text/javascript"></script>
<script>
    function completarLetra(condicioniva_id){
		var condiva = "{{ $condicioniva_query }}";
		const replace = '"';
		var data = condiva.replace(/&quot;/g, replace);
		var dataP = JSON.parse(data);

		$.each(dataP, (index, value) => {
			if (value['id'] == condicioniva_id)
				$("#letra").val(value['letra']);
  		});
	}

    $(function () {
        $("#condicioniva_id").change(function(){
            var  condicioniva_id = $(this).val();
            completarLetra(condicioniva_id);
        });

        $("#botonestado").click(function(){

            var estado = $("#estado").val();
			var descripcion = $("#botonestado").text();

			if (estado == '0')
			{
				estado = '1';
				descripcion = 'Suspendido';
			}
			else
			{
				estado = '0';
				descripcion = 'Activo';
			}

            $("#estado").val(estado);
            $("#botonestado").html("<i class='fa fa-bell'></i>&nbsp;Estado "+descripcion);
        });

        $("#botonform1").click(function(){
            $(".form1").show();
            $(".form2").hide();
            $(".form3").hide();
            $(".form4").hide();
            $(".form5").hide();
        });

        $("#botonform2").click(function(){
            $(".form1").hide();
            $(".form2").show();
            $(".form3").hide();
            $(".form4").hide();
            $(".form5").hide();
        });

        $("#botonform4").click(function(){
            $(".form1").hide();
            $(".form2").hide();
            $(".form3").hide();
            $(".form4").show();
            $(".form5").hide();
        });

        completarLetra({{$data->condicioniva_id}});
    });
</script>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-12">
        @include('includes.form-error')
        @include('includes.mensaje')
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">Editar Cliente </h3>&nbsp;
				<i class="fa fa-user"></i>Datos principales	
                <div class="card-tools">
					<button type="button" id="botonestado" class="btn btn-info btn-sm">
                        <i class="fa fa-bell"></i> Estado {{ $data->descripcionestado }}
                    </button>
                    <a href="{{route('cliente')}}" class="btn btn-outline-info btn-sm">
                        <i class="fa fa-fw fa-reply-all"></i> Volver al listado
                    </a>
                </div>
            </div>
            <form action="{{route('actualizar_cliente', ['id' => $data->id])}}" id="form-general" class="form-horizontal form--label-right" method="POST" autocomplete="off">
                @csrf @method("put")
                <div class="card-body" style="padding-bottom: 0; padding-top: 5px;">
                    @include('ventas.cliente.form1')
                    @include('ventas.cliente.form2')
                    @include('ventas.cliente.form4')
                </div>
                <div class="card-footer" style="padding-top: 0">
                	<div class="row">
                   		<div class="col-lg-4">
                        	@include('includes.boton-form-editar')
                    	</div>
            			<div class="col-lg-8" align="right">
							<button type="button" id="botonform1" class="btn btn-primary btn-sm">
						   	<i class="fa fa-user"></i> Datos principales
							</button>
							<button type="button" id="botonform2" class="btn btn-info btn-sm">
         						<span class="fa fa-cash-register"></span> Datos facturac&oacute;n
      						</button>
							<button type="button" id="botonform3" class="btn btn-info btn-sm">
         						<span class="fa fa-truck"></span> Lugares de entrega
      						</button>
							<button type="button" id="botonform4" class="btn btn-info btn-sm">
         						<span class="fa fa-comment"></span> Leyendas
      						</button>
							<button type="button" id="botonform5" class="btn btn-info btn-sm">
         						<span class="fa fa-copy"></span> Archivos asociados
      						</button>
            			</div>
            		</div>
            	</div>
            </form>
        </div>
    </div>
</div>
@endsection