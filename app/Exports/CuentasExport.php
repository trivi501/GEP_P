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

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        return Cuentas::leftJoin('cuentas_pagos', function ($join) {
                $join->on('cuentas.id', '=', 'cuentas_pagos.cuenta_id')
                    ->whereBetween('cuentas_pagos.fecha_registro', [$this->fechaInicio, $this->fechaFin . ' 23:59:59']);
            })
            ->select('cuentas.id', 'cuentas.cuenta', 'cuentas.subcuenta', 'cuentas.descripcion', 'cuentas.importe')
            ->selectRaw('COALESCE(SUM(cuentas_pagos.monto), 0) as total_pagado')
            ->groupBy('cuentas.id', 'cuentas.cuenta', 'cuentas.subcuenta', 'cuentas.descripcion', 'cuentas.importe')
            ->orderBy('cuentas.id')
            ->get();
    }

    public function headings(): array
    {
        $fechaInicio = \Carbon\Carbon::parse($this->fechaInicio)->format('d/m/Y');
        $fechaFin = \Carbon\Carbon::parse($this->fechaFin)->format('d/m/Y');
        return [
            ['Reporte de Cuentas - Período: ' . $fechaInicio . ' al ' . $fechaFin],
            [],
            ['Cuenta', 'Subcuenta', 'Descripción', 'Importe', 'Total Pagado'],
        ];
    }

    public function map($cuenta): array
    {
        return [
            $cuenta->cuenta,
            $cuenta->subcuenta,
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
                $event->sheet->getDelegate()->mergeCells('A1:E1');
                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            },
        ];
    }
}
