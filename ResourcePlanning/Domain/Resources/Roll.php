<?php


namespace App\ResourcePlanning\Domain\Resources;


class Roll implements Resource
{
    private string $id;
    private float $width;
    private ?float $length;
    private string $productCode;
    private string $material;

    public function __construct(string $id, float $width, ?float $length, string $productCode, string $material)
    {
        $this->id = $id;
        $this->width = $width;
        $this->length = $length;
        $this->productCode = $productCode;
        $this->material = $material;
    }

    public function material(): string
    {
        return $this->material;
    }

    public function id(): string
    {
        return $this->id;
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
        return 1;
    }

    public function productCode(): string
    {
        return $this->productCode;
    }

    public function asString(): string
    {
        return $this->width()."_".$this->length()."_".$this->productCode()."_".$this->material();
    }

    public static function fromString(string $string): Roll
    {
        $explodedString = explode("_", $string);
        $width = $explodedString[0];
        $length = $explodedString[1];
        $productCode = $explodedString[2];
        $cutMaterial = $explodedString[3];

        return new Roll(uniqid(), $width, $length, $productCode, $cutMaterial);

    }

    public function name(): string
    {
        return $this->material;
    }
}

