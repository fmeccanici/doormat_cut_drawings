<?php

namespace App\ResourcePlanning\Domain\Services;

use Illuminate\Support\Collection;

interface SawDrawingsExporterServiceInterface
{
    /**
     * @param Collection $sawDrawings
     * @return string Filepath
     */
    public function export(Collection $sawDrawings): string;
}
