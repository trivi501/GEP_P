<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $cuentas = [
        'DESCUENTO SUBTOTAL PREDIAL (PRONTO PAGO / JUBILADO / PENSIONADO / ADULTO MAYOR)',
        'DESCUENTO RECARGOS',
        'DESCUENTO ACTUALIZACIÓN',
        'DESCUENTO MULTAS',
        'DESCUENTO GASTOS DE EJECUCIÓN',
    ];

    public function up(): void
    {
        foreach ($this->cuentas as $descripcion) {
            $exists = DB::table('cuentas')
                ->whereRaw("TRIM(REPLACE(REPLACE(descripcion, '\r', ''), '\n', '')) = ?", [$descripcion])
                ->exists();

            if (!$exists) {
                DB::table('cuentas')->insert([
                    'descripcion' => $descripcion,
                    'importe' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('cuentas')->whereIn('descripcion', $this->cuentas)->delete();
    }
};
