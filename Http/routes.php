<?php

Route::group([
    'middleware' => [
        'web',
    ],
    'namespace' => 'Modules\Seven\Http\Controllers',
    'prefix' => Helper::getSubdirectory(),
    'roles' => ['admin'],
], function () {
    Route::get('/seven', ['uses' => 'SevenController@index'])->name('seven.index');
    Route::post('/seven', ['uses' => 'SevenController@submit']);

    Route::get('/users/seven_sms/{id}', 'SevenController@user')->name('seven.sms_user');
    Route::post('/users/seven_sms/{id}', 'SevenController@userSubmit');
});
