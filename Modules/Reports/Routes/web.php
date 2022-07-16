<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['namespace' => '\Modules\Reports\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {

    Route::prefix('reports')->group(function() {

        Route::get('/', 'ReportsController@index')->name('reports.index');
        Route::get('/filters', 'ReportFilterController@index')->name('reports.filter');

        // Produit Fini
        Route::get('/product-inventory', 'ProductInventoryController@index')->name('reports.product-inventory');
    });


});
