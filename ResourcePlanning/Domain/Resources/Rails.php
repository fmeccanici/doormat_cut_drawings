<?php

namespace App\ResourcePlanning\Domain\Resources;

class Rails implements Resource
{
    protected string $id;
    protected float $width;
    protected ?float $length;
    protected string $productCode;
    protected string $material;

    public function __construct(string $id, float $length, string $productCode, string $material)
    {
        $this->id = $id;
        $this->length = $length;
        $this->productCode = $productCode;
        $this->material = $material;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function width(): float
    {
        return 0;
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
        return $this->productCode;
    }

    public function name(): string
    {
        return 'rails';
    }
}
