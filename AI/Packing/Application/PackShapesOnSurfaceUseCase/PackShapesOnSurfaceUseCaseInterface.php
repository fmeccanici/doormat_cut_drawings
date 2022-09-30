<?php


namespace App\AI\Packing\Application\PackShapesOnSurfaceUseCase;

interface PackShapesOnSurfaceUseCaseInterface
{
    /**
     * @param PackShapesOnSurfaceUseCaseInput $input
     * @return PackShapesOnSurfaceUseCaseResult
     */
    public function execute(PackShapesOnSurfaceUseCaseInput $input): PackShapesOnSurfaceUseCaseResult;
}
