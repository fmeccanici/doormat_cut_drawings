<?php


namespace App\ResourcePlanning\Application\ProduceGoodsUseCase;


interface ProduceGoodsUseCaseInterface
{
    /**
     * @param ProduceGoodsUseCaseInput $input
     * @return ProduceGoodsUseCaseResult
     */
    public function execute(ProduceGoodsUseCaseInput $input): ProduceGoodsUseCaseResult;
}
