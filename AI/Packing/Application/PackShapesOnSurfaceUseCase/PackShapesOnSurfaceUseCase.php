<?php


namespace App\AI\Packing\Application\PackShapesOnSurfaceUseCase;

use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;

class PackShapesOnSurfaceUseCase implements PackShapesOnSurfaceUseCaseInterface
{
    private AbstractPackingAlgorithm $packingAlgorithm;

    public function __construct(AbstractPackingAlgorithm $packingAlgorithm)
    {
        $this->packingAlgorithm = $packingAlgorithm;
    }

    public function execute(PackShapesOnSurfaceUseCaseInput $input): PackShapesOnSurfaceUseCaseResult
    {
        $shapes = $input->shapes;
        $surface = $input->surface;

        $surface = $this->packingAlgorithm->packShapesOnSurface($shapes, $surface);
        $shapesThatDidNotFitOnSurface = $this->packingAlgorithm->getShapesThatDidNotFitOnSurface();

        $result = new PackShapesOnSurfaceUseCaseResult();

        $result->surfaceWithPackedShapes = $surface;
        $result->shapesThatDidNotFitOnSurface = $shapesThatDidNotFitOnSurface;

        return $result;
    }
}
