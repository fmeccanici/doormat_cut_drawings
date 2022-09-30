<?php


namespace App\ResourcePlanning\Domain\Exporters;

use App\ResourcePlanning\Domain\Tasks\Cutting\CutDrawing;
use Illuminate\Support\Facades\Storage;

class CutDrawingZccExporter implements CutDrawingExporterInterface
{

    public function export(CutDrawing $cutDrawing, string $fileName): string
    {
        $fileNameWithExtension = $fileName.'.zcc';
        $cutDrawingString = $cutDrawing->asString();

        $filePath = 'cut-drawings/zcc/' . $fileNameWithExtension;
        Storage::put($filePath, $cutDrawingString);

        return Storage::path($filePath);
    }
}
