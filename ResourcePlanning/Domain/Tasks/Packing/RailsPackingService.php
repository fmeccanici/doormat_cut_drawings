<?php

namespace App\ResourcePlanning\Domain\Tasks\Packing;

use App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase\GetAmountOfSurfacesNeededForShapesUseCase;
use App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase\GetAmountOfSurfacesNeededForShapesUseCaseInput;
use App\AI\Packing\Application\PackShapesOnSurfaceUseCase\PackShapesOnSurfaceUseCase;
use App\AI\Packing\Application\PackShapesOnSurfaceUseCase\PackShapesOnSurfaceUseCaseInput;
use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;
use App\AI\Packing\Domain\PackingAlgorithm\LargestLineFirstAbstractPackingAlgorithm;
use App\AI\Packing\Domain\Shapes\PackableLine;
use App\AI\Packing\Domain\Surfaces\Surface;
use App\ResourcePlanning\Domain\FinishedGoods\SawedRails;
use App\ResourcePlanning\Domain\Resources\Rails;
use App\ResourcePlanning\Domain\Tasks\Sawing\ToBeSawedLine;
use App\SharedKernel\Geometry\Position;
use Illuminate\Support\Collection;

class RailsPackingService implements \App\ResourcePlanning\Domain\Services\PackingServiceInterface
{
    private LargestLineFirstAbstractPackingAlgorithm $packingAlgorithm;
    protected Collection $toBeSawedLinesThatDidNotFit;

    public function __construct()
    {
        $this->packingAlgorithm = new LargestLineFirstAbstractPackingAlgorithm();
    }

    /**
     * @inheritDoc
     */
    public function packRailsOnRail(Collection $rails, Rails $rail): Collection
    {
        $lines = $this->convertRailsToLines($rails);
        $line = $this->convertRailToSurface($rail);

        $packShapesOnSurfaceUseCase = new PackShapesOnSurfaceUseCase($this->packingAlgorithm);
        $packShapesOnSurfaceUseCaseInput = new PackShapesOnSurfaceUseCaseInput($lines, $line);
        $packShapesOnSurfaceUseCaseResult = $packShapesOnSurfaceUseCase->execute($packShapesOnSurfaceUseCaseInput);

        $surfaceWithPackedShapes = $packShapesOnSurfaceUseCaseResult->surfaceWithPackedShapes;
        $shapesThatDidNotFitOnSurface = $packShapesOnSurfaceUseCaseResult->shapesThatDidNotFitOnSurface;

        $packedShapes = $surfaceWithPackedShapes->packedShapes;
        $toBeSawedLines = collect();

        foreach ($packedShapes as $packedShape)
        {
            $toBeSawedLine = new ToBeSawedLine($packedShape->id(), $packedShape->length(), new Position($packedShape->getPackedPosition()->getX(), $packedShape->getPackedPosition()->getY()));
            $toBeSawedLines->push($toBeSawedLine);
        }

        $toBeSawedLinesThatDidNotFit = collect();

        foreach ($shapesThatDidNotFitOnSurface as $shapeThatDidNotFit)
        {
            $toBeSawedLineThatDidNotFit = new ToBeSawedLine($shapeThatDidNotFit->id(), $shapeThatDidNotFit->length(), null);
            $toBeSawedLinesThatDidNotFit->push($toBeSawedLineThatDidNotFit);
        }

        $this->toBeSawedLinesThatDidNotFit = $toBeSawedLinesThatDidNotFit;

        return $toBeSawedLines;
    }

    private function convertRailsToLines(Collection $rails): Collection
    {
        return $rails->map(function (SawedRails $rail) {
            return new PackableLine(uniqid(), $rail->length());
        });
    }

    private function convertRailToSurface(Rails $rail): Surface
    {
        return new \App\AI\Packing\Domain\Surfaces\LineSurface(uniqid(), $rail->length());
    }

    /**
     * @inheritDoc
     */
    public function getAmountOfRailsNeeded(Collection $rails, Rails $rail): int
    {
        $shapes = $this->convertRailsToLines($rails);
        $surface = $this->convertRailToSurface($rail);

        $packingAlgorithm = $this->getPackingAlgorithm();
        $getAmountOfSurfacesNeededForShapes = new GetAmountOfSurfacesNeededForShapesUseCase($packingAlgorithm);

        $getAmountOfSurfacesNeededForShapesInput = new GetAmountOfSurfacesNeededForShapesUseCaseInput($shapes->all(), $surface);
        $getAmountOfSurfacesNeededForShapesResult = $getAmountOfSurfacesNeededForShapes->execute($getAmountOfSurfacesNeededForShapesInput);

        return $getAmountOfSurfacesNeededForShapesResult->amount;
    }

    public function getPackingAlgorithm(): AbstractPackingAlgorithm
    {
        return new LargestLineFirstAbstractPackingAlgorithm();
    }
}
