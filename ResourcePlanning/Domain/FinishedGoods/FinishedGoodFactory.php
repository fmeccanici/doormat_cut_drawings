<?php


namespace App\ResourcePlanning\Domain\FinishedGoods;


class FinishedGoodFactory
{
    public static function readyMixPaint(string $productCode): ReadyMixPaint
    {
        return new ReadyMixPaint($productCode);
    }

    public static function mixPaint(string $productCode): MixPaint
    {
        return new MixPaint($productCode);
    }

    public static function doormat(string $productCode, ?float $width, ?float $length, ?float $height, ?string $material, ?string $brand): Doormat
    {
        return new Doormat($productCode, $width, $length, $height, $material, $brand);
    }

    public static function rails(string $productCode, float $length, ?string $material, ?string $brand): SawedRails
    {
        $width = 0;
        $height = 0;

        return new SawedRails($productCode, $width, $length, $height, $material, $brand);
    }

    public static function create(int|string $productId, ?string $productGroup, ?float $length = null): ?FinishedGood
    {
        $className = config('resource-planning.finished-goods.'.$productGroup);

        if ($className === null)
        {
            return new NullFinishedGood($productId, null, null, null, null, null);
        }

        return new $className($productId, null, $length, null, null, null);
    }

}
