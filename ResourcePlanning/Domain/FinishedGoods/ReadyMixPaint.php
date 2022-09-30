<?php


namespace App\ResourcePlanning\Domain\FinishedGoods;


class ReadyMixPaint extends FinishedGood
{

    public function __construct(string $productCode, ?string $width = null, ?string $length  = null, ?string $height  = null, ?string $material  = null, ?string $brand  = null)
    {
        parent::__construct($productCode, $width, $length, $height, $material, $brand);
    }

    public function name(): string
    {
        return "ready-mix-paint";
    }

}
