<?php

namespace App\Console\Commands;

use App\Models\Contribuyente;
use Illuminate\Console\Command;

class RegenerateCuentasContribuyentes extends Command
{
    protected $signature = 'cuentas:regenerate';
    protected $description = 'Regenera todas las cuentas de contribuyentes que están en 00000 o vacías';

    public function handle()
    {
        $contribuyentes = Contribuyente::where('cuenta', '00000')
            ->orWhereNull('cuenta')
            ->orWhere('cuenta', '')
            ->get();

        $total = $contribuyentes->count();
        $this->info("{$total} contribuyentes con cuenta inválida encontrados.");

        $cuentasExistentes = Contribuyente::whereNotIn('cuenta', ['00000', '', null])->pluck('cuenta')->toArray();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $generadas = 0;
        foreach ($contribuyentes as $contribuyente) {
            $ref = $contribuyente->nombre_moral
                ?? $contribuyente->primer_apellido
                ?? $contribuyente->nombre_completo
                ?? 'X';

            $letra = strtoupper(substr(trim($ref), 0, 1));
            if (!preg_match('/[A-Z]/', $letra)) {
                $letra = 'X';
            }

            do {
                $cuenta = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . $letra;
            } while (in_array($cuenta, $cuentasExistentes));

            $contribuyente->update(['cuenta' => $cuenta]);
            $cuentasExistentes[] = $cuenta;
            $generadas++;

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$generadas} cuentas regeneradas exitosamente.");
    }
}
