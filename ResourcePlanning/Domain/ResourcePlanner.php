<?php


namespace App\ResourcePlanning\Domain;

use App\ResourcePlanning\Domain\Exceptions\ResourcePlannerException;
use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingInterface;
use App\ResourcePlanning\Domain\Tasks\Packing\PackingInterface;
use Illuminate\Support\Collection;

class ResourcePlanner
{
    private array $producedGoods;
    private array $failedGoods;
    private CuttingInterface $cutting;
    private PackingInterface $packing;

    public function __construct(CuttingInterface $cutting,
                                PackingInterface $packing)
    {
        $this->cutting = $cutting;
        $this->packing = $packing;
        $this->producedGoods = [];
    }

    /**
     * @param array $orderLines
     * @return FinishedGood[]
     * @throws ResourcePlannerException
     */
    public function produceGoods(Collection $orderLines): array
    {
        $orderLinesByFinishedGood = $this->sortOrderLinesByFinishedGood($orderLines);

        foreach (array_keys($orderLinesByFinishedGood) as $finishedGoodName)
        {
            $recipeName = config('resource-planning.recipes.'.$finishedGoodName);

            if ($recipeName === null)
            {
                throw new ResourcePlannerException("Product groep nog niet geconfigureerd");
            } else {
                $recipe = new $recipeName();
            }

            $producedGoods = $recipe->produce($orderLinesByFinishedGood[$finishedGoodName]);
            $this->producedGoods = array_merge($this->producedGoods, $producedGoods);
        }

        $this->failedGoods = [];

        return $this->producedGoods;
    }

    /**
     * @return array
     */
    public function producedGoods(): array
    {
        return $this->producedGoods;
    }

    public function failedGoods(): array
    {
        return $this->failedGoods;
    }

    private function sortOrderLinesByFinishedGood(Collection $orderLines): array
    {
        $orderLinesByFinishedGood = [];

        foreach ($orderLines as $orderLine)
        {
            $finishedGoodName = $orderLine->finishedGood()->name();
            $orderLinesByFinishedGood[$finishedGoodName][] = $orderLine;
        }

        return $orderLinesByFinishedGood;
    }

}
