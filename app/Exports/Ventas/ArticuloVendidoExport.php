<?php

namespace App\Exports\Ventas;

use App\Services\Stock\Articulo_MovimientoService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArticuloVendidoExport implements FromView, WithColumnFormatting, WithMapping, ShouldAutoSize, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    private $tipoOrigen;
    private $desdefecha;
    private $hastafecha;
    private $desdearticulo_id;
    private $hastaarticulo_id;
    private $desdecliente_id;
    private $hastacliente_id;
    private $desdelinea_id;
    private $hastalinea_id;
    private $mventa_id;
    private $nombremventa;

    private $articulo_movimientoService;

    public function __construct(Articulo_MovimientoService $articulo_movimientoservice)
    {
        $this->articulo_movimientoService = $articulo_movimientoservice;
    }

    public function view(): View
    {
        $reporte = $this->articulo_movimientoService->generaDatosRepArticulosVendidos(
            $this->tipoOrigen,
            $this->desdefecha,
            $this->hastafecha,
            $this->desdearticulo_id,
            $this->hastaarticulo_id,
            $this->desdecliente_id,
            $this->hastacliente_id,
            $this->desdelinea_id,
            $this->hastalinea_id,
            $this->mventa_id
        );

        $titulo = $this->tipoOrigen == 'NACIONAL'
            ? 'LISTADO DE VENTAS POR ARTICULO NACIONAL CON IDENTIFICACION DE CLIENTE'
            : 'LISTADO DE VENTAS POR ARTICULO IMPORTADO CON IDENTIFICACION DE CLIENTE';

        return view('exports.ventas.reportearticulovendido.reportearticulovendido', [
            'titulo' => $titulo,
            'articulos' => $reporte['articulos'],
            'totales' => $reporte['totales'],
            'desdefecha' => $this->desdefecha,
            'hastafecha' => $this->hastafecha,
            'marca' => $this->nombremventa,
        ]);
    }

    public function columnFormats(): array
    {
        return [];
    }

    public function map($row): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11, 'name' => 'Arial']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 6,
            'C' => 18,
            'D' => 8,
            'E' => 10,
            'F' => 4,
            'G' => 16,
            'H' => 8,
            'I' => 35,
        ];
    }

    public function title(): string
    {
        return $this->tipoOrigen == 'NACIONAL' ? 'Articulos Nacionales' : 'Articulos Importados';
    }

    public function parametros($tipoOrigen, $desdefecha, $hastafecha,
                            $desdearticulo_id, $hastaarticulo_id,
                            $desdecliente_id, $hastacliente_id,
                            $desdelinea_id, $hastalinea_id,
                            $mventa_id, $nombremventa)
    {
        $this->tipoOrigen = $tipoOrigen;
        $this->desdefecha = $desdefecha;
        $this->hastafecha = $hastafecha;
        $this->desdearticulo_id = $desdearticulo_id;
        $this->hastaarticulo_id = $hastaarticulo_id;
        $this->desdecliente_id = $desdecliente_id;
        $this->hastacliente_id = $hastacliente_id;
        $this->desdelinea_id = $desdelinea_id;
        $this->hastalinea_id = $hastalinea_id;
        $this->mventa_id = $mventa_id;
        $this->nombremventa = $nombremventa;

        return $this;
    }
}
