<?php


namespace App\AI\Packing\Application\PackShapesOnSurfaceUseCase;

use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;

final class PackShapesOnSurfaceUseCaseResult
{
    public Surface $surfaceWithPackedShapes;
    public Collection $shapesThatDidNotFitOnSurface;
}
