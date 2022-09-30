<?php


namespace App\ResourcePlanning\Domain\ProductionRecipes;


use App\ResourcePlanning\Domain\Resources\Coupage;
use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Domain\Tasks\CreateBatchOfCoupagesTask;
use App\ResourcePlanning\Domain\Tasks\CreateBatchOfRollsTask;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingInterface;
use App\ResourcePlanning\Domain\Tasks\Packing\Packing;
use App\ResourcePlanning\Domain\Tasks\Packing\PackingInterface;
use App\ResourcePlanning\Domain\Tasks\ProduceDoormatsFromCoupagesTask;
use App\ResourcePlanning\Domain\Tasks\ProduceDoormatsFromRollsTask;
use Illuminate\Support\Facades\App;

class DoormatsProductionRecipe extends ProductionRecipe
{
    private CuttingInterface $cutting;
    private PackingInterface $packing;
    protected array $producedGoods;

    public function __construct()
    {
        $this->cutting = App::make(CuttingInterface::class);
        $this->packing = App::make(PackingInterface::class);

        $this->producedGoods = [];
    }

    public function produce(array $orderLines): array
    {
        $createBatchTask = new CreateBatchOfCoupagesTask();
        $createBatchTaskResult = $createBatchTask->execute($orderLines);

        $produceDoormatsFromCoupagesTask = new ProduceDoormatsFromCoupagesTask($this->cutting);
        $producedGoods = $produceDoormatsFromCoupagesTask->execute($createBatchTaskResult);
        $this->producedGoods = array_merge($this->producedGoods, $producedGoods);

        $createBatchTask = new CreateBatchOfRollsTask();
        $createBatchTaskResult = $createBatchTask->execute($orderLines);

        $produceDoormatsFromRollsTask = new ProduceDoormatsFromRollsTask($this->cutting, $this->packing);
        $producedGoods = $produceDoormatsFromRollsTask->execute($createBatchTaskResult);
        $this->producedGoods = array_merge($this->producedGoods, $producedGoods);

        return $this->producedGoods;
    }
}
