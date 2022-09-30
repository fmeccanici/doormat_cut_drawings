<?php


namespace App\ResourcePlanning\Domain\Resources;


class Coupage implements Resource
{
    private string $material;
    private float $length;
    private float $width;

    public function __construct(string $material, float $length, float $width)
    {
        $this->material = $material;
        $this->length = $length;
        $this->width = $width;
    }

    public function name(): string
    {
        return "coupage";
    }

    public function width(): float
    {
        return $this->width;
    }

    public function length(): float
    {
        return $this->length;
    }

    public function height(): float
    {
        return 0;
    }

    public function productCode(): ?string
    {
        return null;
    }
}
