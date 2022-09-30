<?php


namespace App\ResourcePlanning\Application\ProduceGoodsUseCase;

use Illuminate\Support\Collection;

final class ProduceGoodsUseCaseInput
{
    protected Collection $orderLines;

    public function __construct(Collection $orderLines)
    {
        $this->orderLines = $orderLines;
    }

    public function orderLines(): Collection
    {
        return $this->orderLines;
    }
}
