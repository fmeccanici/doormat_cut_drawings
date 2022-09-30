<?php


namespace App\ResourcePlanning\Domain\Orders;


use App\ResourcePlanning\Domain\FinishedGoods\Doormat;
use App\ResourcePlanning\Domain\Resources\Coupage;

class OrderFactory
{
    public static function constant(): Order
    {
        $quantity = 1;
        $brand = "Ambiant";
        $material = "Ambiant Entrance";
        $productName = "Deurmat Ambiant Entrance 5017.0246";
        $width_1 = 39.8;
        $length_1 = 69.8;
        $orderNumber_1 = "120399611";
        $customer = "Mevr. Dewarrimont";
        $orderDate = "03-04-2021";
        $description = "Deurmat Ambiant Entrance 5017.0246 Rolbreedte 123 cm Breedte: 39,8cm, Lengte: 69,8 cm ";
        $type = "42141-388730";

        $finishedGood1 = new Doormat($productName, $width_1, $length_1, 0, $material, $brand);
        $resource1 = new Coupage("1", $length_1, $width_1);

        $orderLine_1 = new OrderLine($quantity, $finishedGood1, $resource1, $customer, $orderNumber_1, $orderDate, $description, $type);

        $orderNumber_2 = "120399612";
        $width_2 = 100;
        $length_2 = 100;

        $finishedGood2 = new Doormat($productName, $width_2, $length_2, 0, $material, $brand);
        $resource2 = new Coupage("1", $length_2, $width_2);

        $orderLine_2 = new OrderLine($quantity, $finishedGood2, $resource2, $customer, $orderNumber_2, $orderDate, $description, $type);

        $orderNumber_3 = "120399613";
        $width_3 = 50;
        $length_3 = 40;
        $finishedGood3 = new Doormat($productName, $width_3, $length_3, 0, $material, $brand);
        $resource3 = new Coupage("1", $length_3, $width_3);

        $orderLine_3 = new OrderLine($quantity, $finishedGood3, $resource3, $customer, $orderNumber_3, $orderDate, $description, $type);

        return new Order(collect(array($orderLine_1, $orderLine_2, $orderLine_3)));

    }
}
