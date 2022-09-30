<?php


namespace App\ResourcePlanning\Domain\ProductionRecipes;

use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\Orders\OrderLine;

abstract class ProductionRecipe
{
    /**
     * @param OrderLine[] $orderLines
     * @return FinishedGood[]
     */
    abstract public function produce(array $orderLines): array;
}
