<?php


namespace App\ResourcePlanning\Domain\Orders;


use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\Resources\Resource;
use App\SharedKernel\CleanArchitecture\Entity;

class OrderLine extends Entity
{
    protected int $quantity;
    protected FinishedGood $finishedGood;
    private ?Resource $resource;
    private string $customer;
    private string $orderNumber;
    private string $orderDate;
    private ?string $description;
    private ?string $type;
    protected ?string $pickingContainer;
    protected ?string $registrationNumber;
    protected ?string $location;

    /**
     * OrderLine constructor.
     * @param int $quantity
     * @param FinishedGood $finishedGood
     * @param Resource|null $resource
     * @param string $customer
     * @param string $orderNumber
     * @param string $orderDate
     * @param string|null $description
     * @param string|null $type
     * @param string|null $pickingContainer
     * @param string|null $location
     * @param string|null $registrationNumber
     */
    public function __construct(int $quantity, FinishedGood $finishedGood, ?Resource $resource, string $customer, string $orderNumber, string $orderDate, ?string $description, ?string $type, ?string $pickingContainer = null, ?string $location = null, ?string $registrationNumber = null)
    {
        $this->quantity = $quantity;
        $this->finishedGood = $finishedGood;
        $this->resource = $resource;
        $this->customer = $customer;
        $this->orderNumber = $orderNumber;
        $this->orderDate = $orderDate;
        $this->description = $description;
        $this->type = $type;
        $this->pickingContainer = $pickingContainer;
        $this->location = $location;
        $this->registrationNumber = $registrationNumber;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function changeOrderNumber(string $orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function customer(): string
    {
        return $this->customer;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function finishedGood(): FinishedGood
    {
        return $this->finishedGood;
    }

    /**
     * @return Resource
     */
    public function resource(): Resource
    {
        return $this->resource;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function orderDate(): string
    {
        return $this->orderDate;
    }

    public function pickingContainer(): ?string
    {
        return $this->pickingContainer;
    }

    public function location(): ?string
    {
        return $this->location;
    }

    public function registrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function toString(): string
    {
        $string = "";
        $string = $string."Ordernumber: ".$this->orderNumber()."\n";
        $string = $string."Customer: ".$this->customer()."\n";
        $string = $string."Length: ".$this->finishedGood()->length()."\n";
        $string = $string."Width: ".$this->finishedGood()->width()."\n";
        return $string;
    }

    protected function cascadeSetIdentity(int|string $id): void
    {
        // TODO: Implement cascadeSetIdentity() method.
    }
}
