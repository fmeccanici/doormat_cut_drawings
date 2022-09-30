<?php

namespace App\AI\Packing\Domain\Shapes;

use App\AI\Packing\Domain\Position;

class PackableLine extends PackableShape
{
    public function __construct(string $id, int $length, ?string $surfaceId = null, ?Position $packedPosition = null)
    {
        $this->id = $id;
        $this->surfaceId = $surfaceId;
        $this->packedPosition = $packedPosition;
        $this->length = $length;
        $this->width = 0;
    }

    function convertToLines(): array
    {
        // TODO: Implement convertToLines() method.
    }

    function getArea(): float
    {
        return 0.0;
    }

    function rotate(float $degrees): PackableShape
    {
        return $this;
    }

    function roundShapeSizesToFirstIntegerDividedByTwo(): PackableShape
    {
        // TODO: Implement roundShapeSizesToFirstIntegerDividedByTwo() method.
    }

    function getTopLeft(): Position
    {
        return $this->getBottomLeft();
    }

    function getBottomLeft(): Position
    {
        return new Position(0, 0);
    }

    function getBottomRight(): Position
    {
        return $this->getTopRight();
    }

    function getTopRight(): Position
    {
        return new Position($this->length, 0);
    }

    function center(): Position
    {
        return new Position($this->length / 2, 0);
    }
}
