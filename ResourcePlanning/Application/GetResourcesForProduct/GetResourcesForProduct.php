<?php


namespace App\ResourcePlanning\Application\GetResourcesForProduct;

use App\ResourcePlanning\Domain\FinishedGoods\FinishedGoodFactory;
use App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface;

class GetResourcesForProduct implements GetResourcesForProductInterface
{
    /**
     * @var ResourceRepositoryInterface
     */
    private ResourceRepositoryInterface $resourceRepository;

    /**
     * GetResourcesForProduct constructor.
     * @param ResourceRepositoryInterface $resourceRepository
     */
    public function __construct(ResourceRepositoryInterface $resourceRepository)
    {
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * @inheritDoc
     */
    public function execute(GetResourcesForProductInput $input): GetResourcesForProductResult
    {
        $finishedGood = FinishedGoodFactory::create($input->productCode(), $input->productGroup());
        $resources = $this->resourceRepository->findAllByFinishedGood($finishedGood);
        return new GetResourcesForProductResult(collect($resources));
    }
}
