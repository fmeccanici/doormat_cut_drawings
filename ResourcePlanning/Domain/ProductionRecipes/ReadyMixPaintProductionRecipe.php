<?php


namespace App\ResourcePlanning\Domain\ProductionRecipes;


use App\Inventory\Application\DecreaseInventoryItemQuantity\DecreaseInventoryItemQuantity;
use App\Inventory\Application\DecreaseInventoryItemQuantity\DecreaseInventoryItemQuantityInput;
use App\Inventory\Application\IncreaseInventoryItemQuantity\IncreaseInventoryItemQuantity;
use App\Inventory\Application\IncreaseInventoryItemQuantity\IncreaseInventoryItemQuantityInput;
use App\Inventory\Domain\Repositories\InventoryItemRepositoryInterface;
use App\ResourcePlanning\Domain\Orders\OrderLine;
use App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface;
use App\ResourcePlanning\Domain\Resources\Coupage;
use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Domain\Services\InventoryServiceInterface;
use App\ResourcePlanning\Domain\Tasks\CreateBatchOfCoupagesTask;
use App\ResourcePlanning\Domain\Tasks\CreateBatchOfRollsTask;
use App\ResourcePlanning\Domain\Tasks\ProduceDoormatsFromCoupagesTask;
use App\ResourcePlanning\Domain\Tasks\ProduceDoormatsFromRollsTask;
use App\Warehouse\Domain\Services\ResourcePlanningServiceInterface;
use Illuminate\Support\Facades\App;

class ReadyMixPaintProductionRecipe extends ProductionRecipe
{
    // Made public for tests (Paint Production Recipe Test)
    public $resourceRepository;
    private array $producedGoods;

    public function __construct()
    {
        $this->resourceRepository = App::make(ResourceRepositoryInterface::class);

        $this->producedGoods = [];
    }

    public function produce(array $orderLines): array
    {
        $orderLines = collect($orderLines);

        $this->producedGoods = $orderLines->map(function (OrderLine $orderLine) {
                return $orderLine->finishedGood();
        })->toArray();

        return $this->producedGoods;
    }
}
