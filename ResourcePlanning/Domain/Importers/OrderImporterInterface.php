<?php


namespace App\ResourcePlanning\Domain\Importers;


use App\ResourcePlanning\Domain\Orders\Order;
use App\ResourcePlanning\Infrastructure\Importers\InvalidExcelTemplateException;

interface OrderImporterInterface
{
    /**
     * @param string $fileContent
     * @param string $fileName
     * @return Order
     * @throws InvalidExcelTemplateException
     */
    public function convertToOrder(string $fileContent, string $fileName): Order;
}
