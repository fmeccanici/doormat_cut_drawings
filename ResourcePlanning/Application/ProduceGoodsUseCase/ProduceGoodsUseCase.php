<?php


namespace App\ResourcePlanning\Application\ProduceGoodsUseCase;

use App\ResourcePlanning\Domain\Exceptions\ResourcePlannerException;
use App\ResourcePlanning\Domain\Repositories\CutDrawingRepositoryInterface;
use App\ResourcePlanning\Domain\ResourcePlanner;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingMachine;
use App\ResourcePlanning\Domain\Tasks\Packing\PackingInterface;
use Illuminate\Support\Facades\App;

class ProduceGoodsUseCase implements ProduceGoodsUseCaseInterface
{

    private ResourcePlanner $resourcePlanner;
    private CuttingMachine $cutting;
    private PackingInterface $packing;

    public function __construct()
    {
        $this->cutting = new CuttingMachine(App::make(CutDrawingRepositoryInterface::class));
        $this->packing = App::make(PackingInterface::class);
        $this->resourcePlanner = new ResourcePlanner($this->cutting, $this->packing);
    }

    /**
     * @inheritDoc
     * @throws ResourcePlannerException
     */
    public function execute(ProduceGoodsUseCaseInput $input): ProduceGoodsUseCaseResult
    {
        $producedGoods = $this->resourcePlanner->produceGoods($input->orderLines());
        $result = new ProduceGoodsUseCaseResult();
        $result->producedGoods = $producedGoods;
        $result->failedGoods = $this->resourcePlanner->failedGoods();

        return $result;
    }
}
