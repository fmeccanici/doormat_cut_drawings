<?php


namespace App\ResourcePlanning\Infrastructure\Providers;


use App\Providers\ServiceProvider;
use App\ResourcePlanning\Domain\Exporters\CutDrawingExporterInterface;
use App\ResourcePlanning\Domain\Exporters\CutDrawingZccExporter;
use App\ResourcePlanning\Domain\Importers\OrderImporterInterface;
use App\ResourcePlanning\Domain\Repositories\CutDrawingRepositoryInterface;
use App\ResourcePlanning\Domain\Repositories\ResourceRepositoryInterface;
use App\ResourcePlanning\Domain\Services\InventoryServiceInterface;
use App\ResourcePlanning\Domain\Services\PackingServiceInterface;
use App\ResourcePlanning\Domain\Services\SawDrawingsExporterServiceInterface;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingInterface;
use App\ResourcePlanning\Domain\Tasks\Cutting\CuttingMachine;
use App\ResourcePlanning\Domain\Tasks\Packing\Packing;
use App\ResourcePlanning\Domain\Tasks\Packing\PackingInterface;
use App\ResourcePlanning\Domain\Tasks\Packing\RailsPackingService;
use App\ResourcePlanning\Infrastructure\Importers\ExcelOrderImporter;
use App\ResourcePlanning\Infrastructure\Persistence\Picqer\Repositories\PicqerResourceRepository;
use App\ResourcePlanning\Infrastructure\Repositories\SharepointCutDrawingRepository;
use App\ResourcePlanning\Infrastructure\Services\InventoryService;
use App\ResourcePlanning\Infrastructure\Services\PdfSawDrawingsExporterService;
use App\ResourcePlanning\Presentation\Console\Commands\ProduceGoodsCommand;
use Illuminate\Support\Facades\Route;

class ResourcePlanningServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();

        $this->registerResourcePlanningRoutes();

        $this->app->bind(CutDrawingExporterInterface::class,
                        CutDrawingZccExporter::class);

        $this->app->bind(SawDrawingsExporterServiceInterface::class,
            PdfSawDrawingsExporterService::class);


        $this->app->bind(CuttingInterface::class, CuttingMachine::class);
        $this->app->bind(PackingInterface::class, Packing::class);
        $this->app->bind(PackingServiceInterface::class, RailsPackingService::class);

        $this->app->bind(ResourceRepositoryInterface::class, PicqerResourceRepository::class);
        $this->app->bind(CutDrawingRepositoryInterface::class, SharepointCutDrawingRepository::class);
        $this->app->bind(OrderImporterInterface::class, ExcelOrderImporter::class);
        $this->app->bind(InventoryServiceInterface::class, InventoryService::class);

        $this->commands([
            ProduceGoodsCommand::class
        ]);
    }

    /**
     * Registers the resourceplanning routes
     */
    protected function registerResourcePlanningRoutes(): void
    {
        Route::prefix('resource-planning')
            ->middleware('web')
            ->namespace('App\\ResourcePlanner\\Presentation\\Http')
            ->group(__DIR__ . '/../../Presentation/Http/Routes/web.php');

        Route::prefix('api/resource-planning')
            ->middleware('auth:api')
            ->namespace('App\\ResourcePlanner\\Presentation\\Http')
            ->group(__DIR__ . '/../../Presentation/Http/Routes/api.php');
    }

}
