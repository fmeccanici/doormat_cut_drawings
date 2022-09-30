<?php


namespace App\AI\Packing\Domain;

// TODO: Make separate geometry package: shared kernel for packing and resource planning and possibly other subdomains

class Position
{

    public float $x;
    public float $y;
    private int $decimalsAfterComma;

    public function __construct(float $x, float $y, int $decimalsAfterComma = 3)
    {
        $this->decimalsAfterComma = $decimalsAfterComma;
        $this->setX($x);
        $this->setY($y);
    }

    public function norm()
    {
        return sqrt(pow($this->x, 2) + pow($this->y, 2));
    }

    public function add(Position $other): Position
    {
        return new Position($this->x + $other->x, $this->y + $other->y);
    }

    public function subtract(Position $other): Position
    {
        return new Position($this->x - $other->x, $this->y - $other->y);
    }

    public function equals(Position $other): bool
    {
        if ($this->x == $other->x && $this->y == $other->y)
        {
            return true;
        }

        return false;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setX(float $x)
    {
        $this->x = round($x, $this->decimalsAfterComma);
    }

    public function setY(float $y)
    {
        $this->y = round($y, $this->decimalsAfterComma);
    }
}
