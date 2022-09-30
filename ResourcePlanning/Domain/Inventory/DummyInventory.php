<?php


namespace App\ResourcePlanning\Domain\Inventory;
use App\ResourcePlanning\Domain\Resources\StockResources\CoupageStockResource;
use App\ResourcePlanning\Domain\Resources\StockResources\RollStockResource;
use App\ResourcePlanning\Domain\Resources\StockResources\StockResource;

class DummyInventory implements InventoryInterface
{
    public int $rollsTaken;
    public int $coupagesTaken;

    public function __construct()
    {
        $this->coupagesTaken = 0;
        $this->rollsTaken = 0;
    }

    public function isAvailable(StockResource $stockResource): bool
    {
        return true;
    }

    public function takeFromStock(StockResource $stockResource): StockResource
    {
        if ($stockResource instanceof CoupageStockResource)
        {
            $this->coupagesTaken++;
        } else if ($stockResource instanceof RollStockResource)
        {
            $this->rollsTaken++;
        }

        return $stockResource;
    }

    public function getAvailableAmount(StockResource $stockResource): int
    {
        return 99999;
    }
}
