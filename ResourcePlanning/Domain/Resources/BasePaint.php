<?php


namespace App\ResourcePlanning\Domain\Resources;


use Illuminate\Contracts\Support\Arrayable;

class BasePaint implements Resource, Arrayable
{
    private string $productCode;
    private string $name;

    public function __construct(string $productCode, string $name)
    {
        $this->productCode = $productCode;
        $this->name = $name;
    }

    public function width(): float
    {
        return 0.0;
    }

    public function length(): float
    {
        return 0.0;
    }

    public function height(): float
    {
        return 0.0;
    }

    public function productCode(): ?string
    {
        return $this->productCode;
    }

    public function toArray()
    {
        return [
            "product_group" => "paint",
            "product_code" => $this->productCode,
            "product_name" => $this->name
        ];
    }

    public function name(): string
    {
        return $this->name;
    }
}
