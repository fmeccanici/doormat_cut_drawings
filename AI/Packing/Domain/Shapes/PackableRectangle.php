<?php


namespace App\AI\Packing\Domain\Shapes;

use App\AI\Packing\Domain\Position;

class PackableRectangle extends PackableShape
{
    public function __construct(string $id,
                                float $width,
                                float $length,
                                ?string $surfaceId = null,
                                ?Position $packedPosition = null)
    {
        $this->id = $id;
        $this->setWidth($width);
        $this->setLength($length);
        $this->surfaceId = $surfaceId;
        $this->packedPosition = $packedPosition;
    }

    function convertToLines(): array
    {
        // TODO: Implement convertToLines() method.
    }

    function getArea(): float
    {
        return $this->width * $this->length;
    }

    function rotate(float $degrees): PackableShape
    {
        if ((int) $degrees == 90)
        {
            $tempLength = $this->length;
            $this->length = $this->width;
            $this->width = $tempLength;

            if ($this->packedPosition != null)
            {
                $this->packedPosition = new Position($this->getPackedPosition()->getY(), $this->getPackedPosition()->getX());
            }
        }
        // TODO: Add mathematics to rotate the rectangle
        else
        {

        }

        return $this;
    }

    // TODO: Write unit test for this function
    function roundShapeSizesToFirstIntegerDividedByTwo(): PackableRectangle
    {
        $width = (int) ceil($this->width);
        $length = (int) ceil($this->length);

        if ($width % 2 > 0)
        {
            $width = $width + 1;
        }

        if ($length % 2 > 0)
        {
            $length = $length + 1;
        }

        return new PackableRectangle($this->id, $width, $length);
    }

    public function getTopLeft(): Position
    {
        return $this->packedPosition->add(new Position(-$this->width / 2, $this->length / 2));
    }

    public function getTopRight(): Position
    {
        return $this->packedPosition->add(new Position($this->width / 2, $this->length / 2));
    }

    public function getBottomRight(): Position
    {
        return $this->packedPosition->add(new Position($this->width / 2, -$this->length / 2));
    }

    public function getBottomLeft(): Position
    {
        return $this->packedPosition->add(new Position(-$this->width / 2, -$this->length / 2));
    }

    function center(): Position
    {
        return new Position($this->width / 2, $this->length / 2);
    }
}
