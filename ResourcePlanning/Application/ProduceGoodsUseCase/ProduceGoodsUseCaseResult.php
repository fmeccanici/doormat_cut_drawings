<?php


namespace App\ResourcePlanning\Application\ProduceGoodsUseCase;

use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;

final class ProduceGoodsUseCaseResult
{
    /**
     * @var FinishedGood[]
     */
    public array $producedGoods;

    /**
     * @var FinishedGood[]
     */
    public array $failedGoods;
}
