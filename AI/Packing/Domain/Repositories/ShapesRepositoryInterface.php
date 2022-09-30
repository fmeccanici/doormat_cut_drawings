<?php


namespace App\AI\Packing\Domain\Repositories;


interface ShapesRepositoryInterface
{
    /**
     * @param Shape $stackable
     * @return bool Successfully added
     */
    public function addStackable(Shape $stackable): bool;
    public function getUnstackedStackables(): ?array;
}
