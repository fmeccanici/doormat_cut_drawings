<?php


namespace App\AI\Packing\Infrastructure\Repositories;

class EloquentShapesRepository implements \App\Stacking\Domain\Repositories\ShapesRepositoryInterface
{

    public function addStackable(Stackable $stackable): bool
    {
        try
        {
            $stackable->save();
            return true;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}
