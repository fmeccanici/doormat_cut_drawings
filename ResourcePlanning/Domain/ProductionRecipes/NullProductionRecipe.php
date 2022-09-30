<?php


namespace App\ResourcePlanning\Domain\ProductionRecipes;


class NullProductionRecipe extends ProductionRecipe
{

    function produce(array $orderLines): array
    {
        return [];
    }
}
