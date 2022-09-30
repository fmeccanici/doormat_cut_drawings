<?php


namespace App\ResourcePlanning\Domain\Tasks\Packing;


use App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase\GetAmountOfSurfacesNeededForShapesUseCase;
use App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase\GetAmountOfSurfacesNeededForShapesUseCaseInput;
use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;
use App\AI\Packing\Domain\Shapes\PackableRectangle;
use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Domain\Tasks\Cutting\ToBeCutRectangle;


abstract class PackingInterface
{

    protected array $toBeCutShapesThatDidNotFitOnRoll = [];

    /**
     * @param ToBeCutRectangle[] $toBeCutShapes
     * @param Roll $roll
     * @return array
     */
    abstract function packToBeCutShapesOnRoll(array $toBeCutShapes, Roll $roll): array;
    abstract function getToBeCutShapesThatDidNotFitOnRoll(): array;
    abstract function getPackingAlgorithm(): AbstractPackingAlgorithm;

    /**
     * @param ToBeCutRectangle[] $toBeCutShapes
     * @param Roll $roll
     * @return int
     */
    public function getAmountOfRollsNeeded(array $toBeCutShapes, Roll $roll): int
    {
        $packingAlgorithm = $this->getPackingAlgorithm();
        $getAmountOfSurfacesNeededForShapes = new GetAmountOfSurfacesNeededForShapesUseCase($packingAlgorithm);

        $shapes = $this->convertToBeCutShapesToShapes($toBeCutShapes);
        $surface = $this->convertRollToSurface($roll);

        $getAmountOfSurfacesNeededForShapesInput = new GetAmountOfSurfacesNeededForShapesUseCaseInput($shapes, $surface);
        $getAmountOfSurfacesNeededForShapesResult = $getAmountOfSurfacesNeededForShapes->execute($getAmountOfSurfacesNeededForShapesInput);

        return $getAmountOfSurfacesNeededForShapesResult->amount;
    }

    protected function convertToBeCutShapesToShapes(array $toBeCutShapes): array
    {
        $shapes = [];

        foreach ($toBeCutShapes as $toBeCutShape)
        {
            $shape = new PackableRectangle($toBeCutShape->id(), $toBeCutShape->width(), $toBeCutShape->length());
            $shapes[] = $shape;
        }

        return $shapes;
    }

    protected function convertRollToSurface(Roll $roll)
    {
        return new \App\AI\Packing\Domain\Surfaces\RectangleSurface($roll->id(), $roll->width(), $roll->length());
    }

    protected function determineToBeCutShapesThatDidNotFitOnRoll(array $toBeCutShapes, array $shapesThatDidNotFitOnSurface)
    {
        $this->toBeCutShapesThatDidNotFitOnRoll = [];

        foreach ($toBeCutShapes as $toBeCutShape)
        {
            foreach ($shapesThatDidNotFitOnSurface as $shapeThatDidNotFitOnSurface)
            {
                if ($shapeThatDidNotFitOnSurface->id() == $toBeCutShape->id())
                {
                    $this->toBeCutShapesThatDidNotFitOnRoll[] = $toBeCutShape;
                }
            }
        }
    }
}

