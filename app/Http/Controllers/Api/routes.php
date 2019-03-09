<?php

Route::get('/', 'ApiController@home')->name('apihome');

Route::post('auth/login', 'ApiController@login');
Route::get('auth/logout', 'ApiController@logout')->middleware(['jwt.auth']);

//Route::post('auth/register', 'ApiController@register');