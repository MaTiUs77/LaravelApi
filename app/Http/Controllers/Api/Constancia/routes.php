<?php
Route::get('constancia/{inscripcion_id}', 'Api\Constancia\Constancia@generarPdf');
Route::get('test/{id}','Api\Constancia\Constancia@test');
