<?php


namespace App\AI\Packing\Application\PackShapesOnSurfaceUseCase;

use App\AI\Packing\Domain\Surfaces\Surface;
use Illuminate\Support\Collection;

final class PackShapesOnSurfaceUseCaseInput
{
    public Collection $shapes;
    public Surface $surface;

    public function __construct(Collection $shapes, Surface $surface)
    {
        $this->shapes = $shapes;
        $this->surface = $surface;
    }
}
