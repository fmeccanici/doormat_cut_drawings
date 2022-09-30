<?php

namespace App\ResourcePlanning\Domain\Tasks\Sawing;

use App\ResourcePlanning\Domain\Resources\Rails;
use Illuminate\Support\Collection;

class SawDrawing
{
    protected Collection $toBeSawedLines;
    protected string $filePath;
    protected string $id;
    protected Rails $rails;

    public function __construct(string $id, Collection $toBeSawedLines, Rails $rails)
    {
        $this->id = $id;
        $this->toBeSawedLines = $toBeSawedLines;
        $this->rails = $rails;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function toBeSawedLines(): Collection
    {
        return $this->toBeSawedLines;
    }

    /**
     * Saw loss in percentage
     * @return float
     */
    public function sawLoss(): float
    {
        $maxRailLength = $this->rails->length();
        $sawedRailLength = $this->toBeSawedLines->sum->length();
        $sawLoss = ($maxRailLength - $sawedRailLength) / $maxRailLength;
        $sawLossInPercentages = $sawLoss * 100;
        return round($sawLossInPercentages, 2);
    }

}
