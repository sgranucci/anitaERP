<?php

namespace App\Queries\Stock;

use App\Models\Stock\Articulo;
use App\ApiAnita;
use DB;

class Articulo_MovimientoQuery implements Articulo_MovimientoQueryInterface
{
    protected $model;

    /**
     * PostRepository constructor.
     *
     * @param Post $post
     */
    public function __construct(Articulo $cliente)
    {
        $this->model = $cliente;
    }

    public function generaDatosRepStockOt($estado, $mventa_id,
                                            $desdearticulo, $hastaarticulo,
                                            $desdelinea_id, $hastalinea_id)
    {
        $articulo_query = $this->model->select('articulo_movimiento.ordentrabajo_id as ordentrabajo_id',
                            'combinacion.foto as foto', 
                            'linea.nombre as nombrelinea',
                            'articulo.sku as sku', 
                            'combinacion.codigo as codigocombinacion',
                            'combinacion.nombre as nombrecombinacion',
                            'mventa.nombre as nombremarca',
                            'combinacion.estado as estado',
                            'articulo_movimiento.lote as lote',
                            'articulo_movimiento.modulo_id as modulo_id',
                            'articulo_movimiento_talle.talle_id as talle_id',
                            'talle.nombre as nombretalle',
                            'articulo_movimiento_talle.cantidad as cantidad',
                            'articulo_movimiento_talle.precio as precio')
                            ->join('combinacion', 'combinacion.articulo_id', 'articulo.id')
                            ->join('linea', 'linea.id', 'articulo.linea_id')
                            ->join('mventa', 'mventa.id', 'articulo.mventa_id')
                            ->join('articulo_movimiento', 'articulo_movimiento.combinacion_id', 'combinacion.id')
                            ->join('articulo_movimiento_talle', 'articulo_movimiento_talle.articulo_movimiento_id', 'articulo_movimiento.id')
                            ->join('talle', 'talle.id', 'articulo_movimiento_talle.talle_id')
                            ->whereBetween('articulo.linea_id', [$desdelinea_id, $hastalinea_id])
                            ->where('articulo_movimiento.lote', '>', '0')
        					->orderBy('nombrelinea','ASC')
                            ->orderBy('modulo_id', 'ASC')
                            ->orderBy('sku','ASC')
                            ->orderBy('codigocombinacion', 'ASC')
                            ->orderBy('lote','ASC');
                            
        if ($desdearticulo != '' && $hastaarticulo != '')
            $articulo_query = $articulo_query->whereBetween('articulo.descripcion', [$desdearticulo, $hastaarticulo]);
            
        if ($mventa_id != 0)
            $articulo_query = $articulo_query->where('articulo.mventa_id', $mventa_id);

        switch($estado)
        {
        case 'ACTIVAS':
            $articulo_query = $articulo_query->where('combinacion.estado', 'A');
            break;
        case 'INACTIVAS':
            $articulo_query = $articulo_query->where('combinacion.estado', 'I');
            break;
        }
        $articulo_query = $articulo_query->get();

		return $articulo_query;
    }
}
