<?php


namespace App\ResourcePlanning\Domain\Tasks;


abstract class Task
{
    abstract function execute(array $params): array;
}
