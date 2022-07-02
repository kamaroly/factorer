<?php

/*
*
* Backend Routes
*
* --------------------------------------------------------------------
*/
Route::group(['namespace' => '\Modules\Accounting\Http\Controllers\Backend', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    Route::get('/accounting', 'AccountingController@index')->name('accounting.index');
    Route::post('/accounting/store', 'AccountingController@store')->name('accounting.store');

    Route::get('/accounting/postings', 'PostingsController@index')->name('accounting.postings.index');
});
