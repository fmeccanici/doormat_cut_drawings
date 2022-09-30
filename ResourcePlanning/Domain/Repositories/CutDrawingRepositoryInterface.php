<?php


namespace App\ResourcePlanning\Domain\Repositories;


use App\ResourcePlanning\Domain\Tasks\Cutting\CutDrawing;

interface CutDrawingRepositoryInterface
{
    public function add(CutDrawing $cutDrawing): void;
}
