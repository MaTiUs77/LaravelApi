<?php

namespace App\Http\Controllers\Api\Pase;

use App\Centros;
use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Pases;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class PaseCrud extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt');
    }

    // Index
    public function index()
    {
        $por_pagina = Input::get('por_pagina');

        // Relacion de modelo
        $with = ['Ciclo','Alumno.Persona','CentroOrigen','CentroDestino'];

        if(Input::get('with')) {
            $appendWith = explode(',',Input::get('with'));
            $with = collect($with)->merge($appendWith)->unique()->toArray();
        }

        $query = Pases::with($with);

        // Filtros
        $ciclo = Input::get('ciclo');
        $centro_origen = Input::get('centro_origen');
        $centro_destino = Input::get('centro_destino');
        $documentacion = Input::get('documentacion');
        $estado = Input::get('estado');

        // Aplicar filtros
        if($ciclo) { $query->filtrarCicloNombre($ciclo); }
        if($centro_origen) { $query->filtrarCentroOrigen($centro_origen); }
        if($centro_destino) { $query->filtrarCentroDestino($centro_destino); }
        if($documentacion) { $query->where('estado_documentacion',$documentacion); }
        if($estado) { $query->where('estado_pase',$estado); }

        // Paginacion de resultados
        return $query->customPagination($por_pagina);
    }

    // Show
    public function show($id)
    {
        // Relacion de modelo
        $with = ['Ciclo','Alumno.Persona','CentroOrigen','CentroDestino'];

        if(Input::get('with')) {
            $appendWith = explode(',',Input::get('with'));
            $with = collect($with)->merge($appendWith)->unique()->toArray();
        }

        $query = Pases::with($with)->where('id',$id)->first();

        return $query;
    }

    // Add
    public function store()
    {
        $validationRules = [
            'inscripcion_id' => 'required|numeric',
            'centro_id_origen' => 'required|numeric',
            'centro_id_destino' => 'required|numeric',
            'tipo' => 'required|string',
            'motivo' => 'required|string',
            'observaciones' => 'string',
            'nota_tutor' => 'numeric',
        ];

        // Se validan los parametros
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        // Verificar existencia
        $cursoInscripcion = CursosInscripcions::filtrarInscripcion(Input::get('inscripcion_id'))
            ->first();
        $centroOrigen = Centros::find(Input::get('centro_id_origen'));
        $centroDestino = Centros::find(Input::get('centro_id_destino'));

        if(!$cursoInscripcion) { return ['error'=>'La inscripcion no existe']; }
        if(!$centroOrigen) { return ['error'=>'El centro origen no existe']; }
        if(!$centroDestino) { return ['error'=>'El centro destino no existe']; }

        return compact('cursoInscripcion');
    }

    // Edit
    public function update($id)
    {
        $validationRules = [
            'cuenta' => 'required|string',
            'categoria' => 'required|string',
            'concepto' => 'required|string',
            'cuotas' => 'required|numeric',
            'cuota_nro' => 'required|numeric',
            'monto' => 'required|numeric',
            'fecha' => 'required|date',
        ];

        // Se validan los parametros
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }
    }

    // Delete
    public function destroy($id)
    {
        $validationRules = [
            'id' => 'required|numeric'
        ];

        // Se validan los parametros
        $validator = Validator::make(['id'=>$id], $validationRules);
        if ($validator->fails()) {
            return ['error' => $validator->errors()];
        }

        $query = Pases::find($id);

        if($query!=null)
        {
            $query->delete();

            return ['success'=>'Pase eliminado con exito'];
        }  else {
            return ['error'=>'El ID del pase no existe'];
        }
    }
}
