<?php


namespace App\AI\Packing\Domain\Shapes;

use App\AI\Packing\Domain\Position;

class PackableTriangle extends PackableShape
{

    function isPacked(): bool
    {
        // TODO: Implement isPacked() method.
    }

    function getPackedPosition(): ?Position
    {
        // TODO: Implement getPackedPosition() method.
    }

    function setPackedPosition(Position $stackedPosition): PackableShape
    {
        // TODO: Implement setPackedPosition() method.
    }

    function getSurfaceId(): ?string
    {
        // TODO: Implement getSurfaceId() method.
    }

    /**
     * @inheritDoc
     */
    function convertToLines(): array
    {
        // TODO: Implement convertToLines() method.
    }

    function getArea(): float
    {
        // TODO: Implement getArea() method.
    }

    function rotate(float $degrees): PackableShape
    {
        // TODO: Implement rotate() method.
    }

    function roundShapeSizesToFirstIntegerDividedByTwo(): PackableShape
    {
        // TODO: Implement roundShapeSizesToFirstIntegerDividedByTwo() method.
    }

    function id(): string
    {
        // TODO: Implement getId() method.
    }

    function length(): float
    {
        // TODO: Implement getLength() method.
    }

    function width(): float
    {
        // TODO: Implement getWidth() method.
    }

    function setLength(float $length)
    {
        // TODO: Implement setLength() method.
    }

    function setWidth(float $width)
    {
        // TODO: Implement setWidth() method.
    }

    function setId(string $id)
    {
        // TODO: Implement setId() method.
    }

    function setSurfaceId(string $surfaceId)
    {
        // TODO: Implement setSurfaceId() method.
    }

    function setDecimalsAfterComma(int $decimalsAfterComma)
    {
        // TODO: Implement setDecimalsAfterComma() method.
    }

    function getDecimalsAfterComma(): int
    {
        // TODO: Implement getDecimalsAfterComma() method.
    }

    function unpack()
    {
        // TODO: Implement unpack() method.
    }

    function getTopLeft(): Position
    {
        // TODO: Implement getTopLeft() method.
    }

    function getBottomLeft(): Position
    {
        // TODO: Implement getBottomLeft() method.
    }

    function getBottomRight(): Position
    {
        // TODO: Implement getBottomRight() method.
    }

    function getTopRight(): Position
    {
        // TODO: Implement getTopRight() method.
    }

    function center(): Position
    {
        // TODO: Implement center() method.
    }
}
