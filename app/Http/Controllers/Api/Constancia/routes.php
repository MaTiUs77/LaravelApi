<?php
Route::get('constancia/{inscripcion_id}', 'Api\Constancia\Constancia@inscripcion');
Route::get('constancia_regular/{inscripcion_id}', 'Api\Constancia\Constancia@regular');