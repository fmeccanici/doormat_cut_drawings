<?php


namespace App\ResourcePlanning\Domain\Tasks\Packing;


use App\AI\Packing\Application\PackShapesOnSurfaceUseCase\PackShapesOnSurfaceUseCase;
use App\AI\Packing\Application\PackShapesOnSurfaceUseCase\PackShapesOnSurfaceUseCaseInput;
use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;
use App\AI\Packing\Domain\PackingAlgorithm\NonOptimalValidPositionAbstractPackingAlgorithm;
use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Domain\Tasks\Cutting\ToBeCutRectangle;
use App\SharedKernel\Geometry\Position;

class NonOptimalPacking extends PackingInterface
{

    /**
     * @param ToBeCutRectangle[] $toBeCutShapes
     * @param Roll $roll
     * @return array
     */
    public function packToBeCutShapesOnRoll(array $toBeCutShapes, Roll $roll): array
    {
        $packingAlgorithm = new NonOptimalValidPositionAbstractPackingAlgorithm();
        $packShapesOnSurfaceUseCase = new PackShapesOnSurfaceUseCase($packingAlgorithm);

        $shapes = $this->convertToBeCutShapesToShapes($toBeCutShapes);
        $surface = $this->convertRollToSurface($roll);

        $packShapesOnSurfaceUseCaseInput = new PackShapesOnSurfaceUseCaseInput(collect($shapes), $surface);
        $packShapesOnSurfaceUseCaseResult = $packShapesOnSurfaceUseCase->execute($packShapesOnSurfaceUseCaseInput);

        $surfaceWithPackedShapes = $packShapesOnSurfaceUseCaseResult->surfaceWithPackedShapes;

        $packedToBeCutShapes = [];

        foreach ($surfaceWithPackedShapes->getPackedShapes() as $packedShape)
        {
            foreach ($toBeCutShapes as $toBeCutShape)
            {
                if ($toBeCutShape->id() == $packedShape->id())
                {
                    $toBeCutShape->setCutPosition(new Position($packedShape->getPackedPosition()->getX(), $packedShape->getPackedPosition()->getY()));
                    $packedToBeCutShapes[] = $toBeCutShape;
                }
            }
        }

        $shapesThatDidNotFitOnSurface = $packShapesOnSurfaceUseCaseResult->shapesThatDidNotFitOnSurface;
        $this->determineToBeCutShapesThatDidNotFitOnRoll($toBeCutShapes, $shapesThatDidNotFitOnSurface->all());

        return $packedToBeCutShapes;
    }

    public function getToBeCutShapesThatDidNotFitOnRoll(): array
    {
        return $this->toBeCutShapesThatDidNotFitOnRoll;
    }

    function getPackingAlgorithm(): AbstractPackingAlgorithm
    {
        return new NonOptimalValidPositionAbstractPackingAlgorithm();
    }
}
