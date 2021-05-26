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
    Route::post('/sms77', ['uses' => 'Sms77Controller@submit'])->name('sms77.submit');
});