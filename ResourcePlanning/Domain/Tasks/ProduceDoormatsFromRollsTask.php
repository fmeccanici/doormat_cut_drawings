<?php


namespace App\ResourcePlanning\Domain\Tasks;


use App\ResourcePlanning\Domain\Resources\Roll;
use App\ResourcePlanning\Domain\Tasks\Cutting\BatchCutDrawing;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingInterface;
use App\ResourcePlanning\Domain\Tasks\Cutting\ToBeCutRectangle;
use App\ResourcePlanning\Domain\Tasks\Packing\PackingInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProduceDoormatsFromRollsTask extends Task
{
    private CuttingInterface $cutting;
    private PackingInterface $packing;

    public function __construct(CuttingInterface $cutting,
                                PackingInterface $packing)
    {
        $this->cutting = $cutting;
        $this->packing = $packing;
    }

    function execute(array $params): array
    {
        $producedGoods = [];

        foreach (array_keys($params) as $rollAsString)
        {
            $roll = Roll::fromString($rollAsString);
            $toBeProducedOrderLines = $params[$rollAsString];
            $toBeCutShapes = [];

            foreach ($toBeProducedOrderLines as $toBeProducedOrderLine)
            {
                $toBeCutShape = new ToBeCutRectangle($toBeProducedOrderLine->orderNumber(), $toBeProducedOrderLine->finishedGood()->width(),
                    $toBeProducedOrderLine->finishedGood()->length(), $toBeProducedOrderLine->customer());

                if ($toBeCutShape->length() > $roll->width() && $toBeCutShape->width() > $roll->width())
                {
                    $splitShapePart1 = clone $toBeCutShape;
                    $splitShapePart1->setLength($toBeCutShape->length() / 2);
                    $splitShapePart1->setCustomer($toBeCutShape->customer() . ' Deel 1');
                    $splitShapePart2 = clone $splitShapePart1;
                    $splitShapePart2->setCustomer($toBeCutShape->customer() . ' Deel 2');
                    $splitShapePart2->setId(uniqid());
                    $toBeCutShapes[] = $splitShapePart1;
                    $toBeCutShapes[] = $splitShapePart2;
                } else {
                    $toBeCutShapes[] = $toBeCutShape;
                }

                Log::channel('stacker')->info('Processing ordernumber in roll ' . $toBeProducedOrderLine->orderNumber());
            }

            $amountOfRollsNeeded = $this->packing->getAmountOfRollsNeeded($toBeCutShapes, $roll);

            for ($i = 0; $i < $amountOfRollsNeeded; $i++)
            {
                // TODO: Make ProductionRecipe Class for these steps
                $roll = new Roll(uniqid(), $roll->width(), $roll->length(), $roll->productCode(), $roll->material());

                $packedShapes = $this->packing->packToBeCutShapesOnRoll($toBeCutShapes, $roll);

                $this->packing->getToBeCutShapesThatDidNotFitOnRoll();
                $toBeCutShapesThatDidNotFitOnRoll = $this->packing->getToBeCutShapesThatDidNotFitOnRoll();

                $cutDrawing = new BatchCutDrawing($packedShapes, Carbon::today()->format("h")." uur ".$roll->productCode().' rolbreedte '.$roll->width()."cm"." rol id ".$roll->id(), $roll->width(), $roll->length());
                $cutDrawing->setCutMaterial($roll->material());

                $this->cutting->sendCutDrawing($cutDrawing);
                $this->cutting->place($roll);
                $doormats = $this->cutting->cut($cutDrawing);

                $producedGoods = array_merge($producedGoods, $doormats);
                $toBeCutShapes = $toBeCutShapesThatDidNotFitOnRoll;
            }
        }

        return $producedGoods;
    }
}
