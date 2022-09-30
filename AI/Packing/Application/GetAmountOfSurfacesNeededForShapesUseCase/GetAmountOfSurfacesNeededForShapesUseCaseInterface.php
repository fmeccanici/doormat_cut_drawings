<?php


namespace App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase;


interface GetAmountOfSurfacesNeededForShapesUseCaseInterface
{
    /**
     * @param GetAmountOfSurfacesNeededForShapesUseCaseInput $input
     * @return GetAmountOfSurfacesNeededForShapesUseCaseResult
     */
    public function execute(GetAmountOfSurfacesNeededForShapesUseCaseInput $input): GetAmountOfSurfacesNeededForShapesUseCaseResult;
}
