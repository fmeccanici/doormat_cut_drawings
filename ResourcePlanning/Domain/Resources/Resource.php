<?php


namespace App\ResourcePlanning\Domain\Resources;


interface Resource
{
    public function width(): float;
    public function length(): float;
    public function height(): float;
    public function productCode(): ?string;
    public function name(): string;
}
