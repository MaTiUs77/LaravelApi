<?php
// Deprecada
Route::resource('/carousel', 'Api\Carousel\v1\CarouselCrud');

// v1
Route::prefix('v1')->group(function () {
    Route::resource('/carousel', 'Api\Carousel\v1\CarouselCrud');
});
