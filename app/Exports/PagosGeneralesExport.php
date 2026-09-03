<?php

namespace App\Exports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class PagosGeneralesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $data;

    const CONCEPT_COLUMNS = [
        'rezago_predial' => 'PREDIAL URBANO AÑOS ANTERIORES (REZAGO)',
        'rezago_recargos' => 'RECARGOS PREDIAL URBANO',
        'rezago_actualizacion' => 'ACTUALIZACIONES PREDIAL URBANO',
        'multas' => 'MULTAS FISCALES PREDIAL URBANO',
        'gastos_ejecucion' => 'GASTOS DE EJECUCIÓN PREDIAL URBANO',
        'actual_predial' => 'PREDIAL URBANO AÑO ACTUAL',
        'actual_recargos' => 'RECARGOS PREDIAL URBANO',
        'actual_actualizacion' => 'ACTUALIZACIONES PREDIAL URBANO',
        'descuento_recargos' => 'Descuento Recargos',
        'descuento_multas' => 'Descuento Multas',
    ];

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    private function clasificarConcepto(string $concepto): ?string
    {
        if (preg_match('/^Predial \(/', $concepto)) return 'rezago_predial';
        if (preg_match('/^Predial \d{4}$/', $concepto)) return 'actual_predial';
        if (preg_match('/^Recargos \(/', $concepto)) return 'rezago_recargos';
        if (preg_match('/^Recargos \d{4}$/', $concepto)) return 'actual_recargos';
        if (preg_match('/^Actualización \(/u', $concepto)) return 'rezago_actualizacion';
        if (preg_match('/^Actualización \d{4}$/u', $concepto)) return 'actual_actualizacion';
        if (str_starts_with($concepto, 'Multa')) return 'multas';
        if (str_starts_with($concepto, 'Gastos Ejecución')) return 'gastos_ejecucion';
        if ($concepto === 'Descuento Recargos') return 'descuento_recargos';
        if ($concepto === 'Descuento Multas') return 'descuento_multas';

        return null;
    }

    public function collection()
    {
        $pagos = Pago::with(['cuentasPagos', 'historialCaja.cajero.usuario', 'predio'])
            ->whereBetween('fecha', [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59'])
            ->orderBy('fecha')
            ->get();

        $rows = collect();

        foreach ($pagos as $pago) {
            $montos = array_fill_keys(array_keys(self::CONCEPT_COLUMNS), 0);

            foreach ($pago->cuentasPagos as $cuentaPago) {
                $columna = $this->clasificarConcepto(trim($cuentaPago->concepto));
                if ($columna !== null) {
                    $montos[$columna] += (float) $cuentaPago->monto;
                }
            }

            $rows->push((object) array_merge([
                'folio' => $pago->folio,
                'fecha' => $pago->fecha,
                'nombre' => $pago->nombre,
                'clave_catastral' => $pago->predio?->Clave_predial ?? '',
                'tipo_pago' => $pago->tipo_pago,
                'estatus' => $pago->estatus,
                'cajero' => $pago->historialCaja?->cajero?->usuario?->name,
                'monto' => $pago->monto,
            ], $montos));
        }

        $this->data = $rows;

        return $rows;
    }

    public function headings(): array
    {
        $fechaInicio = \Carbon\Carbon::parse($this->fechaInicio)->format('d/m/Y');
        $fechaFin = \Carbon\Carbon::parse($this->fechaFin)->format('d/m/Y');

        return [
            ['Reporte de Pagos Generales por Cuenta - Período: ' . $fechaInicio . ' al ' . $fechaFin],
            [],
            array_merge(
                ['Folio', 'Fecha', 'Contribuyente', 'Clave Catastral', 'Tipo de Pago', 'Estatus'],
                array_values(self::CONCEPT_COLUMNS),
                ['Cajero', 'Monto']
            ),
        ];
    }

    public function map($row): array
    {
        $montos = array_map(fn($key) => number_format((float) $row->$key, 2), array_keys(self::CONCEPT_COLUMNS));

        return array_merge([
            $row->folio,
            $row->fecha ? \Carbon\Carbon::parse($row->fecha)->format('d/m/Y H:i') : '',
            $row->nombre,
            $row->clave_catastral,
            $row->tipo_pago,
            $row->estatus,
        ], $montos, [
            $row->cajero,
            '$' . number_format((float) $row->monto, 2),
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = $sheet->getHighestColumn();
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sumMonto = 0;
                foreach ($this->data as $row) {
                    $sumMonto += (float) $row->monto;
                }

                $totalRow = 4 + $this->data->count();
                $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
                $sheet->setCellValue($lastColumn . $totalRow, '$' . number_format($sumMonto, 2));
                $sheet->getStyle($lastColumn . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
