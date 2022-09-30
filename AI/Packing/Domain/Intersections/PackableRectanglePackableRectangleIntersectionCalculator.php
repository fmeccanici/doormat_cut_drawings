<?php


namespace App\AI\Packing\Domain\Intersections;

use App\AI\Packing\Domain\Shapes\PackableRectangle;
use App\AI\Packing\Domain\Shapes\PackableShape;
use InvalidArgumentException;

class PackableRectanglePackableRectangleIntersectionCalculator implements ShapesIntersectionCalculatorInterface
{

    public function isIntersecting(PackableShape $first, PackableShape $second): bool
    {
        if (! ($first instanceof PackableRectangle) || ! ($second instanceof PackableRectangle))
        {
            throw new InvalidArgumentException("Both shapes should be a ToBeCutRectangle");
        }

        if ($first->packedPosition == null)
        {
            throw new InvalidArgumentException("First shape has position NULL");
        }

        if ($second->packedPosition == null)
        {
            throw new InvalidArgumentException("Second shape has position NULL");
        }

        if ($first->getBottomRight()->getX() <= $second->getBottomLeft()->getX() || $first->getBottomLeft()->getX() >= $second->getBottomRight()->getX())
        {
            return false;
        }

        if ($first->getBottomRight()->getY() >= $second->getTopRight()->getY() || $first->getTopLeft()->getY() <= $second->getBottomLeft()->getY())
        {
            return false;
        }

        return true;
    }

    public function calculateOverlap(PackableShape $first, PackableShape $second): array
    {
        // TODO: Implement calculateOverlap() method.
    }
}
