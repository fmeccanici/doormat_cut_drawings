<?php


namespace App\AI\Packing\Domain\Intersections;


use App\AI\Packing\Domain\Shapes\PackableShape;

interface ShapesIntersectionCalculatorInterface
{
    public function isIntersecting(PackableShape $first, PackableShape $second): bool;
    public function calculateOverlap(PackableShape $first, PackableShape $second): array;
}
