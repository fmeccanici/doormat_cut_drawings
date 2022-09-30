<?php


namespace App\AI\Packing\Application\GetAmountOfSurfacesNeededForShapesUseCase;

use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;

class GetAmountOfSurfacesNeededForShapesUseCase implements GetAmountOfSurfacesNeededForShapesUseCaseInterface
{
    private AbstractPackingAlgorithm $packingAlgorithm;

    /**
     * GetAmountOfSurfacesNeededForShapesUseCase constructor.
     * @param AbstractPackingAlgorithm $packingAlgorithm
     */
    public function __construct(AbstractPackingAlgorithm $packingAlgorithm)
    {
        $this->packingAlgorithm = $packingAlgorithm;
    }

    /**
     * @inheritDoc
     */
    public function execute(GetAmountOfSurfacesNeededForShapesUseCaseInput $input): GetAmountOfSurfacesNeededForShapesUseCaseResult
    {
        $shapes = $input->shapes;
        $surface = $input->surface;

        $amount = $this->packingAlgorithm->getAmountOfSurfacesNeeded(collect($shapes), $surface);

        $result = new GetAmountOfSurfacesNeededForShapesUseCaseResult();

        $result->amount = $amount;

        return $result;
    }
}
