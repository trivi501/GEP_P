<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateCuentasContribuyentes extends Command
{
    protected $signature = 'cuentas:regenerate';
    protected $description = 'Regenera cuentas inválidas (00000) en tb_contribuyentes';

    public function handle()
    {
        $this->info('Iniciando...');

        $rows = DB::select("SELECT id_contribuyente, nombre_completo, primer_apellido FROM tb_contribuyentes WHERE cuenta = '00000' OR cuenta IS NULL OR cuenta = ''");

        $total = count($rows);
        $this->info("{$total} registros con cuenta inválida.");

        if ($total === 0) {
            $this->info('Nada que regenerar.');
            return;
        }

        $existentes = [];
        DB::table('tb_contribuyentes')
            ->where('cuenta', '!=', '00000')
            ->whereNotNull('cuenta')
            ->where('cuenta', '!=', '')
            ->select('cuenta')
            ->orderBy('id_contribuyente')
            ->chunk(5000, function ($chunk) use (&$existentes) {
                foreach ($chunk as $c) {
                    $existentes[$c->cuenta] = true;
                }
            });

        $this->info(count($existentes) . ' cuentas válidas.');

        $bar = $this->output->createProgressBar($total);
        $gen = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            foreach ($chunk as $r) {
                $letra = 'X';
                $ref = trim($r->primer_apellido ?? $r->nombre_completo ?? '');
                if ($ref !== '') {
                    $first = mb_strtoupper(mb_substr($ref, 0, 1));
                    $first = iconv('UTF-8', 'ASCII//TRANSLIT', $first) ?: $first;
                    if (preg_match('/[A-Z]/', $first)) {
                        $letra = $first;
                    }
                }

                do {
                    $cuenta = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . $letra;
                } while (isset($existentes[$cuenta]));

                DB::update("UPDATE tb_contribuyentes SET cuenta = ? WHERE id_contribuyente = ?", [$cuenta, $r->id_contribuyente]);
                $existentes[$cuenta] = true;
                $gen++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info("{$gen} cuentas regeneradas.");
    }
}
