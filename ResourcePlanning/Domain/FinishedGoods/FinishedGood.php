<?php


namespace App\ResourcePlanning\Domain\FinishedGoods;


use App\SharedKernel\CleanArchitecture\ValueObject;

abstract class FinishedGood extends ValueObject
{
    protected string $productCode;
    protected ?float $width;
    protected ?float $length;
    protected ?float $height;
    protected ?string $material;
    protected ?string $brand;

    /**
     * FinishedGood constructor.
     * @param string $productCode
     * @param float|null $width
     * @param float|null $length
     * @param float|null $height
     * @param string|null $material
     * @param string|null $brand
     */
    public function __construct(string $productCode, ?float $width, ?float $length, ?float $height, ?string $material, ?string $brand)
    {
        $this->width = $width;
        $this->length = $length;
        $this->height = $height;
        $this->material = $material;
        $this->brand = $brand;
        $this->productCode = $productCode;
    }

    public function width(): ?float
    {
        return $this->width;
    }

    public function length(): ?float
    {
        return $this->length;
    }

    public function height(): ?float
    {
        return $this->height;
    }

    public function material(): ?string
    {
        return $this->material;
    }

    public function brand(): ?string
    {
        return $this->brand;
    }

    public function productCode(): string
    {
        return $this->productCode;
    }

    abstract function name(): string;
}
