<?php

use App\ResourcePlanning\Presentation\Http\Api\ResourcePlanningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/produce-goods', [ResourcePlanningController::class, 'executeProduceGoodsUseCase'])->name('produce-goods');
Route::post('/get-resources-for-product', [ResourcePlanningController::class, 'getResourcesForProduct'])->name('get-resources-for-product');
