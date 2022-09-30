<?php


namespace App\ResourcePlanning\Domain\Tasks\Cutting;


use App\SharedKernel\Geometry\Line;
use App\SharedKernel\Geometry\Position;

class ToBeCutRectangle
{

    private int $decimalsAfterComma;
    private string $id;
    private ?Position $cutPosition;
    private float $length;
    private float $width;
    private string $customer;
    protected ?string $registrationNumber;
    protected ?string $location;

    public function __construct(string $id,
                                float $width,
                                float $length,
                                string $customer,
                                ?Position $cutPosition = null,
                                int $decimalsAfterComma = 3,
                                ?string $location = null,
                                ?string $registrationNumber = null)
    {
        $this->id = $id;
        $this->setDecimalsAfterComma($decimalsAfterComma);
        $this->setWidth($width);
        $this->setLength($length);
        $this->setCustomer($customer);
        $this->setCutPosition($cutPosition);
        $this->location = $location;
        $this->registrationNumber = $registrationNumber;
    }

    public function convertToLines(): array
    {
        $topLeftToTopRight = new Line($this->topLeft(), $this->topRight());
        $bottomLeftToTopLeft = new Line($this->bottomLeft(), $this->topLeft());
        $bottomLeftToBottomRight = new Line($this->bottomLeft(), $this->bottomRight());
        $bottomRightToTopRight = new Line($this->bottomRight(), $this->topRight());

        return array($topLeftToTopRight, $bottomLeftToTopLeft, $bottomLeftToBottomRight, $bottomRightToTopRight);
    }

    public function customer(): string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer)
    {
        $this->customer = $customer;
    }

    public function setCutPosition(?Position $cutPosition)
    {
        $this->cutPosition = $cutPosition;
    }

    public function cutPosition(): ?Position
    {
        return $this->cutPosition;
    }

    public function rotate(float $degrees): ToBeCutRectangle
    {
        if ((int) $degrees == 90)
        {
            $tempLength = $this->length;
            $this->length = $this->width;
            $this->width = $tempLength;

            if ($this->cutPosition != null)
            {
                $this->cutPosition = new Position($this->cutPosition()->y(), $this->cutPosition()->x());
            }
        }
        // TODO: Add mathematics to rotate the rectangle
        else
        {

        }

        return $this;
    }

    public function topLeft(): Position
    {
        return $this->cutPosition->add(new Position(-$this->width / 2, $this->length / 2));
    }

    public function topRight(): Position
    {
        return $this->cutPosition->add(new Position($this->width / 2, $this->length / 2));
    }

    public function bottomRight(): Position
    {
        return $this->cutPosition->add(new Position($this->width / 2, -$this->length / 2));
    }

    public function bottomLeft(): Position
    {
        return $this->cutPosition->add(new Position(-$this->width / 2, -$this->length / 2));
    }

    function id(): string
    {
        return $this->id;
    }

    function length(): float
    {
        return $this->length;
    }

    function width(): float
    {
        return $this->width;
    }

    function setLength(float $length)
    {
        $this->length = round($length, $this->decimalsAfterComma);
    }

    function setWidth(float $width)
    {
        $this->width = round($width, $this->decimalsAfterComma);
    }

    function setId(string $id)
    {
        $this->id = $id;
    }

    function setSurfaceId(string $surfaceId)
    {
        $this->surfaceId = $surfaceId;
    }

    function setDecimalsAfterComma(int $decimalsAfterComma)
    {
        $this->decimalsAfterComma = $decimalsAfterComma;
    }

    function getDecimalsAfterComma(): int
    {
        return $this->decimalsAfterComma;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function registrationNumber(): ?string
    {
        return $this->registrationNumber;
    }
}
