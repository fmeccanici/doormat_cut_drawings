<?php


namespace App\ResourcePlanning\Infrastructure\Repositories;


use App\ResourcePlanning\Domain\FinishedGoods\FinishedGood;
use App\ResourcePlanning\Domain\FinishedGoods\FinishedGoodFactory;
use App\ResourcePlanning\Domain\Resources\Resource;
use App\ResourcePlanning\Domain\Resources\ResourceFactory;
use Illuminate\Support\Facades\DB;

class MySqlResourceRepository implements \App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function findAllByFinishedGood(FinishedGood $finishedGood): array
    {
        $productCode = $finishedGood->productCode();

        $resources = [];

        $mySqlResources = DB::connection("sitemanager")->table("view_product_or_product_option_external")
                        ->select(["*"])
                        ->where("productnumber_external", "=", $productCode)
                        ->get();

        foreach ($mySqlResources as $mySqlResource)
        {
            $resourceProductCode = $mySqlResource->productnumber_internal;

            $productGroupId = DB::connection("sitemanager")->table("fsm_website_product")
                ->select(["*"])
                ->where("productnumber_internal", "=", $resourceProductCode)
                ->first()
                ->fsm_website_product_group_id;

            $productGroup = DB::connection("sitemanager")->table("fsm_website_product_group_language")
                ->select(["*"])
                ->where("fsm_website_product_group_id", "=", $productGroupId)
                ->first()
                ->url_name;

            $resources[] = ResourceFactory::create($resourceProductCode, $productGroup);
        }


        return $resources;
    }

    /**
     * @inheritDoc
     */
    public function findOneById(string $id): ?Resource
    {
        // TODO: Implement find() method.
    }

    /**
     * @inheritDoc
     */
    public function addOne(Resource $resource): void
    {
        // TODO: Implement add() method.
    }
}
