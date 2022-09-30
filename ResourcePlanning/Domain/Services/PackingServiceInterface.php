<?php

namespace App\ResourcePlanning\Domain\Services;

use App\AI\Packing\Domain\PackingAlgorithm\AbstractPackingAlgorithm;
use App\ResourcePlanning\Domain\FinishedGoods\SawedRails;
use App\ResourcePlanning\Domain\Resources\Rails;
use Illuminate\Support\Collection;

interface PackingServiceInterface
{
    /**
     * @param Collection<SawedRails> $rails
     * @param Rails $rail
     * @return Collection To be sawed lines
     */
    public function packRailsOnRail(Collection $rails, Rails $rail): Collection;

    /**
     * @param Collection<SawedRails> $rails
     * @param Rails $rail
     * @return int
     */
    public function getAmountOfRailsNeeded(Collection $rails, Rails $rail): int;

    public function getPackingAlgorithm(): AbstractPackingAlgorithm;
}
