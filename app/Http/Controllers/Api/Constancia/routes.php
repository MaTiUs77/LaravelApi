<?php
Route::get('constancia/{inscripcion_id}', 'Api\Constancia\Constancia@generarPdf');

Route::get('web/{id}','Api\Constancia\Constancia@web');
Route::get('local/{id}','Api\Constancia\Constancia@local');
