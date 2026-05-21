<?php

namespace App\Queries\Stock;

interface Articulo_MovimientoQueryInterface
{
    public function generaDatosRepStockOt($estado, $mventa_id,
                                            $desdearticulo, $hastaarticulo,
                                            $desdelinea_id, $hastalinea_id,
                                            $desdecategoria_id, $hastacategoria_id,
                                            $desdelote, $hastalote, $deposito_id);
    public function leeStockPorLote($lote, $articulo_id, $combinacion_id);
    public function buscaLoteImportacion($lotestock_id);
    public function generaDatosRepArticulosVendidos($tipoOrigen, $desdefecha, $hastafecha,
                                            $desdearticulo_id, $hastaarticulo_id,
                                            $desdecliente_id, $hastacliente_id,
                                            $desdelinea_id, $hastalinea_id,
                                            $mventa_id);
}

