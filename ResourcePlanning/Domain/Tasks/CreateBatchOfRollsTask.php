<?php


namespace App\ResourcePlanning\Domain\Tasks;


use App\ResourcePlanning\Domain\Orders\OrderLine;
use App\ResourcePlanning\Domain\Resources\Roll;

class CreateBatchOfRollsTask extends Task
{

    function execute(array $params): array
    {
        $orderLinesBatchDoormatsPerRoll = $this->getOrderLinesBatchDoormatsPerRoll($params);

        return $orderLinesBatchDoormatsPerRoll;
    }

    /**
     * @param OrderLine[] $orderLines
     * @return array
     */
    private function getOrderLinesBatchDoormatsPerRoll(array $orderLines): array
    {
        $salesOrderLinesBatchDoormatsPerRoll = [];

        foreach ($orderLines as $orderLine)
        {
            if ($orderLine->resource() instanceof Roll)
            {
                $conveyorBeltEnabled = config('resource-planning.services.zund.conveyor_belt.used');
                $divideOnRollLength = config('resource-planning.services.zund.conveyor_belt.divide_on_roll_length');

                $length = $conveyorBeltEnabled ? $this->determineRollLength($orderLine) : $divideOnRollLength;

                if ($orderLine->finishedGood()->material() == "Kokosmat")
                {
                    $rollMaterial = "Kokos";
                } else {
                    $rollMaterial = $orderLine->finishedGood()->material();
                }

                $neededRoll = new Roll("1", $orderLine->resource()->width(), $length, $orderLine->finishedGood()->productCode(), $rollMaterial);
                $salesOrderLinesBatchDoormatsPerRoll[$neededRoll->asString()][] = $orderLine;
            }
        }

        return $salesOrderLinesBatchDoormatsPerRoll;
    }

    private function determineRollLength(OrderLine $orderLine): int
    {
        // TODO: Refactor to be part of Inventory, ask Inventory which rolls are available for a certain material
        if ($orderLine->finishedGood()->brand() == "Kokos" && $orderLine->resource()->width() == 100)
        {
            $length = 1225;
        } else if ($orderLine->finishedGood()->brand() == "Kokos" && $orderLine->resource()->width() == 220)
        {
            $length = 600;
        } else if ($orderLine->finishedGood()->brand() == "Ambiant" && explode(" ", $orderLine->finishedGood()->material()[1] != "Lobby") && $orderLine->resource()->width() == 123)
        {
            $length = 975;
        } else if ($orderLine->finishedGood()->brand() == "Ambiant" && explode(" ", $orderLine->finishedGood()->material()[1] != "Lobby") && $orderLine->resource()->width() == 200)
        {
            $length = 600;
        } else if ($orderLine->finishedGood()->brand() == "Ambiant" && explode(" ", $orderLine->finishedGood()->material())[1] == "Lobby" && $orderLine->resource()->width() == 123)
        {
            $length = 975;
        } else if ($orderLine->finishedGood()->brand() == "Ambiant" && explode(" ", $orderLine->finishedGood()->material())[1] == "Lobby" && $orderLine->resource()->width() == 200)
        {
            $length = 600;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Forbo" && $orderLine->resource()->width() == 98)
        {
            $length = 975;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Forbo" && $orderLine->resource()->width() == 148)
        {
            $length = 975;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Forbo" && $orderLine->resource()->width() == 198)
        {
            $length = 600;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Zeno" && $orderLine->resource()->width() == 98)
        {
            $length = 975;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Zeno" && $orderLine->resource()->width() == 198)
        {
            $length = 600;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Ondervloer 5mm" && $orderLine->resource()->width() == 135)
        {
            $length = 700;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Ondervloer 3,6mm" && $orderLine->resource()->width() == 130)
        {
            $length = 1100;
        } else if (explode(" ", $orderLine->finishedGood()->brand())[0] == "Squid" && $orderLine->resource()->width() == 137)
        {
            $length = 1500;
        } else {
            $length = 980;
        }

        return $length;
    }
}
