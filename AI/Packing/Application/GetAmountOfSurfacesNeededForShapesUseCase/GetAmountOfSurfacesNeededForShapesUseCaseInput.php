<?php


namespace App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase;

use App\AI\Packing\Domain\Surfaces\Surface;

final class GetAmountOfSurfacesNeededForShapesUseCaseInput
{
    public array $shapes;
    public Surface $surface;

    public function __construct(array $shapes, Surface $surface)
    {
        $this->shapes = $shapes;
        $this->surface = $surface;
    }
}
