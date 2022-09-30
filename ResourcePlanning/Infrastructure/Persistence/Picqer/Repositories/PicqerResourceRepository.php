<?php

namespace App\ResourcePlanning\Infrastructure\Persistence\Picqer\Repositories;

use App\ResourcePlanning\Domain\Exceptions\InvalidProductException;
use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\FinishedGoods\MixPaint;
use App\ResourcePlanning\Domain\Resources\BasePaint;
use App\ResourcePlanning\Domain\Resources\Resource;
use App\ResourcePlanning\Infrastructure\ApiClients\ApiClient;
use App\ResourcePlanning\Infrastructure\ApiClients\PicqerApiClient;
use App\ResourcePlanning\Infrastructure\Exceptions\PicqerResourceRepositoryOperationException;
use Illuminate\Support\Arr;
class PicqerResourceRepository implements \App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface
{
    public function __construct(PicqerApiClient $apiClient)
    {
        $this->apiClient = $apiClient->getClient();
    }

    /**
     * @inheritDoc
     * @throws PicqerResourceRepositoryOperationException
     * @throws InvalidProductException
     */
    public function findAllByFinishedGood(FinishedGood $finishedGood): array
    {
        $apiResponse = $this->apiClient->getProductByProductcode($finishedGood->productCode());

        if (! $apiResponse["success"])
        {
            throw new PicqerResourceRepositoryOperationException("Failed getting product with product code " . $finishedGood->productCode() . " from Picqer.");
        }

        $tags = collect(Arr::get($apiResponse, 'data.tags', []));

        if ($tags->isEmpty() && $finishedGood instanceof MixPaint)
        {
            throw new InvalidProductException("Er is geen basisblik gekoppeld voor eind product met product code " . $finishedGood->productCode() . " in Picqer");
        }

        // TODO: Als er meerdere tags zijn moet hij de basis ean goed kunnen parsen en niet de eerste pakken, voor eerste iteratie is dit prima
        // Task 18878: Zorg dat de PicqerResourceRepository kan omgaan met meerdere tags, en de basis ean daaruit parset
        $baseEan = $tags->keys()->first();

        $apiResponse = $this->apiClient->getProductByProductcode($baseEan);

        if (! $apiResponse["success"])
        {
            throw new PicqerResourceRepositoryOperationException("Failed getting product (base EAN) with product code " . $finishedGood->productCode() . " from Picqer.");
        }

        if(! $baseProductName = Arr::get($apiResponse, 'data.name')) {
            throw new InvalidProductException('De naam van het basis product is onbekend voor product code ' . $finishedGood->productCode() . ' in Picqer.');
        }

        // TODO: Do not hard code base paint when adding more product groups
        // Task 18879: Niet hardcoded van BasePaint resource in PiqcerResourceRepository
        $resource = new BasePaint($baseEan, $baseProductName);

        return array($resource);
    }

    /**
     * @inheritDoc
     */
    public function findOneById(string $id): ?Resource
    {
        // TODO: Implement find() method.
    }

    /**
     * @inheritDoc
     */
    public function addOne(Resource $resource): void
    {
        // TODO: Implement add() method.
    }
}
