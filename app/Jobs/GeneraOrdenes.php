<?php

namespace App\Jobs;

use App\Services\Graficos\IndicadoresService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use DB;

class GeneraOrdenes implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    //public $tries = 1;
    //public $backoff = 1;
    private $especie;
    private $calculoBase;
    private $mmCorta;
    private $mmLarga;
    private $compresion;
    private $largoVMA;
    private $largoCCI;
    private $largoXTL;
    private $umbralXTL;
    private $swingSize;
    private $filtroSetup;
    private $factorCompresion;
    private $data;
    private $indicadoresService;

    //public $timeout = 36000;

    //public $failOnTimeout = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $indicadoresService, $especie, $calculobase, 
                                $mmcorta, $mmlarga, $compresion, $largovma, $largocci, $largoxtl,
                                $umbralxtl, $swingSize, $filtroSetup)
    {
        Log::info("1");
        $this->indicadoresService = $indicadoresService;
        $this->data = $data;
        $this->especie = $especie;
        $this->calculoBase = $calculobase;
        $this->mmCorta = $mmcorta;
        $this->mmLarga = $mmlarga;
        $this->compresion = $compresion;
        $this->largoVMA = $largovma;
        $this->largoCCI = $largocci;
        $this->largoXTL = $largoxtl;
        $this->umbralXTL = $umbralxtl;
        $this->swingSize = $swingSize;
        $this->filtroSetup = $filtroSetup;
        $this->factorCompresion = 5;
        switch($compresion)
        {
        case 1:
            $this->factorCompresion = 1;
            break;
        case 2:
            $this->factorCompresion = 5;
            break;
        case 3:
            $this->factorCompresion = 15;
            break;
        case 4:
            $this->factorCompresion = 60;
            break;
        case 5:
            $this->factorCompresion = 3600;
            break;
        }
        Log::info("2");
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {        
        Log::info("3");

        $fecha = date('Y-m-d H:i', ceil($this->data->fecha/1000));
        $open = $this->data->open;
        $low = $this->data->low;
        $high = $this->data->high;

        Log::info($fecha.' '.$open.' '.$low.' '.$high);
        $this->indicadoresService->generaDatosOrdenes($this->data, $this->especie, $this->calculoBase, 
                        $this->mmCorta, $this->mmLarga, $this->compresion, $this->largoVMA, $this->largoCCI, 
                        $this->largoXTL, $this->umbralXTL, $this->swingSize, $this->filtroSetup,
                        $this->factorCompresion);
    }
}
