<?php


namespace App\ResourcePlanning\Domain\FinishedGoods;


class NullFinishedGood extends FinishedGood
{

    function name(): string
    {
        return "unknown";
    }
}
