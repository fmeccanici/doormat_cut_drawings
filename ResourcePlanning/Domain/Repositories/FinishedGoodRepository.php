<?php


namespace App\ResourcePlanning\Domain\Repositories;


use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;

interface FinishedGoodRepository
{
    /**
     * @param string $productCode
     * @return FinishedGood
     */
    public function findByProductCode(string $productCode): FinishedGood;
}
