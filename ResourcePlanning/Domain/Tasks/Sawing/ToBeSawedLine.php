<?php

namespace App\ResourcePlanning\Domain\Tasks\Sawing;

use App\SharedKernel\Geometry\Position;

class ToBeSawedLine
{
    protected string $id;
    protected ?Position $sawPosition;
    protected float $length;
    protected ?string $location;

    public function __construct(string $id,
                                float $length,
                                ?Position $sawPosition = null,
                                ?string $location = null)
    {
        $this->id = $id;
        $this->length = $length;
        $this->sawPosition = $sawPosition;
        $this->location = $location;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function length(): float
    {
        return $this->length;
    }

    public function sawPosition(): ?Position
    {
        return $this->sawPosition;
    }

    public function changeLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function dash()
    {
        return $this->sawPosition()->x() + $this->length() / 2;
    }
}
