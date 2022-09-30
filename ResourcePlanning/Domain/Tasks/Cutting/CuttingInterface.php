<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;

use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\Resources\Resource;

interface CuttingInterface
{
    function sendCutDrawing(CutDrawing $cutDrawing);
    function place(Resource $stockResource);

    /**
     * @param CutDrawing $cutDrawing
     * @return FinishedGood[]
     */
    function cut(CutDrawing $cutDrawing): array;
}
