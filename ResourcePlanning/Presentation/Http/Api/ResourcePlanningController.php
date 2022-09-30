<?php


namespace App\ResourcePlanning\Presentation\Http\Api;


use App\Jobs\ProduceGoodsJob;
use App\ResourcePlanning\Application\GetResourcesForProduct\GetResourcesForProduct;
use App\ResourcePlanning\Application\GetResourcesForProduct\GetResourcesForProductInput;
use App\ResourcePlanning\Domain\Importers\OrderImporterInterface;
use App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface;
use App\ResourcePlanning\Infrastructure\Importers\InvalidExcelTemplateException;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ResourcePlanningController
{
    private OrderImporterInterface $orderImporter;

    public function __construct()
    {
        $this->orderImporter = App::make(OrderImporterInterface::class);
    }

    public function executeProduceGoodsUseCase(Request $request)
    {
        $file = $request->file('orderlist');

        try {
            $order = $this->orderImporter->convertToOrder($file->getContent(),
                "deurmat_paklijst_" . Carbon::now()->format('Y-m-d\TH-i-s') . '.' . $file->getClientOriginalExtension());

            // TODO: Think about how to return the goods that were succesfully produced and that failed
            // Since job runs async, it is not trivial
            ProduceGoodsJob::dispatch($order);

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => null,
                'data' => null
            ]);

        } catch (InvalidExcelTemplateException $e) {

            // Report to the log file
            report($e);

            // We return status 400 if the error is caused by the user
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);

        } catch (\Exception $e){

            // Report to the log file
            report($e);

            // We return status 500 if the error is caused by the server
            return response([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getResourcesForProduct(Request $request, ResourceRepositoryInterface $resourceRepository)
    {

        try {
            $productCode = $request->input('product')["product_code"];
            $productGroup = $request->input('product')["product_group"];

            $useCase = new GetResourcesForProduct($resourceRepository);
            $input = new GetResourcesForProductInput([
                "product" => [
                    "product_code" => $productCode,
                    "product_group" => $productGroup
                ]
            ]);

            $result = $useCase->execute($input);
            $response["meta"]["created_at"] = time();
            $response["payload"]["resources"] = $result->resources()->toArray();

        } catch (\Exception $e) {

            $response["meta"]["created_at"] = time();
            $response["error"]["code"] = $e->getCode();
            $response["error"]["message"] = $e->getMessage();

            Log::error($e->getMessage().$e->getTraceAsString());
        }

        return $response;

    }
}
