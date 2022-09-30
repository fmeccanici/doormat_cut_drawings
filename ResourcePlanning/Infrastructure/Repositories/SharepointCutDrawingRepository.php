<?php


namespace App\ResourcePlanning\Infrastructure\Repositories;


use App\ResourcePlanning\Domain\Exporters\CutDrawingExporterInterface;
use App\ResourcePlanning\Domain\Repositories\CutDrawingRepositoryInterface;
use App\ResourcePlanning\Domain\Tasks\Cutting\CutDrawing;
use Illuminate\Support\Facades\Storage;

class SharepointCutDrawingRepository implements CutDrawingRepositoryInterface
{
    protected CutDrawingExporterInterface $cutDrawingExporter;

    public function __construct(CutDrawingExporterInterface $cutDrawingExporter)
    {
        $this->cutDrawingExporter = $cutDrawingExporter;
    }

    public function add(CutDrawing $cutDrawing): void
    {
        $filePath = $this->cutDrawingExporter->export($cutDrawing, $cutDrawing->getFileName());
        $extension = '.' . pathinfo($filePath, PATHINFO_EXTENSION);
        $fileContent = file_get_contents($filePath);
        Storage::disk('cutting-machine')->put($cutDrawing->getFileName() . $extension, $fileContent);
    }
}
