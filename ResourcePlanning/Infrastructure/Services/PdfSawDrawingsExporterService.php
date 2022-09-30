<?php

namespace App\ResourcePlanning\Infrastructure\Services;

use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PdfSawDrawingsExporterService implements \App\ResourcePlanning\Domain\Services\SawDrawingsExporterServiceInterface
{
    private PDF $pdf;

    public function __construct()
    {
        $this->pdf = app('dompdf.wrapper');
    }

    /**
     * @inheritDoc
     */
    public function export(Collection $sawDrawings): string
    {
        $output = $this->pdf->loadView('warehouse.saw-drawing', [
            'saw_drawings' => $sawDrawings->all(),
            'quantity_rails' => $sawDrawings->count(),
            'saw_loss' => $sawDrawings->average->sawLoss()
        ])->output();

        $pdf = $output;
        $filePath = 'saw-drawings/zaaglijst.pdf';
        Storage::put($filePath, $pdf);

        return $filePath;

    }
}
