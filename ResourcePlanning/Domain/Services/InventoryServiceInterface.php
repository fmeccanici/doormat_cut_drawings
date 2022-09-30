<?php


namespace App\ResourcePlanning\Domain\Services;


interface InventoryServiceInterface
{
    public function increaseInventoryItem(string $productCode, int $quantity);
    public function decreaseInventoryItem(string $productCode, int $quantity);
}
