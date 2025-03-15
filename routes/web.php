<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\ContainersController;
use App\Http\Controllers\CablestockController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TransactionController;

use App\Http\Middleware\SecurityMiddleware;
use App\Http\Middleware\AddHeadData;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware([AddHeadData::class])->group(function () {
    Route::get('/dashboard', function () {  // auth default
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware([SecurityMiddleware::class])->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');           // auth default
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');     // auth default
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');  // auth default
            
            //// General routes
            // normal routes
            Route::get('/', [IndexController::class, 'index'])->name('index'); // home page
            Route::get('/cablestock', [CablestockController::class, 'index'])->name('cablestock'); // cablestock page
            Route::get('/assets', [AssetsController::class, 'index'])->name('assets'); // assets page
            Route::get('/containers', [ContainersController::class, 'index'])->name('containers'); // containers page
            Route::get('/stock/{stock_id}/{modify_type?}', [StockController::class, 'index'])
                ->where('stock_id', '[0-9]+') // Ensure stock_id is numeric
                ->name('stock');
            Route::get('/transactions/{stock_id?}', [TransactionController::class, 'index'])
                ->where('stock_id', '[0-9]+') // Ensure stock_id is numeric
                ->name('transactions');

            // optics routes - auth in progress

            // admin routes - auth in progress
            Route::get('/admin', [AdminController::class, 'index'])->name('admin'); // admin page

            //// Ajax requests
            Route::get('/_ajax-stock', [AjaxController::class, 'getStockAjax'])->name('_ajax-stock'); // for the index page ajax
            Route::get('/_ajax-stockCables', [AjaxController::class, 'getCablesAjax'])->name('_ajax-stockCables'); // for the index page ajax
            Route::get('/_ajax-selectBoxes', [AjaxController::class, 'getSelectBoxes'])->name('_ajax-selectBoxes'); // for the index page ajax
            Route::get('/_ajax-nearbystock', [AjaxController::class, 'getNearbyStockAjax'])->name('_ajax-nearbystock'); // for the container page nearby stock
            Route::post('/_ajax-addProperty', [AjaxController::class, 'addProperty'])->name('_ajax-addProperty'); // for the new-properties blade page to add a new property (tag/manu./etc)
            Route::post('/_ajax-loadProperty', [AjaxController::class, 'loadProperty'])->name('_ajax-loadProperty'); // for the new-properties blade page to get a list of properties (tag/manu./etc)

            //// Form requests
            //  Containers
            Route::post('/containers.addContainer', [ContainersController::class, 'addContainer'])->name('containers.addContainer'); // add new container
            Route::post('/containers.deleteContainer', [ContainersController::class, 'deleteContainer'])->name('containers.deleteContainer'); // add new container
            Route::post('/containers.editContainer', [ContainersController::class, 'editContainer'])->name('containers.editContainer'); // add new container
            Route::post('/containers.unlinkFromContainer', [ContainersController::class, 'unlinkFromContainer'])->name('containers.unlinkFromContainer'); // add new container
            Route::post('/containers.linkToContainer', [ContainersController::class, 'linkToContainer'])->name('containers.linkToContainer'); // add new container

            //

            ////
        });

        // no auth needed
        Route::get('/about', [AboutController::class, 'index'])->name('about'); // no auth needed

        Route::get('/test', [IndexController::class, 'test'])->name('test'); // Testing page

    });
});


require __DIR__.'/auth.php';
