<?php


namespace App\ResourcePlanning\Infrastructure\Repositories;


use App\ResourcePlanning\Domain\Repositories\SalesOrderRepositoryInterface;
use App\ResourcePlanning\Domain\ResourcePlanner;

class EloquentSalesOrderRepository implements SalesOrderRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function findResourcePlanning(int $id): ?ResourcePlanner
    {
        return ResourcePlanner::find($id);
    }
}
