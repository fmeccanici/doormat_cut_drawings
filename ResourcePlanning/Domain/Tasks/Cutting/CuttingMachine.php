<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;


use App\ResourcePlanning\Domain\Repositories\CutDrawingRepositoryInterface;
use App\ResourcePlanning\Domain\Resources\Resource;

class CuttingMachine implements CuttingInterface
{
    private CutDrawingRepositoryInterface $cutDrawingRepository;

    public function __construct(CutDrawingRepositoryInterface $cutDrawingRepository)
    {
        $this->cutDrawingRepository = $cutDrawingRepository;
    }

    public function sendCutDrawing(CutDrawing $cutDrawing)
    {
        $this->cutDrawingRepository->add($cutDrawing);
    }

    function place(Resource $stockResource)
    {
        // warehouse employee places resource on doormat
        // can be for example automated by robots in the future
    }


    function cut(CutDrawing $cutDrawing): array
    {
        return $cutDrawing->getToBeCutShapes();
    }

}
