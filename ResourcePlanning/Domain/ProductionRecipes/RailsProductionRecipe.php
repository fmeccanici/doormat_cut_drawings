<?php

namespace App\ResourcePlanning\Domain\ProductionRecipes;

use App\ResourcePlanning\Domain\Orders\OrderLine;
use App\ResourcePlanning\Domain\Resources\Rails;
use App\ResourcePlanning\Domain\Services\PackingServiceInterface;
use App\ResourcePlanning\Domain\Services\SawDrawingsExporterServiceInterface;
use App\ResourcePlanning\Domain\Tasks\Sawing\SawDrawing;
use App\ResourcePlanning\Domain\Tasks\Sawing\ToBeSawedLine;
use Illuminate\Support\Facades\App;

class RailsProductionRecipe extends ProductionRecipe
{
    private PackingServiceInterface $packingService;
    protected array $producedGoods;
    protected $sawDrawingExporterService;

    public function __construct()
    {
        $this->packingService = App::make(PackingServiceInterface::class);
        $this->sawDrawingExporterService = App::make(SawDrawingsExporterServiceInterface::class);
        $this->producedGoods = [];
    }

    public function produce(array $orderLines): array
    {
        $orderLines = collect($orderLines);
        $productCode = $orderLines->first()->finishedGood()->productCode();
        $material = $productCode;
        $locations = collect();
        $orderLines->each(function (OrderLine $orderLine) use ($locations) {
            $locations[$orderLine->finishedGood()->length()] = $orderLine->pickingContainer();
        });

        $toBeSawedRails = $orderLines->map(function (OrderLine $orderLine) {
            return $orderLine->finishedGood();
        });

        $stockRails = new Rails(uniqid(), 6000, $productCode, $material);

        $railsNeeded = $this->packingService->getAmountOfRailsNeeded($toBeSawedRails, $stockRails);

        $sawDrawings = collect();

        for ($i = 0; $i < $railsNeeded; $i++)
        {
            $toBeSawedLines = $this->packingService->packRailsOnRail($toBeSawedRails, $stockRails);

            $toBeSawedLines->map(function (ToBeSawedLine $toBeSawedLine) use ($locations) {
                $location = $locations[$toBeSawedLine->length()];
                $toBeSawedLine->changeLocation($location);
            });

            $sawDrawing = new SawDrawing(uniqid(), $toBeSawedLines, $stockRails);
            $sawDrawings->push($sawDrawing);
            $toBeSawedRails = $toBeSawedRails->filter(function ($toBeSawedRails) use ($toBeSawedLines) {
                 if ($toBeSawedLines->first(function ($toBeSawedLine) use ($toBeSawedRails) {
                     return $toBeSawedLine->length() === $toBeSawedRails->length();
                 }))
                 {
                     return false;
                 } else {
                     return true;
                 }
            });
        }

        $filePath = $this->sawDrawingExporterService->export($sawDrawings);
        $sawDrawings['file_path'] = $filePath;
        return $sawDrawings->all();
    }
}
