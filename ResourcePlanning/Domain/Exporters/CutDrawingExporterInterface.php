<?php


namespace App\ResourcePlanning\Domain\Exporters;

use App\ResourcePlanning\Domain\Tasks\Cutting\CutDrawing;

interface CutDrawingExporterInterface
{
    /**
     * @param CutDrawing $cutDrawing
     * @param string $fileName
     * @return mixed Filepath of stored file
     */
    public function export(CutDrawing $cutDrawing, string $fileName): string;
}
