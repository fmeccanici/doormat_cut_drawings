<?php


namespace App\AI\Packing\Domain\Repositories;


interface SurfacesRepositoryInterface
{
    /**
     * @param StackingArea $stackingArea
     * @return bool Successfully added
     */
    public function addStackingArea(StackingArea $stackingArea): bool;

    public function getNonEmptyStackingAreas(): ?array;
    public function getAllStackingAreas(): ?array;
}
