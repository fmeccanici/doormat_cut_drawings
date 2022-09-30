<?php

namespace App\ResourcePlanning\Presentation\Console\Commands;

use App\ResourcePlanning\Application\ProduceGoodsUseCase\ProduceGoodsUseCase;
use App\ResourcePlanning\Application\ProduceGoodsUseCase\ProduceGoodsUseCaseInput;
use App\ResourcePlanning\Infrastructure\Importers\ExcelOrderImporter;
use Illuminate\Console\Command;

class ProduceGoodsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resource-planning:produce-goods-from-order-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produces goods from sales order';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filePath = getcwd()."/app/ResourcePlanning/Presentation/Console/Commands/";
        $fileName = "orderlist_5.xlsm";
        $fileContent = file_get_contents($filePath.$fileName);

        $startTime = microtime(true);

        $salesOrderImporter = new ExcelOrderImporter();
        $salesOrder = $salesOrderImporter->convertToSalesOrder($fileContent, $fileName);

        $failedSalesOrderLines = $salesOrderImporter->getFailedSalesOrderLines();

        $produceGoodsUseCase = new ProduceGoodsUseCase();
        $produceGoodsUseCaseInput = new ProduceGoodsUseCaseInput($salesOrder);
        $produceGoodsUseCaseResult = $produceGoodsUseCase->execute($produceGoodsUseCaseInput);

        $stopTime = microtime(true);

        $executionTime = $stopTime - $startTime;

        var_dump(sizeof($salesOrder->getSalesOrderLines())." OrderLines, Time: ".$executionTime);
        dd($failedSalesOrderLines);
    }
}
