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
            Route::get('/theme-testing', [ProfileController::class, 'themeTesting'])->name('theme-testing'); // theme-testing page

            //// General routes
            // normal routes
            Route::get('/', [IndexController::class, 'index'])->name('index'); // home page
            Route::get('/cablestock', [CablestockController::class, 'index'])->name('cablestock'); // cablestock page
            Route::get('/assets', [AssetsController::class, 'index'])->name('assets'); // assets page
                // make these \/ work the same way as the stock/add + stock/remove etc and dynamically load the content in -> same goes for the optics page,
                // but this has a permission with it. Might be worth making more permissions and having a full permissions matrix
                // ^ maybe instead of doing permission groups, have a simple permissions table, with booleans
                // This way each users' permissions can be adjusted one by one e.g.
                //      optics: x | admin: x | cpus:   | memory: x | etc
                // admin will default to have all of them, and will auto tick all boxes
                Route::get('/assets/cpus', [AssetsController::class, 'cpus'])->name('cpus'); // assets > cpus page
                Route::get('/assets/memory', [AssetsController::class, 'incomplete'])->name('memory'); // assets > memory page
                Route::get('/assets/disks', [AssetsController::class, 'incomplete'])->name('disks'); // assets > disks page
                Route::get('/assets/fans', [AssetsController::class, 'incomplete'])->name('fans'); // assets > fans page
                Route::get('/assets/psus', [AssetsController::class, 'incomplete'])->name('psus'); // assets > psus page
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
            Route::get('/assets/optics', [OpticsController::class, 'index'])->name('optics'); // assets > optics page

            // admin routes - auth in progress
            Route::get('/admin', [AdminController::class, 'index'])->name('admin'); // admin page
            Route::get('/changelog/{start_date?}/{end_date?}/{table?}/{user?}/{page?}', [ChangelogController::class, 'index'])->name('changelog'); // admin page

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
            // Cablestock
            Route::post('/cablestock.modifyStock', [CableStockController::class, 'modifyCableStock'])->name('cablestock.modifyStock'); // modify cable stock 
            Route::post('/cablestock.moveStock', [CableStockController::class, 'moveCableStock'])->name('cablestock.moveStock'); // move cable stock 
            // Changelog
            Route::post('/changelog.filter', [ChangelogController::class, 'filterChangelog'])->name('changelog.filter'); // filter the changelog
            // Theme-testing
            Route::post('/theme-testing.uploadTheme', [ProfileController::class, 'uploadTheme'])->name('theme-testing.uploadTheme'); // filter the changelog
            // Profile
            Route::post('/profile.enable2FA', [ProfileController::class, 'enable2FA'])->name('profile.enable2FA'); // enable 2FA
            Route::post('/profile.reset2FA', [ProfileController::class, 'reset2FA'])->name('profile.reset2FA'); // reset 2FA secret
            // Admin - Auth needed here
            Route::post('/admin.globalSettings', [AdminController::class, 'globalSettings'])->name('admin.globalSettings'); // Adjust global settings
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
