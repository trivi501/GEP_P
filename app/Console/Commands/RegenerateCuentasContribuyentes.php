<?php

namespace App\Console\Commands;

use App\Models\Contribuyente;
use Illuminate\Console\Command;

class RegenerateCuentasContribuyentes extends Command
{
    protected $signature = 'cuentas:regenerate';
    protected $description = 'Regenera cuentas inválidas (00000, vacías) en tb_contribuyentes';

    public function handle()
    {
        $this->info('Iniciando regeneración de cuentas...');

        $invalidos = Contribuyente::where(function ($q) {
            $q->where('cuenta', '00000')
              ->orWhereNull('cuenta')
              ->orWhere('cuenta', '');
        })->select('id_contribuyente', 'cuenta', 'nombre_moral', 'primer_apellido')->get();

        $total = $invalidos->count();
        if ($total === 0) {
            $this->info('No se encontraron cuentas inválidas.');
            return;
        }

        $this->info("{$total} cuentas inválidas encontradas. Regenerando...");

        $existentes = Contribuyente::whereNot('cuenta', '00000')
            ->whereNotNull('cuenta')
            ->where('cuenta', '!=', '')
            ->pluck('cuenta')
            ->toArray();

        $this->info(count($existentes) . ' cuentas válidas existentes.');

        $bar = $this->output->createProgressBar($total);
        $generadas = 0;

        foreach ($invalidos as $c) {
            $letra = 'X';
            $ref = trim($c->nombre_moral ?? $c->primer_apellido ?? '');
            if ($ref !== '') {
                $first = mb_strtoupper(mb_substr($ref, 0, 1));
                if (preg_match('/[A-Z]/u', $first)) {
                    $letra = $first;
                }
            }

            do {
                $cuenta = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . $letra;
            } while (in_array($cuenta, $existentes, true));

            Contribuyente::where('id_contribuyente', $c->id_contribuyente)->update(['cuenta' => $cuenta]);
            $existentes[] = $cuenta;
            $generadas++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$generadas} cuentas regeneradas.");
    }
}
