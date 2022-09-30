<?php

namespace App\AI\Packing\Domain\PackingAlgorithm;

use App\AI\Packing\Domain\Exceptions\LargestLineFirstPackingAlgorithmOperationException;
use App\AI\Packing\Domain\Position;
use App\AI\Packing\Domain\Shapes\PackableLine;
use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;

class LargestLineFirstAbstractPackingAlgorithm extends AbstractPackingAlgorithm
{
    /**
     * @throws LargestLineFirstPackingAlgorithmOperationException
     */
    function packShapesOnSurface(Collection $shapes, Surface $surface): Surface
    {
        // TODO: Task 19345: Verzin een betere abstractie voor packing algoritmes, wellicht splitsen in 1D en 2D packing algoritmes, surfaces en shapes
        if (! $surface instanceof \App\AI\Packing\Domain\Surfaces\LineSurface)
        {
            throw new LargestLineFirstPackingAlgorithmOperationException('Surface should be a line');
        }

        $this->shapes = $shapes;

        $lines = $shapes->sortByDesc(function (PackableLine $line) {
            return $line->length;
        })->values();

        $maxLineLength = $surface->getLength();

        $lines->each(function (PackableLine $line) use ($surface, $maxLineLength){
            $sum = collect($surface->packedShapes)->sum(function (PackableLine $line) {
                return $line->length();
            });

            if ($sum < $maxLineLength)
            {
                $length = $sum + $line->length();
                $x = $sum + $line->center()->getX();
                if ($length <= $maxLineLength)
                {
                    $line->setPackedPosition(new Position($x, 0));
                    $surface->addShape($line);
                }
            }
        });

        $this->shapes = $lines;

        return $surface;
    }
}
