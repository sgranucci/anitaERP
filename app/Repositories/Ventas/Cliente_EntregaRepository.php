<?php

namespace App\Repositories\Ventas;

use App\Models\Ventas\Cliente;
use App\Models\Ventas\Cliente_Entrega;
use App\Models\Configuracion\Localidad;
use App\Models\Configuracion\Provincia;
use App\Models\Ventas\Zonavta;
use App\Models\Ventas\Subzonavta;
use App\Models\Ventas\Vendedor;
use App\Models\Ventas\Transporte;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\ApiAnita;
use Carbon\Carbon;
use Auth;

class Cliente_EntregaRepository implements Cliente_EntregaRepositoryInterface
{
    protected $model, $modelCliente;
    protected $tableAnita = 'entrcli';
    protected $keyField = 'codigo';
    protected $keyFieldAnita = 'entc_cliente';

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Cliente_Entrega $cliente_entrega, Cliente $cliente)
    {
        $this->model = $cliente_entrega;
        $this->modelCliente = $cliente;
    }

    public function create(array $data)
    {
		return self::guardarCliente_Entrega($data, 'create');
    }

    public function update(array $data, $id)
    {
		return self::guardarCliente_Entrega($data, 'update', $id);
    }

    public function delete($cliente_id, $codigo)
    {
		// Elimina anita
		self::eliminarAnita($codigo);

        $cliente = $this->model->where('cliente_id', $cliente_id)->delete();

		return $cliente;
    }

    public function find($id)
    {
        if (null == $cliente = $this->model->find($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cliente;
    }

    public function findOrFail($id)
    {
        if (null == $cliente = $this->model->findOrFail($id)) {
            throw new ModelNotFoundException("Registro no encontrado");
        }

        return $cliente;
    }

	private function guardarCliente_Entrega($data, $funcion, $id = null)
	{
		if ($funcion == 'update')
		{
			// Borra los registros antes de grabar nuevamente
       		$this->delete($id, $data['codigo']);
		}

		$nombres = $data['nombres'];
		$domicilios = $data['domicilios'];

		if ($data['localidades_id'] ?? '')
			$localidades_id = $data['localidades_id'];
		else
			$localidades_id = $data['localidad_id_previas'];

		$provincias_id = $data['provincias_id'];
		$codigospostales = $data['codigospostales'];
		$transportes_id = $data['transportes_id'];

		// Borra de anita
		self::eliminarAnita($data['codigo']);

		for ($i_entrega = 0; $i_entrega < count($nombres); $i_entrega++)
		{
		  	//* Valida si se cargo el lugar de entrega
		  	if ($nombres[$i_entrega] != '' && $domicilios[$i_entrega] != '')
			{
				$provincia = Provincia::find($provincias_id[$i_entrega])->first();
				if ($provincia)
			  		$pais_id = $provincia->pais_id;
				else
			  		$pais_id = 1;
	
       			$cliente = $this->modelCliente->find($id);
				if ($cliente)
				{
			  		$zonavta_id = $cliente->zonavta_id;
			  		$subzonavta_id = $cliente->subzonavta_id;
			  		$vendedor_id = $cliente->vendedor_id;
				}
	
				$cliente_entrega = $this->model->create([
								'cliente_id' => $id,
								'nombre' => $nombres[$i_entrega],
								'codigo' => $i_entrega,
								'domicilio' => $domicilios[$i_entrega],
								'localidad_id' => $localidades_id[$i_entrega],
								'provincia_id' => $provincias_id[$i_entrega],
								'pais_id' => $pais_id,
								'codigopostal' => $codigospostales[$i_entrega],
								'zonavta_id' => $zonavta_id,
								'subzonavta_id' => $subzonavta_id,
								'vendedor_id' => $vendedor_id,
								'transporte_id' => $transportes_id[$i_entrega],
								]);

				// Guarda en anita
				self::guardarAnita($data, $i_entrega);
			}
		}
	}

    public function sincronizarConAnita(){
		ini_set('max_execution_time', '300');
	  	ini_set('memory_limit', '512M');

        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'list', 
						'campos' => "
							entc_cliente, 
							entc_linea, 
							entc_lugar, 
							entc_direccion, 
							entc_localidad, 
							entc_provincia, 
							entc_cod_postal, 
							entc_expreso
								", 
						'tabla' => $this->tableAnita );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        foreach ($dataAnita as $value) {
            $this->traerRegistroDeAnita($value->entc_cliente, $value->entc_linea, true);
        }
    }

    private function traerRegistroDeAnita($cliente, $linea, $fl_crea_registro){
        $apiAnita = new ApiAnita();
        $data = array( 
            'acc' => 'list', 'tabla' => $this->tableAnita, 
            'campos' => '
							entc_cliente, 
							entc_linea, 
							entc_lugar, 
							entc_direccion, 
							entc_localidad, 
							entc_provincia, 
							entc_cod_postal,
							entc_expreso
						',
            'whereArmado' => " WHERE entc_cliente = '".$cliente."' and entc_linea = '".$linea."' "
        );
        $dataAnita = json_decode($apiAnita->apiCall($data));

        if ($dataAnita) {
            $data = $dataAnita[0];

			$provincia_id = $data->entc_provincia;
			$localidad_id = $data->entc_localidad;
			if ($provincia_id == 0)
			  	$provincia_id = 1;
			if ($localidad_id == 0)
			  	$localidad_id = 1;
	
       		$transporte = Transporte::select('id', 'codigo')->where('codigo' , $data->entc_expreso)->first();
			if ($transporte)
				$transporte_id = $transporte->id;
			else
				$transporte_id = 1;

       		$Cliente = Cliente::where('codigo' , ltrim($cliente, '0'))->first();

			$pais_id = 1;

			if ($Cliente)
			{
				$arr_campos = [
					"cliente_id" => $Cliente->id,
					"nombre" => $data->entc_lugar,
					"codigo" => $data->entc_linea,
					"domicilio" => $data->entc_direccion,
					"localidad_id" => $localidad_id,
					"provincia_id" => $provincia_id,
					"pais_id" => $pais_id,
					"codigopostal" => $data->entc_cod_postal,
					"zonavta_id" => $Cliente->zonavta_id,
					"subzonavta_id" => $Cliente->subzonavta_id,
					"vendedor_id" => $Cliente->vendedor_id,
					"transporte_id" => $Cliente->transporte_id,
            		];
		
				if ($fl_crea_registro)
            		$this->model->create($arr_campos);
				else
            		$this->model->where('cliente_id', $cliente->id)->where('codigo', $data->entc_lugar)->update($arr_campos);
			}
        }
    }

	private function guardarAnita($data, $linea) {
        $apiAnita = new ApiAnita();

		$nombres = $data['nombres'];
		$domicilios = $data['domicilios'];

		if ($data['localidades_id'] ?? '')
			$localidades_id = $data['localidades_id'];
		else
			$localidades_id = $data['localidad_id_previas'];

		$provincias_id = $data['provincias_id'];
		$codigospostales = $data['codigospostales'];
		$transportes_id = $data['transportes_id'];

		$this->setCamposAnita($transportes_id[$linea], $codigotransporte);

        $data = array( 'tabla' => $this->tableAnita, 'acc' => 'insert',
            'campos' => ' 
							entc_cliente, 
							entc_linea, 
							entc_lugar, 
							entc_direccion, 
							entc_localidad, 
							entc_provincia, 
							entc_cod_postal, 
							entc_expreso
				',
            'valores' => " 
				'".str_pad($data['codigo'], 6, "0", STR_PAD_LEFT)."', 
				'".$linea."',
				'".$nombres[$linea]."',
				'".$domicilios[$linea]."',
				'".$localidades_id[$linea]."',
				'".$provincias_id[$linea]."',
				'".$codigospostales[$linea]."',
				'".$codigotransporte."' "
        );
        $apiAnita->apiCall($data);
	}

	private function eliminarAnita($cliente) {
        $apiAnita = new ApiAnita();
        $data = array( 'acc' => 'delete', 'tabla' => $this->tableAnita, 
				'whereArmado' => " WHERE entc_cliente = '".str_pad($cliente, 6, "0", STR_PAD_LEFT)."' ");
        $apiAnita->apiCall($data);
	}

	private function setCamposAnita($transporte_id, &$codigotransporte)
	{
       	$transporte = Transporte::select('id', 'codigo')->where('id' , $transporte_id)->first();
		if ($transporte)
			$codigotransporte = $transporte->codigo;
		else
			$codigotransporte = 0;
	}
}
