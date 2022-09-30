<?php


namespace App\ResourcePlanning\Domain\Repositories;


use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\Resources\Resource;

interface ResourceRepositoryInterface
{
    /**
     * @param FinishedGood $finishedGood
     * @return Resource[]
     */
    public function findAllByFinishedGood(FinishedGood $finishedGood): array;

    /**
     * @param string $id
     * @return ?Resource
     */
    public function findOneById(string $id): ?Resource;

    /**
     * @param Resource $resource
     * @return void
     */
    public function addOne(Resource $resource): void;
}
