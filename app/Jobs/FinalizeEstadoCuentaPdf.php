<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use setasign\Fpdi\Fpdi;

class FinalizeEstadoCuentaPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 120;

    protected string $token;
    protected int $totalChunks;

    public function __construct(string $token, int $totalChunks)
    {
        $this->token = $token;
        $this->totalChunks = $totalChunks;
    }

    public function handle(): void
    {
        $tempDir = storage_path('app/temp');

        $hasPages = false;
        $pdf = new Fpdi();

        for ($i = 1; $i <= $this->totalChunks; $i++) {
            $chunkPath = "{$tempDir}/chunk_{$this->token}_{$i}.pdf";

            if (!file_exists($chunkPath)) {
                continue;
            }

            $pageCount = $pdf->setSourceFile($chunkPath);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $pdf->addPage();
                $pdf->useTemplate($tplId);
                $hasPages = true;
            }
        }

        if (!$hasPages) {
            Cache::put("pdf_status_{$this->token}", 'error', 600);
            Cache::put("pdf_error_{$this->token}", 'No se generaron páginas. Revisa los logs.', 600);
            return;
        }

        $finalPath = "{$tempDir}/estado_cuenta_masivo_{$this->token}.pdf";
        $pdf->Output('F', $finalPath);

        foreach (glob("{$tempDir}/chunk_{$this->token}_*.pdf") as $chunkFile) {
            @unlink($chunkFile);
        }

        Cache::put("pdf_path_{$this->token}", $finalPath, 600);
        Cache::put("pdf_status_{$this->token}", 'ready', 600);
    }
}
