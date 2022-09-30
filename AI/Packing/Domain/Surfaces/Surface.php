<?php


namespace App\AI\Packing\Domain\Surfaces;

use App\AI\Packing\Domain\Intersections\ShapesIntersectionCalculatorInterface;
use App\AI\Packing\Domain\Position;
use App\AI\Packing\Domain\Shapes\PackableShape;

abstract class Surface
{
    public string $id;
    public float $width;
    public float $length;
    public array $packedShapes;
    public ShapesIntersectionCalculatorInterface $intersectionCalculator;

    abstract function packedArea();
    abstract function unpackedArea();

    /**
     * @param PackableShape $shape
     * @return Position[]
     */
    abstract function getPossiblePackingPositionsForShape(PackableShape $shape): array;
    abstract function isShapeInsideSurface(PackableShape $shape): bool;
    abstract function getPackedShapeById(string $id): ?PackableShape;
    abstract function addShape(PackableShape $shape);
    abstract function getStepSize(): ?float;
    abstract function isShapeWeNeedToPackIntersectingWithPackedArea(PackableShape $shape);

    abstract function getWidth(): float;
    abstract function getLength(): float;
    abstract function removePackedShapeById(string $id);

    public function removeAllPackedShapes(): void
    {
        $this->packedShapes = [];
    }

    /**
     * @return PackableShape[]|null
     */
    public function getPackedShapes(): ?array
    {
        $result = [];

        foreach ($this->packedShapes as $packedShape)
        {
            if ($packedShape->packedPosition !== null)
            {
                $result[] = $packedShape;
            }
        }

        return $result;
    }

    public function unpackedShapes(): array
    {
        $result = [];

        foreach ($this->packedShapes as $packedShape)
        {
            if ($packedShape->packedPosition === null)
            {
                $result[] = $packedShape;
            }
        }

        return $result;
    }
}
