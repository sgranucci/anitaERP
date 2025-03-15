<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Ventas\FacturacionService;
use App\Services\Ventas\FacturanteService;
use Carbon\Carbon;

class FacturanteController extends Controller
{
	private $facturacionService;
    private $facturanteService;

    public function __construct(FacturacionService $facturacionservice,
                                FacturanteService $facturanteservice)
    {
        $this->facturacionService = $facturacionservice;
        $this->facturanteService = $facturanteservice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crearImportacion()
    {
        return view('ventas.facturante.crear');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listarComprobanteFull(Request $request)
    {
        $response = array();
        $parameters = $request->all();

        $rules =  array(
              'desdefecha'    => 'required',
              'hastafecha'    => 'required'
          );
          
        $messages = array(
              'desdefecha.required' => 'Fecha desde es requerida.',
              'hastafecha.required' => 'Fecha hasta es requerida.',
          );

        $medioPago_enum = [
            '1' => 'Mercado pago',
			      '2' => 'Tienda nube',
            '3' => 'Go',
            '4' => 'Transferencia',
            '5' => 'No transfiere'
		    ];          
        
        $desdefecha = $parameters['desdefecha'];
        $hastafecha = $parameters['hastafecha'];
        
        $validator = \Validator::make(array('desdefecha' => $desdefecha,
                                            'hastafecha' => $hastafecha), $rules, $messages);
        if(!$validator->fails()) {
              $retorno = $this->facturanteService->listadoComprobanteFull($parameters);
//dd($retorno);
              if (is_array($retorno))
                $datas = $retorno;
              else
              {
                // Convierte en coleccion al ser 1 solo item
                $datas = json_encode($retorno);

                $d = json_decode(stripslashes($datas));

                $data = collect([$d]);
                $datas = $data;
              }

              $arraySalida = [];
              for ($i = 0; $i < count($datas); $i++)
              {
                if (!isset($datas[$i]->Prefijo))
                  continue;
                
                if ($datas[$i]->Prefijo == 21)
                  $datas[$i]->mediopago = '1';
                elseif ($datas[$i]->Prefijo == 23)
                  $datas[$i]->mediopago = '1';
                else
                  $datas[$i]->mediopago = '5';

                $letra = substr($datas[$i]->TipoComprobante, -1);
                switch($datas[$i]->TipoComprobante)
                {
                  case 'FA':
                  case 'FB':
                  case 'FC':
                    $tipoComprobante = 'FAC';
                    $signo = 1.;
                    break;
                  case 'NCA':
                  case 'NCB':
                  case 'NCC':
                    $tipoComprobante = 'NCD';
                    $signo = -1.;
                    break;
                  case 'NDA':
                  case 'NDB':
                  case 'NDC':
                    $tipoComprobante = 'NDB';
                    $signo = 1.;
                    break;
                }
                $venta = $this->facturanteService->leeComprobante($tipoComprobante, $letra, 
                  $datas[$i]->Prefijo, $datas[$i]->Numero);
                if (isset($venta[0]->ven_nro) ? $venta[0]->ven_nro != $datas[$i]->Numero : true)
                //  if ($datas[$i]->Numero > 12307 && $datas[$i]->Numero < 12337)
                    $arraySalida[] = $datas[$i];
              }

              $datas = $arraySalida;
//dd($datas);
              if (isset($datas[0]->TipoComprobante))
                return view('ventas.facturante.index', compact('datas', 'desdefecha', 'hastafecha', 'medioPago_enum'));
              else
              {
                    return redirect('ventas/crearimportacionfacturastiendanube')->with('mensaje', 'Error de lectura');
              }
           } else {
              $errors = $validator->errors();
              return response()->json($errors->all());
           }
        return view('ventas/crearimportacionfacturastiendanube')->with('mensaje', 'Comprobantes leidos con éxito');
    }

    public function generarFacturasTiendaNube(Request $request)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '2400');

        $datos = json_decode($request->datos,true);

        $qFacturas = count($datos);
        //$qFacturas = 1;
        for ($ii = 0; $ii < $qFacturas; $ii++)
        {
            if ($datos[$ii]['mediopago'] != '5') 
            {
              // CAE y CLIENTES VIENEN de a dos registro en array
              $ret = $this->facturanteService->generaFactura($datos[$ii]['tipocomprobante'], $datos[$ii]['prefijo'], 
                                          $datos[$ii]['numero'],
                                          $datos[$ii]['condicionventa'], $datos[$ii]['fechahora'], 
                                          $datos[$ii]['total'], $datos[$ii]['totalneto'], 
                                          $datos[$ii]['iva1'], 
                                          $datos[$ii]['iva2'], $datos[$ii]['subtotalnoalcanzado'], 
                                          $datos[$ii]['subtotalexcento'], 
                                          $datos[$ii]['totalpercepcioniibb'], $datos[$ii]['item'], 
                                          $datos[$ii]['cae'], 
                                          $datos[$ii]['fechavencimientocae'], $datos[$ii]['cliente'],
                                          $datos[$ii]['mediopago']);
                                    
              if ($ret['error'] != 'Success')
                return($ret);
                            
              $signo = 1;
              if (substr($datos[$ii]['tipocomprobante'], 0, 2) == "NC")
                $signo = -1;
              $total = $datos[$ii]['total']*$signo;
              $this->facturanteService->generaPre($total, $datos[$ii]['mediopago']);
            }
        }

        // Graba PRE con asiento contable
        $this->facturanteService->grabaPre(Carbon::now());
        return ($ret);
    }

}
