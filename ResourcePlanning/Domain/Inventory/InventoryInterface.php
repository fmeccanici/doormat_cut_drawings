<?php


namespace App\ResourcePlanning\Domain\Inventory;


use App\ResourcePlanning\Domain\Resources\StockResources\StockResource;

interface InventoryInterface
{
    public function isAvailable(StockResource $stockResource): bool;
    public function getAvailableAmount(StockResource $stockResource): int;
    public function takeFromStock(StockResource $stockResource): StockResource;

}
