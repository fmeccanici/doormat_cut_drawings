<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;

// for testing
use App\ResourcePlanning\Domain\Resources\Resource;

class DummyCutting implements CuttingInterface
{
    private Resource $cuttingMaterial;
    private array $goods;

    public function sendCutDrawing(CutDrawing $cutDrawing)
    {
        $this->goods = $cutDrawing->getToBeCutShapes();

        // save file on sharepoint
    }

    function place(Resource $stockResource)
    {
        // warehouse employee places resource on doormat
        // can be for example automated by robots in the future

        $this->cuttingMaterial = $stockResource;
    }


    function cut(CutDrawing $cutDrawing): array
    {
        return $this->goods;
    }
}
