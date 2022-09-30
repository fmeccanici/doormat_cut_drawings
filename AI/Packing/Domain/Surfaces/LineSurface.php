<?php

namespace App\AI\Packing\Domain\Surfaces;

use App\AI\Packing\Domain\Exceptions\InvalidShapeException;
use App\AI\Packing\Domain\Shapes\PackableShape;

class LineSurface extends Surface
{
    protected int $decimalsAfterComma;

    public function __construct(string $id,
                                float $length,
                                array $packedShapes = [],
                                int   $decimalsAfterComma = 2)
    {
        $this->id = $id;
        $this->length = $length;
        $this->width = 0;
        $this->packedShapes = $packedShapes;
        $this->decimalsAfterComma = $decimalsAfterComma;
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
        // TODO: Implement getPossiblePackingPositionsForShape() method.
    }

    function isShapeInsideSurface(PackableShape $shape): bool
    {
        // TODO: Implement isShapeInsideSurface() method.
    }

    function getPackedShapeById(string $id): ?PackableShape
    {
        // TODO: Implement getPackedShapeById() method.
    }

    /**
     * @throws InvalidShapeException
     */
    function addShape(PackableShape $shape)
    {
        if (! $shape instanceof \App\AI\Packing\Domain\Shapes\PackableLine)
        {
            throw new InvalidShapeException('PackableShape should be a line');
        }

        $shape->surfaceId = $this->id;
        $this->packedShapes[] = $shape;
    }

    function getStepSize(): ?float
    {
        // TODO: Implement getStepSize() method.
    }

    function isShapeWeNeedToPackIntersectingWithPackedArea(PackableShape $shape)
    {
        // TODO: Implement isShapeWeNeedToPackIntersectingWithPackedArea() method.
    }

    function setDecimalsAfterComma(int $decimalsAfterComma)
    {
        // TODO: Implement setDecimalsAfterComma() method.
    }

    function getDecimalsAfterComma(): int
    {
        // TODO: Implement getDecimalsAfterComma() method.
    }

    function getWidth(): float
    {
        // TODO: Implement getWidth() method.
    }

    function getLength(): float
    {
        return $this->length;
    }

    function removePackedShapeById(string $id)
    {
        // TODO: Implement removePackedShapeById() method.
    }
}
