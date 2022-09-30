<?php


namespace App\AI\Packing\Domain\PackingAlgorithm;

use App\AI\Packing\Domain\Line;
use App\AI\Packing\Domain\Shapes\PackableShape;
use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;

abstract class AbstractPackingAlgorithm
{
    /**
     * @var Collection<PackableShape|Line>
     */
    protected Collection $shapes;
    protected Surface $surface;

    /**
     *
     * @param Collection $shapes
     * @param Surface $surface
     * @return Surface
     */
    abstract function packShapesOnSurface(Collection $shapes, Surface $surface): Surface;

    public function getAmountOfSurfacesNeeded(Collection $shapes, Surface $surface): int
    {
        $amountOfSurfacesNeeded = 1;
        $this->packShapesOnSurface($shapes, $surface);

        $shapesThatDitNotFitOnSurface = $this->getShapesThatDidNotFitOnSurface();

        while ($shapesThatDitNotFitOnSurface->isNotEmpty())
        {
            $amountOfSurfacesNeeded++;
            $surface->removeAllPackedShapes();

            $this->packShapesOnSurface($shapesThatDitNotFitOnSurface, $surface);

            $shapesThatDitNotFitOnSurface = $this->getShapesThatDidNotFitOnSurface();
        }

        return $amountOfSurfacesNeeded;
    }

    public function getShapesThatDidNotFitOnSurface(): Collection
    {
        return $this->shapes->filter(function (PackableShape $shape) {
            return ! $shape->isPacked();
        })->values();
    }
}
