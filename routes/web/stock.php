<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\TagController;

Route::middleware(['auth', 'check.permission:stock'])->group(function () { // Stock pages - locked behind the stock permission
    
    Route::get('/stock/{stock_id}/{modify_type?}/{add_new?}/{search?}', [StockController::class, 'index']) // stock pages
        ->where('stock_id', '[0-9\-]+') // Ensure stock_id is numeric or -
        ->where('add_new', '[a-z\-]+') // allow text and -
        ->name('stock');

    Route::get('/transactions/{type?}/{stock_id?}', [TransactionController::class, 'index']) // transactions page
        ->where('type', '[a-z\-]+') // type of transaction
        ->where('stock_id', '[0-9]+') // Ensure stock_id is numeric
        ->name('transactions');
        
    Route::get('/favourites', [FavouritesController::class, 'index'])->name('favourites'); // favourites page
    Route::get('/tags', [TagController::class, 'index'])->name('tags'); // tags page
    Route::get('/importstock', [StockController::class, 'importStockView'])->name('importstock'); // import stock page
    

    // POST REQUESTS
    Route::post('/tags.editTag', [TagController::class, 'editTag'])->name('tags.editTag'); // edit tags
    Route::post('/stock.add.existing', [StockController::class, 'addExistingStock'])->name('stock.add.existing'); // add existing stock quantity
    Route::post('/stock.add.new', [StockController::class, 'addNewStock'])->name('stock.add.new'); // add new stock 
    Route::post('/stock.add.import', [StockController::class, 'importStock'])->name('stock.add.import'); // import stock
    Route::post('/stock.remove.existing', [StockController::class, 'removeExistingStock'])->name('stock.remove.existing'); // remove existing stock
    Route::post('/stock.remove.existing.id', [StockController::class, 'removeExistingStockById'])->name('stock.remove.existing.id'); // remove existing stock by ID
    Route::post('/stock.move', [StockController::class, 'moveStock'])->name('stock.move'); // move stock quantity
    Route::post('/stock.move.container', [StockController::class, 'moveStockContainer'])->name('stock.move.container'); // move stock quantity when item is a container
    Route::post('/stock.move.cable', [StockController::class, 'moveStockCable'])->name('stock.move.cable'); // move cable stock quantity
    Route::post('/stock.edit', [StockController::class, 'editStock'])->name('stock.edit'); // edit stock
    Route::post('/stock.edit.imageupload', [StockController::class, 'uploadStockImage'])->name('stock.edit.imageupload'); // add stock image in edit stock
    Route::post('/stock.edit.imagelink', [StockController::class, 'linkStockImage'])->name('stock.edit.imagelink'); // link stock image
    Route::post('/stock.edit.imageunlink', [StockController::class, 'unlinkStockImage'])->name('stock.edit.imageunlink'); // unlink stock image
    Route::post('/stock.edit.item', [StockController::class, 'editItem'])->name('stock.edit.item'); // edit item info
    Route::post('/stock.delete.existing', [StockController::class, 'deleteStock'])->name('stock.delete.existing'); // delete unused stock
    
});