<?php
// Deprecada
Route::resource('/centros', 'Api\Centros\v1\CentrosCrud');

// v1
Route::resource('/v1/centros', 'Api\Centros\v1\CentrosCrud');