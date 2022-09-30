<?php


namespace App\ResourcePlanning\Domain\Resources;


class NullResource implements Resource
{
    private string $productCode;

    public function __construct(string $productCode)
    {
        $this->productCode = $productCode;
    }

    public function width(): float
    {
        // TODO: Implement width() method.
    }

    public function length(): float
    {
        // TODO: Implement length() method.
    }

    public function height(): float
    {
        // TODO: Implement height() method.
    }

    public function productCode(): ?string
    {
        return $this->productCode;
    }
}
