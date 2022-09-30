<?php


namespace App\ResourcePlanning\Domain\Resources;


class ResourceFactory
{
    public static function create(?string $productId, ?string $productGroup): ?Resource
    {
        $className = config('resource-planning.resources.'.$productGroup);

        if ($className === null)
        {
            return new NullResource($productId);
        }

        return new $className(null, null, null, null, null, $productId);
    }
}
