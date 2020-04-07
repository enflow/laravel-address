<?php

use Enflow\Address\Http\Controllers\SuggestController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => config('address.api_route_prefix', 'address'), 'middleware' => config('address.api_middleware', ['web'])], function () {
    Route::get('suggest', SuggestController::class)->name('address::suggest')->middleware('throttle:' . config('address.throttles.suggest', '60,5'));
});
