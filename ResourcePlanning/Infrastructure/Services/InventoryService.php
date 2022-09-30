<?php


namespace App\ResourcePlanning\Infrastructure\Services;


use App\Inventory\Application\DecreaseInventoryItemQuantity\DecreaseInventoryItemQuantity;
use App\Inventory\Application\DecreaseInventoryItemQuantity\DecreaseInventoryItemQuantityInput;
use App\Inventory\Application\IncreaseInventoryItemQuantity\IncreaseInventoryItemQuantity;
use App\Inventory\Application\IncreaseInventoryItemQuantity\IncreaseInventoryItemQuantityInput;
use App\Inventory\Domain\Repositories\InventoryItemRepositoryInterface;
use App\ResourcePlanning\Domain\Services\InventoryServiceInterface;
use Illuminate\Support\Facades\App;

class InventoryService implements \App\ResourcePlanning\Domain\Services\InventoryServiceInterface
{
    private InventoryItemRepositoryInterface $inventoryItemRepository;

    public function __construct()
    {
        $this->inventoryItemRepository = App::make(InventoryItemRepositoryInterface::class);
    }

    public function increaseInventoryItem(string $productCode, int $quantity)
    {
        $increaseInventoryItemQuantity = new IncreaseInventoryItemQuantity($this->inventoryItemRepository);

        $increaseInventoryItemQuantityInput = new IncreaseInventoryItemQuantityInput([
            "inventory_item" => [
                "product_code" => $productCode,
                "quantity" => $quantity
            ]
        ]);

        $increaseInventoryItemQuantity->execute($increaseInventoryItemQuantityInput);

    }

    public function decreaseInventoryItem(string $productCode, int $quantity)
    {
        // TODO: We should not know about InventoryItemRepositorInterface, which is located in Inventory bounded context
        $decreaseInventoryItemQuantity = new DecreaseInventoryItemQuantity($this->inventoryItemRepository);

        $decreaseInventoryItemQuantityInput = new DecreaseInventoryItemQuantityInput([
            "inventory_item" => [
                "product_code" => $productCode,
                "quantity" => $quantity
            ]
        ]);

        $decreaseInventoryItemQuantity->execute($decreaseInventoryItemQuantityInput);
    }
}
