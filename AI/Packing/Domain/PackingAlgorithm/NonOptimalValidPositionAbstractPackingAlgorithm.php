<?php


namespace App\AI\Packing\Domain\PackingAlgorithm;


use App\AI\Packing\Domain\Position;
use App\AI\Packing\Domain\Shapes\PackableShape;
use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;

class NonOptimalValidPositionAbstractPackingAlgorithm extends AbstractPackingAlgorithm
{

    /**
     * @param Collection<PackableShape> $shapes
     * @param Surface $surface
     * @return Surface
     */
    public function packShapesOnSurface(Collection $shapes, Surface $surface): Surface
    {
        $this->shapes = $shapes;

        for ($i = 0; $i < sizeof($shapes); $i++)
        {
            $currentShape = $shapes[$i];

            if ($i === 0)
            {
                $currentShape->setPackedPosition($currentShape->center());
            } else {
                $previousShape = $shapes[$i - 1];

                $n = 2;
                while (! $previousShape->isPacked())
                {
                    $previousShape = $shapes[$i - $n];
                    $n++;
                }

                $packedPosition = new Position($currentShape->width() / 2, $previousShape->getPackedPosition()->getY() + $previousShape->length() / 2 + $currentShape->length() / 2);
                $currentShape->setPackedPosition($packedPosition);

                if (! $surface->isShapeInsideSurface($currentShape))
                {
                    $currentShape->rotate(90);
                    $packedPosition = new Position($currentShape->width() / 2, $previousShape->getPackedPosition()->getY() + $previousShape->length() / 2 + $currentShape->length() / 2);
                    $currentShape->setPackedPosition($packedPosition);
                }

                if (! $surface->isShapeInsideSurface($currentShape))
                {
                    $currentShape->unpack();
                }

            }

            $surface->packedShapes[] = $currentShape;

        }

        return $surface;
    }

}
