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

    public function __construct($fechaInicio, $fechaFin)
    {
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $pagos = Pago::with(['cuentasPagos.cuenta', 'historialCaja.cajero.usuario', 'predio'])
            ->whereBetween('fecha', [$this->fechaInicio . ' 00:00:00', $this->fechaFin . ' 23:59:59'])
            ->orderBy('fecha')
            ->get();

        $rows = collect();

        foreach ($pagos as $pago) {
            foreach ($pago->cuentasPagos as $cuentaPago) {
                $rows->push((object) [
                    'folio' => $pago->folio,
                    'fecha' => $pago->fecha,
                    'nombre' => $pago->nombre,
                    'rfc' => $pago->rfc,
                    'clave_catastral' => $pago->predio?->Clave_predial ?? '',
                    'tipo_pago' => $pago->tipo_pago,
                    'estatus' => $pago->estatus,
                    'cuenta_codigo' => $cuentaPago->cuenta?->cuenta ?? $cuentaPago->cuenta?->indetec,
                    'cuenta_descripcion' => $cuentaPago->cuenta?->descripcion ?? $cuentaPago->concepto,
                    'concepto' => $cuentaPago->concepto,
                    'monto' => $cuentaPago->monto,
                    'cajero' => $pago->historialCaja?->cajero?->usuario?->name,
                ]);
            }
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
            ['Folio', 'Fecha', 'Contribuyente', 'RFC', 'Clave Catastral', 'Tipo de Pago', 'Estatus', 'Cuenta', 'Descripción de Cuenta', 'Concepto', 'Cajero', 'Monto'],
        ];
    }

    public function map($row): array
    {
        return [
            $row->folio,
            $row->fecha ? \Carbon\Carbon::parse($row->fecha)->format('d/m/Y H:i') : '',
            $row->nombre,
            $row->rfc,
            $row->clave_catastral,
            $row->tipo_pago,
            $row->estatus,
            $row->cuenta_codigo,
            $row->cuenta_descripcion,
            $row->concepto,
            $row->cajero,
            '$' . number_format((float) $row->monto, 2),
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
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sumMonto = 0;
                foreach ($this->data as $row) {
                    $sumMonto += (float) $row->monto;
                }

                $totalRow = 4 + $this->data->count();
                $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                $sheet->getStyle('A' . $totalRow)->getFont()->setBold(true);
                $sheet->setCellValue('L' . $totalRow, '$' . number_format($sumMonto, 2));
                $sheet->getStyle('L' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}
