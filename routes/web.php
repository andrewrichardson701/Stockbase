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
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\OpticsController;
use App\Http\Controllers\ChangelogController;

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
            Route::get('/stock/{stock_id}/{modify_type?}/{add_new?}/{search?}', [StockController::class, 'index'])
                ->where('stock_id', '[0-9\-]+') // Ensure stock_id is numeric or -
                ->where('add_new', '[a-z\-]+') // allow text and -
                ->name('stock');
            Route::get('/transactions/{stock_id?}', [TransactionController::class, 'index'])
                ->where('stock_id', '[0-9]+') // Ensure stock_id is numeric
                ->name('transactions');
            Route::get('/favourites', [FavouritesController::class, 'index'])->name('favourites'); // favourites page
            Route::get('/tags', [TagController::class, 'index'])->name('tags'); // favourites page

            // optics routes - auth in progress
            Route::get('/optics', [OpticsController::class, 'index'])->name('optics'); // admin page

            // admin routes - auth in progress
            Route::get('/admin', [AdminController::class, 'index'])->name('admin'); // admin page
            Route::get('/changelog/{start_date?}/{end_date?}/{table?}/{user?}', [ChangelogController::class, 'index'])->name('changelog'); // admin page

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
            

            //// Form requests
            //  Containers
            Route::post('/containers.addContainer', [ContainersController::class, 'addContainer'])->name('containers.addContainer'); // add new container
            Route::post('/containers.deleteContainer', [ContainersController::class, 'deleteContainer'])->name('containers.deleteContainer'); // add new container
            Route::post('/containers.editContainer', [ContainersController::class, 'editContainer'])->name('containers.editContainer'); // add new container
            Route::post('/containers.unlinkFromContainer', [ContainersController::class, 'unlinkFromContainer'])->name('containers.unlinkFromContainer'); // add new container
            Route::post('/containers.linkToContainer', [ContainersController::class, 'linkToContainer'])->name('containers.linkToContainer'); // add new container
            // Stock
            Route::post('/stock.add.existing', [StockController::class, 'addExistingStock'])->name('stock.add.existing'); // add existing stock quantity
            Route::post('/stock.add.new', [StockController::class, 'addNewStock'])->name('stock.add.new'); // add new stock 
            Route::post('/stock.edit', [StockController::class, 'editStock'])->name('stock.edit'); // edit stock
            Route::post('/stock.edit.imageupload', [StockController::class, 'uploadStockImage'])->name('stock.edit.imageupload'); // add stock image in edit stock
            Route::post('/stock.edit.imagelink', [StockController::class, 'linkStockImage'])->name('stock.edit.imagelink'); // link stock image
            Route::post('/stock.edit.imageunlink', [StockController::class, 'unlinkStockImage'])->name('stock.edit.imageunlink'); // unlink stock image
            // Changelog
            Route::post('/changelog.filter', [ChangelogController::class, 'filterChangelog'])->name('changelog.filter'); // filter the changelog
            //

            ////
        });

        // no auth needed
        Route::get('/about', [AboutController::class, 'index'])->name('about'); // no auth needed
        Route::get('/error', [IndexController::class, 'error'])->name('error'); // no auth needed
        Route::get('/test', [IndexController::class, 'test'])->name('test'); // Testing page

    });
});


require __DIR__.'/auth.php';
