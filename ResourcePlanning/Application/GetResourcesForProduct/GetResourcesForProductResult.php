<?php


namespace App\ResourcePlanning\Application\GetResourcesForProduct;


use Illuminate\Support\Collection;

final class GetResourcesForProductResult
{
    private Collection $resources;

    /**
     * GetResourcesForProductResult constructor.
     * @param Collection $resources
     */
    public function __construct(Collection $resources)
    {
        $this->resources = $resources;
    }

    public function resources(): Collection
    {
        return $this->resources;
    }
}
