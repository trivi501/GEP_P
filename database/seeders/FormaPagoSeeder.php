<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormaPagoSeeder extends Seeder
{
    public function run(): void
    {
        $formas = [
            [1, '01', 'Efectivo'],
            [2, '02', 'Cheque'],
            [3, '04', 'Tarjeta de Crédito'],
            [4, '28', 'Tarjeta de Débito'],
            [5, '03', 'Transferencia Electrónica'],
        ];

        foreach ($formas as [$id, $satKey, $desc]) {
            DB::table('f4_c_formapago')->updateOrInsert(
                ['id' => $id],
                [
                    'c_FormaPago' => $satKey,
                    'Descripción' => $desc,
                    'activo' => 1,
                ]
            );
        }
    }
}
