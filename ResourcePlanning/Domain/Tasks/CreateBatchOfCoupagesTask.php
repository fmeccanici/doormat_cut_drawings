<?php


namespace App\ResourcePlanning\Domain\Tasks;


use App\ResourcePlanning\Domain\Exceptions\InvalidCreateBatchOfCoupagesTaskOperationException;
use App\ResourcePlanning\Domain\Orders\OrderLine;
use App\ResourcePlanning\Domain\Resources\Coupage;

class CreateBatchOfCoupagesTask extends Task
{

    function execute(array $params): array
    {
        $result = [];

        foreach ($params as $param)
        {
            if (! $param instanceof OrderLine)
            {
                throw new InvalidCreateBatchOfCoupagesTaskOperationException("Input to task has to be an order line");
            }

            if ($param->resource() instanceof Coupage)
            {
                $result[] = $param;
            }

        }

        return $result;
    }
}
