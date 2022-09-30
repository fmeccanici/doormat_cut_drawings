<?php


namespace App\AI\Packing\Domain\PackingAlgorithm;

use App\AI\Packing\Domain\Line;
use App\AI\Packing\Domain\Position;
use App\AI\Packing\Domain\Shapes\PackableShape;
use App\AI\Packing\Domain\Surfaces\RectangleSurface;
use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BottomLeftLargestAreaHeuristicAbstractPackingAlgorithm extends AbstractPackingAlgorithm
{

    public function packShapesOnSurface(Collection $shapes, Surface|Line $surface): Surface
    {
        if (! $this->doShapesHaveUniqueIds($shapes))
        {
            throw new InvalidArgumentException("Shapes do not have unique id's");
        }

        $this->shapes = $shapes;
        $this->surface = $surface;

        $shapesSortedOnArea = $this->sortShapesOnArea($shapes->all());

        foreach ($shapesSortedOnArea as $shapeWeNeedToPack)
        {
            if ($this->isShapeLengthOrWidthSmallerThanSurfaceWidth($shapeWeNeedToPack))
            {
                $shapeWeNeedToPack = $shapeWeNeedToPack->roundShapeSizesToFirstIntegerDividedByTwo();
            }

            $originalShape = clone $shapeWeNeedToPack;
            $rotatedShape = $shapeWeNeedToPack->rotate(90);

            $possiblePackingPositionsOriginalShape = $surface->getPossiblePackingPositionsForShape($originalShape);

            $highFloatingPointValue = 9999999.0;
            $packingPositionOriginalShape = new Position($highFloatingPointValue, $highFloatingPointValue);

            foreach ($possiblePackingPositionsOriginalShape as $possiblePackingPosition)
            {
                if ($this->isPositionMoreLowerLeftThanOtherPosition($possiblePackingPosition, $packingPositionOriginalShape))
                {
                    $packingPositionOriginalShape = $possiblePackingPosition;
                }
                else {
                    continue;
                }
            }

            $possiblePackingPositionsRotatedShape = $surface->getPossiblePackingPositionsForShape($rotatedShape);
            $packingPositionRotatedShape = new Position($highFloatingPointValue, $highFloatingPointValue);

            foreach ($possiblePackingPositionsRotatedShape as $possiblePackingPosition)
            {
                if ($this->isPositionMoreLowerLeftThanOtherPosition($possiblePackingPosition, $packingPositionRotatedShape))
                {
                    $packingPositionRotatedShape = $possiblePackingPosition;
                }
                else {
                    continue;
                }
            }

            if ($this->isPositionMoreLowerLeftThanOtherPosition($packingPositionRotatedShape, $packingPositionOriginalShape))
            {
                $shapeWeNeedToPack->setPackedPosition($packingPositionRotatedShape);

                foreach ($this->shapes as $shape)
                {
                    if ($shape->id() == $shapeWeNeedToPack->id())
                    {
                        $shape->rotate(90);
                    }
                }
            } else {
                $shapeWeNeedToPack->rotate(90);
                $shapeWeNeedToPack->setPackedPosition($packingPositionOriginalShape);
            }

            // either rotated and original shape do not fit on surface
            if ($shapeWeNeedToPack->getPackedPosition()->getX() === $highFloatingPointValue || $shapeWeNeedToPack->getPackedPosition()->getY() === $highFloatingPointValue)
            {
                continue;
            }

            $this->surface->addShape($shapeWeNeedToPack);
        }

        $this->shrinkShapesToOriginalSizesAndMultiplyByOneHundred();

        $failedShapes = $this->moveShapesMoreBottomLeftUntilTheyDontFitAnymore();

        // TODO: Refactor zodat deze logica niet hier staat maar in functie
        $largestYValue = 0;
        $correspondingLength = null;

        foreach ($this->surface->getPackedShapes() as $packedShape)
        {
            if ($packedShape->getPackedPosition()->getY() > $largestYValue)
            {
                $largestYValue = $packedShape->getPackedPosition()->getY();
                $correspondingLength = $packedShape->length();
            }
        }

        foreach ($failedShapes as $failedShape)
        {
            $position = new Position($failedShape->length() / 2,$largestYValue + $correspondingLength / 2 + $failedShape->length() / 2);
            $failedShape->setPackedPosition($position);

            try {
                $this->surface->addShape($failedShape);

                // Needed to make sure getShapesThatDidNotFitOnSurface() still works
                foreach ($this->shapes as $shape)
                {
                    if ($shape->id() === $failedShape->id())
                    {
                        $shape->setPackedPosition($position);
                    }
                }

            } catch (InvalidArgumentException $e)
            {
                $failedShape->unpack();
            }
        }

        return $this->surface;
    }

    private function isShapeLengthOrWidthSmallerThanSurfaceWidth(PackableShape $shape): bool
    {
        return $shape->width() < $this->surface->getWidth() || $shape->length() < $this->surface->getWidth();
    }


    /**
     * @return PackableShape[] Failed shapes
     */
    private function moveShapesMoreBottomLeftUntilTheyDontFitAnymore(): array
    {
        $stepSize = 1;

        $packedShapes = $this->surface->getPackedShapes();
        $this->surface->removeAllPackedShapes();

        $surfaceWithMillimeterStepSize = new RectangleSurface("test",$this->surface->getWidth() * 100, $this->surface->getLength() * 100);
        $failedShapes = [];

        foreach ($packedShapes as $packedShape)
        {
            while ($surfaceWithMillimeterStepSize->isShapeInsideSurface($packedShape) && ! $surfaceWithMillimeterStepSize->isShapeWeNeedToPackIntersectingWithPackedArea($packedShape))
            {
                $this->moveShapeToTheLeft($packedShape, $stepSize);
            }

            if (! $surfaceWithMillimeterStepSize->isShapeInsideSurface($packedShape) || $surfaceWithMillimeterStepSize->isShapeWeNeedToPackIntersectingWithPackedArea($packedShape))
            {
                $this->moveShapeToTheRight($packedShape, $stepSize);
            }

            while ($surfaceWithMillimeterStepSize->isShapeInsideSurface($packedShape) && ! $surfaceWithMillimeterStepSize->isShapeWeNeedToPackIntersectingWithPackedArea($packedShape))
            {
                $this->moveShapeToTheBottom($packedShape, $stepSize);
            }

            if (! $surfaceWithMillimeterStepSize->isShapeInsideSurface($packedShape) || $surfaceWithMillimeterStepSize->isShapeWeNeedToPackIntersectingWithPackedArea($packedShape))
            {
                $this->moveShapeToTheTop($packedShape, $stepSize);
            }


            // Nodig omdat:
            // Zie Bug 16174: Stacker Web werkt niet bij een grote paklijst
            // TODO: Refactor code zodat dit duidelijker is
            try {
                $surfaceWithMillimeterStepSize->addShape($packedShape);
                $packedShape = $this->convertPackedShapeBackToCentimeters($packedShape);
                $this->surface->addShape($packedShape);
                $this->copyPackedShapeToOriginalShape($packedShape);

            } catch (InvalidArgumentException $e)
            {
                $packedShape = $this->convertPackedShapeBackToCentimeters($packedShape);
                $failedShapes[] = $packedShape;
                Log::error("Failure in shrinking rectangle ".$packedShape->id()." with width ".$packedShape->width()." and length ".$packedShape->length());
            }

        }

        return $failedShapes;
    }

    private function convertPackedShapeBackToCentimeters(PackableShape $packedShape): PackableShape
    {
        $result = new \App\AI\Packing\Domain\Shapes\PackableRectangle($packedShape->id(), $packedShape->width() / 100, $packedShape->length() / 100);
        $result->setPackedPosition(new Position($packedShape->getPackedPosition()->getX() / 100, $packedShape->getPackedPosition()->getY() / 100));
        return $result;
    }

    private function copyPackedShapeToOriginalShape(PackableShape $packedShape)
    {
        foreach ($this->shapes as $originalShape)
        {
            if ($packedShape->id() == $originalShape->id())
            {
                $originalShape->setWidth($packedShape->width());
                $originalShape->setLength($packedShape->length());
                $position = new Position($packedShape->getPackedPosition()->getX(), $packedShape->getPackedPosition()->getY());
                $originalShape->setPackedPosition($position);
            }
        }
    }

    private function moveShapeToTheLeft(PackableShape $shape, float $stepSize)
    {
        $newPosition = new Position($shape->getPackedPosition()->getX() - $stepSize, $shape->getPackedPosition()->getY());
        $shape->setPackedPosition($newPosition);
    }

    private function moveShapeToTheRight(PackableShape $shape, float $stepSize)
    {
        $newPosition = new Position($shape->getPackedPosition()->getX() + $stepSize, $shape->getPackedPosition()->getY());
        $shape->setPackedPosition($newPosition);
    }

    private function moveShapeToTheBottom(PackableShape $shape, float $stepSize)
    {
        $newPosition = new Position($shape->getPackedPosition()->getX(), $shape->getPackedPosition()->getY() - $stepSize);
        $shape->setPackedPosition($newPosition);
    }

    private function moveShapeToTheTop(PackableShape $shape, float $stepSize)
    {
        $newPosition = new Position($shape->getPackedPosition()->getX(), $shape->getPackedPosition()->getY() + $stepSize);
        $shape->setPackedPosition($newPosition);
    }

    private function doShapesHaveUniqueIds(Collection $shapes): bool
    {
        $ids = $shapes->map(function (PackableShape $shape)
        {
            return $shape->id;
        });

        return $ids->unique()->count() === $shapes->count();
    }

    /**
     * @param PackableShape[] $shapes
     * @return PackableShape[]
     */
    public function sortShapesOnArea(array $shapes): array
    {
        $arrayUsedForSorting = [];

        foreach ($shapes as $shape)
        {
            $arrayUsedForSorting[$shape->id()] = $shape->getArea();
        }

        arsort($arrayUsedForSorting);

        $sortedShapes = [];

        foreach ($arrayUsedForSorting as $shapeId => $area)
        {
            foreach ($shapes as $shape)
            {
                if ($shape->id() == $shapeId)
                {
                    // clone needed to store by value instead of reference
                    $sortedShapes[] = clone $shape;
                }
            }
        }

        return $sortedShapes;
    }


    /**
     * @param PackableShape[] $shapes
     * @return PackableShape[]
     */
    private function sortShapesOnLengthCenterVector(array $shapes): array
    {
        $arrayUsedForSorting = [];

        foreach ($shapes as $shape)
        {
            $length = sqrt(pow($shape->getPackedPosition()->getX(), 2) + pow($shape->getPackedPosition()->getY(), 2));
            $arrayUsedForSorting[$shape->id()] = $length;
        }

        arsort($arrayUsedForSorting);

        $sortedShapes = [];

        foreach ($arrayUsedForSorting as $shapeId => $length)
        {
            foreach ($shapes as $shape)
            {
                if ($shape->id() == $shapeId)
                {
                    // clone needed to store by value instead of reference
                    $sortedShapes[] = clone $shape;
                }
            }
        }

        return array_reverse($sortedShapes);
    }

    private function shrinkShapesToOriginalSizesAndMultiplyByOneHundred()
    {
        foreach ($this->surface->getPackedShapes() as $packedShape)
        {
            foreach ($this->shapes as $originalShape)
            {
                if ($originalShape->id() === $packedShape->id())
                {
                    $packedShape->setWidth($originalShape->width() * 100);
                    $packedShape->setLength($originalShape->length() * 100);
                    $packedShape->setPackedPosition(new Position($packedShape->getPackedPosition()->getX() * 100, $packedShape->getPackedPosition()->getY() * 100));
                }
            }
        }
    }

    private function isPositionMoreLowerLeftThanOtherPosition(Position $first, Position $second): bool
    {
        if ($first->norm() < $second->norm())
        {
            return true;
        }

        return false;
    }

}
