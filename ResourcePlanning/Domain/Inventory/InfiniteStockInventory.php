<?php


namespace App\ResourcePlanning\Domain\Inventory;

use App\ResourcePlanning\Domain\Resources\StockResources\RollStockResource;
use App\ResourcePlanning\Domain\Resources\StockResources\StockResource;

class InfiniteStockInventory implements InventoryInterface
{
    // TODO: Get available stock by material, e.g. with roll material it gives the roll with correct size that is available

    public function isAvailable(StockResource $stockResource): bool
    {
        return true;
    }

    public function takeFromStock(StockResource $stockResource): StockResource
    {
        return $stockResource;
    }

    public function getAvailableAmount(StockResource $stockResource): int
    {
        return 99999;
    }
}
