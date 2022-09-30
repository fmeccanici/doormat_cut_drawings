<?php


namespace App\AI\Packing\Domain\Shapes;

use App\AI\Packing\Domain\Position;

abstract class PackableShape
{
    public string $id;
    public float $width;
    public float $length;
    public ?Position $packedPosition;
    public ?string $surfaceId;

    /**
     * @return PackableLine[]
     */
    abstract function convertToLines(): array;
    abstract function getArea(): float;
    abstract function rotate(float $degrees): PackableShape;
    abstract function roundShapeSizesToFirstIntegerDividedByTwo(): PackableShape;
    abstract function getTopLeft(): Position;
    abstract function getBottomLeft(): Position;
    abstract function getBottomRight(): Position;
    abstract function getTopRight(): Position;
    abstract function center(): Position;

    public function isPacked(): bool
    {
        return $this->packedPosition != null;
    }

    public function unpack()
    {
        $this->packedPosition = null;
    }

    public function getPackedPosition(): ?Position
    {
        if ($this->packedPosition == null)
        {
            throw new ShapeNotPackedException();
        }

        return $this->packedPosition;
    }

    public function setPackedPosition(Position $packedPosition): PackableShape
    {
        $this->packedPosition = $packedPosition;
        return $this;
    }

    public function getSurfaceId(): ?string
    {
        if ($this->surfaceId == null)
        {
            throw new ShapeNotPackedException();
        }

        return $this->surfaceId;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function length(): float
    {
        return $this->length;
    }

    public function width(): float
    {
        return $this->width;
    }

    public function setLength(float $length)
    {
        $this->length = $length;
    }

    public function setWidth(float $width)
    {
        $this->width = $width;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function setSurfaceId(string $surfaceId)
    {
        $this->surfaceId = $surfaceId;
    }

}
