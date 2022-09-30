<?php


namespace App\AI\Packing\Domain\Surfaces;

// TODO: Make separate geometry package: shared kernel for packing and resource planning and possibly other subdomains

use App\AI\Packing\Domain\Position;
use App\AI\Packing\Domain\Shapes\PackableShape;
use InvalidArgumentException;

class RectangleSurface extends Surface
{
    public function __construct(string $id,
                                float $width,
                                float $length,
                                array $packedShapes = [])
    {
        $this->id = $id;
        $this->width = $width;
        $this->length = $length;
        $this->packedShapes = $packedShapes;
    }

    function packedArea()
    {
        // TODO: Implement packedArea() method.
    }

    function unpackedArea()
    {
        // TODO: Implement unpackedArea() method.
    }

    function getPossiblePackingPositionsForShape(PackableShape $shape): array
    {
        if ($shape->length() > $this->getWidth() && $shape->width() > $this->getWidth())
        {
            throw new InvalidArgumentException("Both the shape's width (".$shape->width().") and length (".$shape->length()." are larger than the width of the surface (".$this->getWidth().")");
        }

        return $this->getPossiblePackingPositions($shape);

    }

    private function getPossiblePackingPositions(PackableShape $shape): array
    {
        if ($shape->width() > $this->getWidth())
        {
            $horizontalValues = [];
        } else {
            $horizontalValues = range($shape->width / 2, $this->width - $shape->width / 2, $this->getStepSize());
        }

        if ($shape->length() > $this->getLength())
        {
            $verticalValues = [];
        } else {
            $verticalValues = range($shape->length / 2, $this->length - $shape->length / 2, $this->getStepSize());
        }

        $possiblePackingPositions = [];

        foreach ($horizontalValues as $horizontalValue)
        {
            foreach ($verticalValues as $verticalValue)
            {
                $s = new \App\AI\Packing\Domain\Shapes\PackableRectangle($shape->id(), $shape->width(), $shape->length());
                $packedPosition = new Position($horizontalValue, $verticalValue);
                $s->setPackedPosition($packedPosition);

                if (! $this->isShapeWeNeedToPackIntersectingWithPackedArea($s))
                {
                    $possiblePackingPositions[] = new Position($horizontalValue, $verticalValue);
                }
            }
        }

        return $possiblePackingPositions;
    }

    function isShapeWeNeedToPackIntersectingWithPackedArea(PackableShape $shapeWeNeedToPack): bool
    {
        foreach ($this->getPackedShapes() as $alreadyPackedShape)
        {
            $intersectionCalculatorClassName = "App\AI\Packing\Domain\Intersections\\".
                $this->getClassNameWithoutNamespace($shapeWeNeedToPack::class).
                $this->getClassNameWithoutNamespace($alreadyPackedShape::class).
                'IntersectionCalculator';

            $this->intersectionCalculator = new $intersectionCalculatorClassName;

            if ($this->intersectionCalculator->isIntersecting($alreadyPackedShape, $shapeWeNeedToPack))
            {
                return true;
            }
        }

        return false;

    }

    public function getIntersectingShapeWithPackedArea(PackableShape $shapeWeNeedToPack): ?PackableShape
    {
        foreach ($this->getPackedShapes() as $alreadyPackedShape)
        {
            $intersectionCalculatorClassName = "App\AI\Packing\Domain\Intersections\\".
                $this->getClassNameWithoutNamespace($shapeWeNeedToPack::class).
                $this->getClassNameWithoutNamespace($alreadyPackedShape::class).
                'IntersectionCalculator';

            $this->intersectionCalculator = new $intersectionCalculatorClassName;

            if ($this->intersectionCalculator->isIntersecting($alreadyPackedShape, $shapeWeNeedToPack))
            {
                return $alreadyPackedShape;
            }
        }

        return null;

    }

    private function getClassNameWithoutNamespace(string $className)
    {
        $classNameAsArray = explode("\\", $className);
        return end($classNameAsArray);
    }

    function isShapeInsideSurface(PackableShape $shape): bool
    {
        $packedPosition = $shape->packedPosition;

        if ($packedPosition->getX() - $shape->width / 2 < 0 || $packedPosition->getX() + $shape->width / 2 > $this->width)
        {
            return false;
        }

        if ($packedPosition->getY() - $shape->length / 2 < 0 || $packedPosition->getY() + $shape->length / 2 > $this->length)
        {
            return false;
        }

        return true;
    }

    function getPackedShapeById(string $id): ?PackableShape
    {
        $packedShapesById = [];

        foreach ($this->packedShapes as $packedShape)
        {
            if ($packedShape->id == $id)
            {
                $packedShapesById[] = $packedShape;
            }
        }

        if (sizeof($packedShapesById) > 1)
        {
            throw new MultipleShapesWithSameIdException();
        }

        if (sizeof($packedShapesById) == 0)
        {
            return null;
        }

        return $packedShapesById[0];

    }

    function addShape(PackableShape $shape)
    {
        if ($shape->getPackedPosition() === null)
        {
            throw new InvalidArgumentException("PackableShape does not have a packing position");
        }

        if ($this->getPackedShapeById($shape->id()) != null)
        {
            throw new InvalidArgumentException("PackableShape with id ".$shape->id()." is already packed in this surface");
        }

        if ($this->isShapeWeNeedToPackIntersectingWithPackedArea($shape))
        {
            throw new InvalidArgumentException("To be added PackableShape ".$shape->id()." with Packed Position (".$shape->getPackedPosition()->getX().", ".$shape->getPackedPosition()->getY().")"." is intersecting with already packed shape ");
        }

        if (! $this->isShapeInsideSurface($shape))
        {
            throw new InvalidArgumentException("To be added PackableShape ".$shape->id()." is outside the surface with Packed Position (".$shape->getPackedPosition()->getX().", ".$shape->getPackedPosition()->getY().")");
        }

        $shape->surfaceId = $this->id;

        $this->packedShapes[] = $shape;
    }

    function getStepSize(): ?float
    {
        return 1;
    }

    function getWidth(): float
    {
        return $this->width;
    }

    function getLength(): float
    {
        return $this->length;
    }


    function removePackedShapeById(string $id)
    {
        $newShapesArray = [];

        foreach ($this->getPackedShapes() as $shape)
        {
            if ($shape->getId() != $id)
            {
                $newShapesArray[] = $shape;
            }
        }

        $this->packedShapes = $newShapesArray;
    }
}
