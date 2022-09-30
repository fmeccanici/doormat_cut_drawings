<?php


namespace App\AI\Packing\Infrastructure\Providers;

use App\Providers\ServiceProvider;

class PackingServiceProvider extends ServiceProvider
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
    public function boot()
    {
        parent::boot();

        $this->registerStackingRoutes();
    }

    /**
     * Registers the stacking routes
     */
    protected function registerStackingRoutes(): void
    {
        Route::prefix('stacking')
            ->middleware('web')
            ->namespace('App\\PackableShape\\Presentation\\Http')
            ->group(__DIR__ . '/../../Presentation/Http/Routes/web.php');

        Route::prefix('api/stacking')
            ->middleware('api')
            ->namespace('App\\PackableShape\\Presentation\\Http')
            ->group(__DIR__ . '/../../Presentation/Http/Routes/api.php');
    }

}
