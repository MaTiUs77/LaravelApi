<?php

namespace App\Http\Controllers\Api\Carousel\v1;

use App\Http\Controllers\Api\Utilities\DefaultValidator;
use App\Http\Controllers\Controller;
use App\Carousel;

class CarouselCrud extends Controller
{
    public function index()
    {
        // Se validan los parametros
        $input = request()->all();
        
        $query = Carousel::withOnDemand();

        $query->when(request('mobile'), function ($q, $v) {
            return $q->where('mobile', $v);
        });

        $query->when(request('desktop'), function ($q, $v) {
            return $q->where('desktop', $v);
        });

        $query->when(request('enabled'), function ($q, $v) {
            return $q->where('enabled', $v);
        });

        $carousel = $query->get();

        if($carousel->isNotEmpty()) {
            return $carousel;
        } else {
            abort(204,'No se encontraron resultados con el filtro aplicado');
        }
    }

    public function show($id)
    {
        // Se validan los parametros
        $input = ['id'=>$id];
        $rules = ['id'=>'required|numeric'];
        if($fail = DefaultValidator::make($input,$rules)) return $fail;

        $query = Carousel::withOnDemand();

        // Localiza la imagen de Carousel en cuestion
        $carousel = $query->findOrFail($id);

        return $carousel;
    }
}
