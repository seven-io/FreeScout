<?php

Route::group([
    'middleware' => [
        //'auth', // TODO!?
        //'roles', // TODO!?
        'web',
    ],
    'namespace' => 'Modules\Sms77\Http\Controllers',
    'prefix' => Helper::getSubdirectory(),
    'roles' => ['admin'],
], function () {
    Route::get('/sms77', ['uses' => 'Sms77Controller@index'])->name('sms77.index');
    Route::post('/sms77', ['uses' => 'Sms77Controller@submit']);

    Route::get('/users/sms77_sms/{id}', 'Sms77Controller@user')->name('sms77.sms_user');
    Route::post('/users/sms77_sms/{id}', 'Sms77Controller@userSubmit');
});