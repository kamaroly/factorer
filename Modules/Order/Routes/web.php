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
Route::group(['namespace' => '\Modules\Order\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {
    Route::prefix('order')->group(function() {
        Route::get('/', 'OrderController@index')->name('order.index');
        Route::get('/create', 'OrderController@create')->name('order.create');
        Route::post('/store', 'OrderController@store')->name('order.store');
        Route::get('/{orderId}', 'OrderController@showInvoice')->name('order.receipt');
    });
});
