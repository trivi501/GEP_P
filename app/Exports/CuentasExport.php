<?php

namespace App\Exports;

use App\Models\Cuentas;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CuentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $data;

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $this->data = Cuentas::leftJoin('cuentas_pagos', function ($join) {
                $join->on('cuentas.id', '=', 'cuentas_pagos.cuenta_id')
                    ->whereBetween('cuentas_pagos.fecha_registro', [$this->fechaInicio, $this->fechaFin . ' 23:59:59']);
            })
            ->select('cuentas.id', 'cuentas.indetec', 'cuentas.descripcion', 'cuentas.importe')
            ->selectRaw('COALESCE(SUM(cuentas_pagos.monto), 0) as total_pagado')
            ->groupBy('cuentas.id', 'cuentas.indetec', 'cuentas.descripcion', 'cuentas.importe')
            ->orderBy('cuentas.id')
            ->get();

        return $this->data;
    }

    public function headings(): array
    {
        $fechaInicio = \Carbon\Carbon::parse($this->fechaInicio)->format('d/m/Y');
        $fechaFin = \Carbon\Carbon::parse($this->fechaFin)->format('d/m/Y');
        return [
            ['Reporte de Cuentas - Período: ' . $fechaInicio . ' al ' . $fechaFin],
            [],
            ['Indetec', 'Descripción', 'Importe', 'Total Pagado'],
        ];
    }

    public function map($cuenta): array
    {
        return [
            $cuenta->indetec,
            $cuenta->descripcion,
            '$' . number_format((float) $cuenta->importe, 2),
            '$' . number_format((float) $cuenta->total_pagado, 2),
        ];
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
                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sumImporte = 0;
                $sumPagado = 0;

                foreach ($this->data as $cuenta) {
                    if ($cuenta->indetec === '8') continue;
                    $sumImporte += (float) $cuenta->importe;
                    $sumPagado += (float) $cuenta->total_pagado;
                }

                $totalRow = 4 + $this->data->count();
                $sheet->setCellValue('A' . $totalRow, 'TOTAL (sin descuentos)');
                $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
                $sheet->setCellValue('C' . $totalRow, '$' . number_format($sumImporte, 2));
                $sheet->getStyle('C' . $totalRow)->getFont()->setBold(true);
                $sheet->setCellValue('D' . $totalRow, '$' . number_format($sumPagado, 2));
                $sheet->getStyle('D' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
