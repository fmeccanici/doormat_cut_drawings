<?php


namespace App\ResourcePlanning\Domain\Orders;


use Illuminate\Support\Collection;

class Order
{
    protected Collection $orderLines;

    public function __construct(Collection $orderLines)
    {
        $this->orderLines = $orderLines;
    }

    public function add(OrderLine $orderLine)
    {
        $this->orderLines->push($orderLine);
    }

    public function orderLines(): Collection
    {
        return $this->orderLines;
    }

}
