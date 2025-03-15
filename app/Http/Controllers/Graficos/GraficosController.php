<?php

namespace App\Http\Controllers\Graficos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Exports\Graficos\LecturasExport;
use App\Exports\Graficos\IndicadoresExport;
use App\Exports\Graficos\OperacionesExport;
use App\Exports\Graficos\ReporteIndicadoresExport;
use App\Services\Graficos\IndicadoresService;
use App\Jobs\GeneraOrdenes;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Throwable;
use PDF;
use DB;

class GraficosController extends Controller
{
	private $indicadoresService;

	public function __construct(IndicadoresService $indicadoresservice)
	{
		$this->middleware('auth');

		$this->indicadoresService = $indicadoresservice;
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */    
	private $calculoBase_enum = [
		'1' => 'HL/2',
		'2' => 'HLC/3',
		'3' => 'OHLC/4',
	];

	public function index()
    {
		$lecturas = '';
        return view('graficos.velas', compact('lecturas'));
    }

	public function leerDatosLecturas($fecha, $dias)
    {
		$dfecha = strptime($fecha, '%d-%m-%Y');
		$timestamp = mktime(5, 0, 0, $dfecha['tm_mon']+1, $dfecha['tm_mday'], $dfecha['tm_year']+1900)*1000;
			
		$data = DB::connection('trade')->table('trade.lecturas')
				->select('fechaChar as fechastr',
						 'openPrice as open',
						 'highPrice as high',
						 'lowPrice as low',
						 'closePrice as close',
						 'volume')
				->where('chartTime', '>=', $timestamp)
				->get();
								
		foreach($data as $key => $value)
		{
			if (substr($value->fechastr, -8) < "23:59:00")
				$array[] = ['hora'=>substr($value->fechastr, -8), 'low'=>$value->low, 'open'=>$value->open, 'close'=>$value->close, 'high'=>$value->high];
		}
		$lecturas = json_encode($array, JSON_NUMERIC_CHECK);
		
		return $lecturas;
    }

	public function indexReporteLecturas()
	{
		$compresion_enum = [
			'1' => '1 minuto',
			'2' => '5 minutos',
			'3' => '15 minutos',
			'4' => '1 hora',
			'5' => '1 día'
			];

		return view('graficos.reporte.create', compact('compresion_enum'));
	}

	public function crearReporteLecturas(Request $request)
    {
		switch($request->extension)
		{
		case "Genera Reporte en Excel":
			$extension = "xlsx";
			break;
		case "Genera Reporte en PDF":
			$extension = "pdf";
			break;
		case "Genera Reporte en CSV":
			$extension = "csv";
			break;
		}
		return (new LecturasExport)
				->parametros($request->desdefecha, $request->hastafecha, $request->desdehora, $request->hastahora, $request->compresion)
				->download('reportelecturas.'.$extension);
    }

	public function indexReporteIndicadores()
	{
		$compresion_enum = [
			'1' => '1 minuto',
			'2' => '5 minutos',
			'3' => '15 minutos',
			'4' => '1 hora',
			'5' => '1 día'
			];

		$filtroSetup_enum = [
			'A' => 'Solo alcistas',
			'B' => 'Solo bajistas',
			'T' => 'Alcistas y Bajistas',
			];

		$gatillo_enum = [
				'A' => 'RRR >= 1.5',
				'B' => 'RRR >= 1.5 y SL < 500',
				];
	
		$administracionPosicion_enum = [
			'A' => 'Administración sin filtro de tiempo',
			'B' => 'Administración filtrando por tiempo',
			];

		$filtrosMatematicos_enum = [
				'S' => 'Con filtros matematicos',
				'B' => 'Sin filtros matematicos',
				];
	
		$calculoBase_enum = $this->calculoBase_enum;
		return view('graficos.reporteindicadores.create', compact('calculoBase_enum', 'compresion_enum',
																'filtroSetup_enum', 'administracionPosicion_enum',
																'filtrosMatematicos_enum',
																'gatillo_enum'));
	}

	public function crearReporteIndicadores(Request $request)
    {
		switch($request->extension)
		{
		case "Genera Reporte en Excel":
			$extension = "xlsx";
			break;
		case "Genera Reporte en PDF":
			$extension = "pdf";
			break;
		case "Genera Reporte en CSV":
			$extension = "csv";
			break;
		}
		$calculoBase_enum = $this->calculoBase_enum;

        $indicadores = $this->indicadoresService->calculaIndicadores($request->desdefecha, 
                        $request->hastafecha, 
                        $request->desdeHhra, 
                        $request->hastahora, 
                        $request->especie,
                        $request->calculobase,
                        $request->mmcorta,
                        $request->mmlarga,
                        $request->compresion,
                        $request->largovma,
                        $request->largocci,
                        $request->largoxtl,
                        $request->umbralxtl,
                        $calculoBase_enum,
                        $request->swingsize,
						$request->filtroSetup,
						$request->cantidadcontratos,
						$request->administracionposicion,
						$request->tiempo,
						$request->filtrosmatematicos,
						$request->gatillo);

		return (new ReporteIndicadoresExport)
				->parametros($request->desdefecha, 
							$request->hastafecha, 
							$request->desdehora, 
							$request->hastahora, 
							$request->especie,
							$request->calculobase,
							$request->mmcorta,
							$request->mmlarga,
							$request->compresion,
							$request->largovma,
							$request->largocci,
							$request->largoxtl,
							$request->umbralxtl,
							$calculoBase_enum,
							$request->swingsize,
							$request->filtroSetup,
							$request->cantidadcontratos,
							$indicadores['indicadores'],
							$indicadores['operaciones'],
							$request->administracionposicion,
							$request->tiempo)
				->download('reporteIndicadores.'.$extension);
    }
	
	public function indexGeneraOrdenes()
	{
		$compresion_enum = [
			'1' => '1 minuto',
			'2' => '5 minutos',
			'3' => '15 minutos',
			'4' => '1 hora',
			'5' => '1 día'
			];

		$filtroSetup_enum = [
			'A' => 'Solo alcistas',
			'B' => 'Solo bajistas',
			'T' => 'Alcistas y Bajistas',
			];
		$calculoBase_enum = $this->calculoBase_enum;

		return view('graficos.generaordenes.create', compact('calculoBase_enum', 'compresion_enum',
																'filtroSetup_enum'));
	}

	// Genera ordenes
	public function _generaOrdenes()
	{
		GeneraOrdenes::dispatchNow
		(request()->especie, request()->calculobase, 
								request()->mmcorta, request()->mmlarga, request()->compresion, 
								request()->largovma, request()->largocci, request()->largoxtl,
								request()->umbralxtl, request()->swingsize, request()->filtroSetup);

		return redirect('graficos/ordenes')->with('mensaje', 'Proceso iniciado con éxito');
	}

	public function __generaOrdenes()
	{
		$batch = Bus::batch([
			new GeneraOrdenes(request()->especie, request()->calculobase, 
			request()->mmcorta, request()->mmlarga, request()->compresion, 
			request()->largovma, request()->largocci, request()->largoxtl,
			request()->umbralxtl, request()->swingsize, request()->filtroSetup),
		])->then(function (Batch $batch) {
			// All jobs completed successfully...
		})->catch(function (Batch $batch, Throwable $e) {
			// First batch job failure detected...
			Log::info($e->getMessage());
		})->finally(function (Batch $batch) {
			// The batch has finished executing...
			return redirect('graficos/ordenes')->with('mensaje', 'Proceso finalizado con éxito');
		})->dispatch();

		return redirect('graficos/ordenes')->with('batchId', $batch->id);
		//return $batch->id;
	}

	public function generaOrdenes()
	{
		// Inicializa variables
		$indicadoresService = new IndicadoresService;

		$indicadoresService->datas = [];

		$indicadoresService->acumOpen = 0;
		$indicadoresService->acumClose = $indicadoresService->acumLow = $indicadoresService->acumHigh = 0;
		$indicadoresService->acumVolume = 0;
		$indicadoresService->acumCantLectura = 0;
		$indicadoresService->acumFlEmpezoRango = false;
		$indicadoresService->acumItem = 0;
		$indicadoresService->acumFecha = "01-01-2001";
		$indicadoresService->acumFechaInicioRango = "01-01-2001";
		$indicadoresService->acumFechaStr = "01-01-2001";
		$indicadoresService->acumFechaLectura = "01-01-2001";
		$indicadoresService->cantLectura = 0;
		$indicadoresService->comision = 2.25;

		$indicadoresService->acumFlBuscaEntrada = $indicadoresService->acumFlAbrePosicion = false;
		$indicadoresService->acumOff0 = $indicadoresService->acumOff1oA = -1;
		$indicadoresService->acumFlAcista = false;
		$indicadoresService->acumFlBajista = false;
		$indicadoresService->flAbc = false;
		$indicadoresService->flAbCd = false;
		$indicadoresService->fl3Drives = false;
		$indicadoresService->flShark = false;
		$indicadoresService->flW4 = false;
		$indicadoresService->flSp = false;
		$indicadoresService->flInertia = false;
		$indicadoresService->flVolatilidad = false;

		$indicadoresService->acumFlAnulacionAbcAlcistaActiva = false;
		$indicadoresService->acumFlAnulacionAbcBajistaActiva = false;
		$indicadoresService->acumFlAnulacionAbCdAlcistaActiva = false;
		$indicadoresService->acumFlAnulacionAbCdBajistaActiva = false;

		$indicadoresService->acumIdSenial = $indicadoresService->acumIdTrade = 0;
		$indicadoresService->cantidadActivaContratos = $indicadoresService->totalContratos;
		$indicadoresService->acumFlCerroPorTiempoAlcista = false;
		$indicadoresService->acumFlCerroPorTiempoBajista = false;
		$indicadoresService->acumFlCierraPorTiempo = false;
		$indicadoresService->acumProfitAndLoss = 0;
		$indicadoresService->pivotes = [];
		$indicadoresService->flSpAlcista = false;
		$indicadoresService->tgtSpAlcista1 = 0;
		$indicadoresService->ventanaSpAlcista = 0;

		$indicadoresService->flSpBajista = false;
		$indicadoresService->tgtSpBajista1 = 0;
		$indicadoresService->ventanaSpBajista = 0;
		$indicadoresService->flEmpiezaOperacion = false;
		
		// Variables de calculo de swing
		$indicadoresService->acumTendencia = 'Indefinida';
		$indicadoresService->acumBnMinActual = $indicadoresService->acumBnMaxActual = $indicadoresService->acumMaximoActual = 0;
		$indicadoresService->acumMinimoActual = 0;
		$indicadoresService->acumBnMaximo = $indicadoresService->acumBnMinimo = $indicadoresService->acumBnMaximoAnterior = 0;
		$indicadoresService->acumBnMinimoAnterior = 0;

		$indicadoresService->ultimoMaximoBajista = $indicadoresService->ultimoMinimoBajista = 0;
		$indicadoresService->ultimoMaximoAlcista = $indicadoresService->ultimoMinimoAlcista = 0;
		$indicadoresService->offsetD = $indicadoresService->offsetC = 0;
		$indicadoresService->offsetB = $indicadoresService->offsetA = 0;
		$indicadoresService->offsetU = $indicadoresService->offsetO = 0;

		// Filtro outbound
		$indicadoresService->acumCantidadPivotes = 0;

		$indicadoresService->acumTipoOperacion = '';

		$indicadoresService->offsetMaximoCW4 = $indicadoresService->offsetMinimoTW4 = 0;
		$indicadoresService->offsetMaximoDW4 = $indicadoresService->offsetMinimoUW4 = 0;
		$indicadoresService->offsetMaximoOW4 = $indicadoresService->offsetMinimoCW4 = 0;
		$indicadoresService->offsetMaximoTW4 = $indicadoresService->offsetMinimoDW4 = 0;
		$indicadoresService->offsetMaximoUW4 = $indicadoresService->offsetMinimoOW4 = 0;
		$indicadoresService->acumFlAnulacionW4AlcistaActiva = false;
		$indicadoresService->acumFlAnulacionW4BajistaActiva = false;

		$indicadoresService->flAnulaCandidato = true;

		$indicadoresService->offsetCAbc = $indicadoresService->offsetBAbc = 0;
		$indicadoresService->offsetAAbc = $indicadoresService->offsetCAbCd = 0;
		$indicadoresService->offsetBAbCd = $indicadoresService->offsetAAbCd = 0;

		// Por ahora no utiliza filtros para anular candidatos
		$indicadoresService->acumconFiltrosCandidato = true;

		// Filtro outbound
		$indicadoresService->acumFlFiltroOutBound = false;
		
		$dataPrueba = DB::connection('trade')->table('trade.lecturas')
			->select('id',
					'fechaLectura as fechalectura',
					'chartTime as fecha',
					'openPrice as open',
					'highPrice as high',
					'lowPrice as low',
					'closePrice as close',
					'volume')
			->where('especie', request()->especie)
			->where('fechaLectura','>','2024-04-01')
			->where('fechaLectura','<','2024-04-02')
			->orderBy('fechaLectura', 'ASC')
			->take(100)
			->get();
		$batch = Bus::batch([])->dispatch();
		foreach($dataPrueba as $data)
		{
			$batch->add(new GeneraOrdenes($data, $indicadoresService, request()->especie, request()->calculobase, 
										request()->mmcorta, request()->mmlarga, request()->compresion, 
										request()->largovma, request()->largocci, request()->largoxtl,
										request()->umbralxtl, request()->swingsize, request()->filtroSetup));
		}

		return $batch;
	}

	public function batch()
	{
		$batchId = request()->id;

		return Bus::findBatch($batchId);
	}
}
