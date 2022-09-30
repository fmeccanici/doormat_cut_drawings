<?php


namespace App\ResourcePlanning\Application\GetResourcesForProduct;


interface GetResourcesForProductInterface
{
    /**
     * @param GetResourcesForProductInput $input
     * @return GetResourcesForProductResult
     */
    public function execute(GetResourcesForProductInput $input): GetResourcesForProductResult;
}
