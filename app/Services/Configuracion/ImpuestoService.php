<?php
namespace App\Services\Configuracion;

use App\Models\Stock\Articulo;
use App\Models\Configuracion\Impuesto;
use App\Services\Configuracion\IIBBService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App;
use Auth;

class ImpuestoService
{
	protected $IIBBService;

    public function __construct(
								IIBBService $IIBBservice
								)
    {
        $this->IIBBService = $IIBBservice;
    }

	public function calculaImpuestoVenta($dataItem, $dataCliente)
	{
		// Inicializa variables
		$totalFinal = 0.;
		$descuentoItem = 0.;
		$descuentoFinal = 0.;
		$totalBruto = 0.;
		$nroinscripcion = "";
		$condicionIIBB = "";
		$provincia = 0;
		$totalNeto = 0.;

		// Asigna datos cliente
		$nroInscripcion = $dataCliente['nroinscripcion'];
		$retieneIva = $dataCliente['retieneiva'];
		$condicionIIBB = $dataCliente['condicioniibb'];
		$provincia = $dataCliente['provincia'];
		$porcDescuento = 0;

		// Calcula netos por tasa
		$netos = [];
		$subtotales = [];
		foreach($dataItem as $item)
		{
			// Lee tasa impuesto del item
        	$impuesto = Impuesto::findOrFail($item['impuesto_id']);

			if ($impuesto)
			{
				// Calcula importe del item
				$importesindto = $item['cantidad'] * 
							($item['incluyeimpuesto'] == 'N' || $item['incluyeimpuesto'] == '2' ? 
							$item['precio'] : ($item['precio'] / (1.+($impuesto->valor/100))));

				$totalItem = $importesindto * (1. - ($item['descuento'] / 100.));

				$totalBruto += $totalItem;
				$descuentoItem += ($importesindto * $item['descuento'] / 100.);

				if ($impuesto->valor > 0)
					$totalNeto += $totalItem;

				// Acumula subtotales
				self::agregaItemTotales("Subtotal", 0, $totalItem, 0, 0, $subtotales);

				// Agrega descuento final
				if ($item['descuentofinal'] != 0.)
				{
					$descuentoFinal += ($totalItem * $item['descuentofinal'] / 100.);

					$totalItem *= (1. - ($item['descuentofinal'] / 100.));
					$porcDescuento = $item['descuentofinal'];
				}

				// Acumula netos por tasa de impuesto
				self::agregaItemTotales(($impuesto->valor == 0. ? "Exento" : "Gravado al ".$impuesto->valor."%"), $impuesto->valor, 
					$totalItem, $impuesto->id, $impuesto->codigo, $netos);
			}
		}

		if ($descuentoFinal != 0.)
		{
			$detalle = "Descuento ".$porcDescuento.'%';
			$totalNeto -= $descuentoFinal;

			self::agregaItemTotales($detalle, $porcDescuento, -$descuentoFinal, 0, 0, $subtotales);
		}

		// Agrega impuestos nacionales
		$impuestos = [];
		for ($i = 0; $i < count($netos); $i++)
		{
			if($netos[$i]['tasa'] != 0.)
			{
				$detalle = "Iva ".$netos[$i]['tasa']."%";
				$importe = $netos[$i]['importe'] * $netos[$i]['tasa'] / 100.;

				$impuestos[] = ["concepto"=>$detalle,
								"baseimponible" => $netos[$i]['importe'],
								"tasa"=>$netos[$i]['tasa'],
								"importe"=>$importe,
								"impuesto_id"=>$netos[$i]['impuesto_id'],
								"codigo"=>$netos[$i]['codigo']
							];
			}
		}

		// Agrega percepcion de iva si es agente de percepcion y el cliente no lo es
        if (env('ANITA_AGENTE_PERCEPCION_IVA') == 'si' && $retieneIva != 'S')
		{
			$percepcionIva = $totalNeto * env('ANITA_TASA_PERCEPCION_IVA') / 100.;

			$impuestos[] = ["concepto"=>"Percepcion IVA",
							"baseimponible" => $totalNeto,
							"tasa"=>env('ANITA_TASA_PERCEPCION_IVA'),
							"importe"=>$percepcionIva,
							];
		}

		// Agrega impuestos provinciales
		$percepcionesIIBB = $this->IIBBService->calculaPercepcionIIBB($totalNeto, $nroInscripcion, 
																		$condicionIIBB, $provincia);

		$conceptosTotales = array_merge($subtotales, $netos, $impuestos, $percepcionesIIBB);

		// Agrega total final
		for ($i = 0, $totalFinal = 0; $i < count($conceptosTotales); $i++)
		{
			if ($conceptosTotales[$i]['concepto'] != "Subtotal" &&
				substr($conceptosTotales[$i]['concepto'], 0, 9) != "Descuento")
				$totalFinal += $conceptosTotales[$i]['importe'];
		}

		$conceptosTotales[] = ["concepto"=>"Total",
								"tasa"=>0,
								"importe"=>$totalFinal,
							];

		return $conceptosTotales;
	}

	// Busca un valor en array

	public function buscaValor($arrayconcepto, $concepto, $key, $valor)
	{
		$valorRetorno = 0;
		
		foreach($arrayconcepto as $item)
		{
			$pos = strpos($item[$concepto], $key);

			if ($pos >= 0 && $pos !== false)
				$valorRetorno += $item[$valor];
		}
		return $valorRetorno;
	}

	// Agrega item 
	private function agregaItemTotales($concepto, $tasa, $totalItem, $impuesto_id, $codigo, &$tabla)
	{
		// Acumula subtotales
		$fl_encontro = false;
		for ($i = 0; $i < count($tabla); $i++)
		{
			if ($tabla[$i]['concepto'] == $concepto)
			{
				$fl_encontro = true;
				$tabla[$i]['importe'] += $totalItem;
				break;
			}
		}
		if (!$fl_encontro)
		{
			$tabla[] = ["concepto"=>$concepto,
						"tasa"=>$tasa,
						"importe"=>$totalItem,
						"impuesto_id"=>$impuesto_id,
						"codigo"=>$codigo
						];
		}
	}
}
