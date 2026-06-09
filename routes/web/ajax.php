<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AjaxController;

//// Ajax requests
Route::get('/_ajax-stock', [AjaxController::class, 'getStockAjax'])->name('_ajax-stock'); // for the index page ajax
Route::get('/_ajax-stockCables', [AjaxController::class, 'getCablesAjax'])->name('_ajax-stockCables'); // for the index page ajax
Route::get('/_ajax-selectBoxes', [AjaxController::class, 'getSelectBoxes'])->name('_ajax-selectBoxes'); // for the index page ajax
Route::get('/_ajax-nearbystock', [AjaxController::class, 'getNearbyStockAjax'])->name('_ajax-nearbystock'); // for the container page nearby stock
//
Route::post('/_ajax-addProperty', [AjaxController::class, 'addProperty'])->name('_ajax-addProperty'); // for the new-properties blade page to add a new property (tag/manu./etc)
Route::post('/_ajax-loadProperty', [AjaxController::class, 'loadProperty'])->name('_ajax-loadProperty'); // for the new-properties blade page to get a list of properties (tag/manu./etc)
//
Route::post('/_ajax-favouriteStock', [AjaxController::class, 'favouriteStock'])->name('_ajax-favouriteStock'); // for adding/removing favourites in the stock page
//
Route::post('/_ajax-nearbyContainers', [AjaxController::class, 'getNearbyContainersAjax'])->name('_ajax-nearbyContainers'); // get a list of nearby containers
//
Route::post('/_ajax-getDiskInfo', [AjaxController::class, 'getDiskInfoAjax'])->name('_ajax-getDiskInfo'); // get disk info
//
Route::post('/_ajax-getMemoryInfo', [AjaxController::class, 'getMemoryInfoAjax'])->name('_ajax-getMemoryInfo'); // get memory info
//
Route::post('/_ajax-getCpuInfo', [AjaxController::class, 'getCpuInfoAjax'])->name('_ajax-getCpuInfo'); // get cpu info
////