<?php


namespace App\ResourcePlanning\Domain\Tasks;


use App\ResourcePlanning\Domain\Resources\Coupage;
use App\ResourcePlanning\Domain\Tasks\Cutting\CoupageCutDrawing;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingInterface;
use App\ResourcePlanning\Domain\Tasks\Cutting\ToBeCutRectangle;
use App\SharedKernel\Geometry\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProduceDoormatsFromCoupagesTask extends Task
{
    private CuttingInterface $cutting;

    public function __construct(CuttingInterface $cutting)
    {
        $this->cutting = $cutting;
    }


    function execute(array $params): array
    {
        $producedGoods = [];

        foreach ($params as $orderLine)
        {
            $coupage = new Coupage($orderLine->finishedGood()->material(), $orderLine->finishedGood()->width(), $orderLine->finishedGood()->length());

            Log::channel('stacker')->info('Processing Coupage ordernumber ' . $orderLine->orderNumber());

            $toBeCutShape = new ToBeCutRectangle($orderLine->orderNumber(), $orderLine->finishedGood()->width(), $orderLine->finishedGood()->length(), $orderLine->customer(), location: $orderLine->location(), registrationNumber: $orderLine->registrationNumber());
            $toBeCutShape->setCutPosition(new Position($orderLine->finishedGood()->width() / 2, $orderLine->finishedGood()->length() / 2));

            if ($toBeCutShape->width() > $toBeCutShape->length())
            {
                $toBeCutShape->rotate(90);
            }

            $fileName = Carbon::today()->format("h") . " uur ".$orderLine->finishedGood()->productCode() . ' coupage ' . $orderLine->customer() . ' ' . $orderLine->orderNumber() . ' ' . uniqid();
            $cutDrawing = new CoupageCutDrawing(array($toBeCutShape), $fileName, $orderLine->finishedGood()->width(), $orderLine->finishedGood()->length());
            $cutDrawing->setCutMaterial($orderLine->finishedGood()->material());

            $this->cutting->sendCutDrawing($cutDrawing);
            $this->cutting->place($coupage);
            $doormat = $this->cutting->cut($cutDrawing)[0];
            $producedGoods[] = $doormat;

        }

        return $producedGoods;
    }
}
