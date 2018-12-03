<?php
namespace App\Http\Controllers\Api\Forms;

use App\Centros;
use App\Ciclos;
use App\Ciudades;
use App\Cursos;
use App\Http\Controllers\Controller;
use App\Inscripcions;
use Illuminate\Support\Facades\Input;

class Forms extends Controller
{
    public function ciclos()
    {
        return Ciclos::select('id','nombre')->get();
    }
    public function centros()
    {
        $campo = ['id','cue','nombre','ciudad_id','nivel_servicio','direccion','sector','telefono'];
        $centro = Centros::select($campo);

        $nivel_servicio = Input::get('nivel_servicio');
        $ciudad = Input::get('ciudad');
        $ciudad_id = Input::get('ciudad_id');
        $sector = Input::get('sector');
        $nombre = Input::get('nombre');

        if($nivel_servicio) {
            $centro->where('nivel_servicio',$nivel_servicio);
        }

        if($ciudad_id) {
            $centro->where('ciudad_id',$ciudad_id);
        }

        if($ciudad) {
            $ciudad = Ciudades::where('nombre',$ciudad)->first();
            $centro->where('ciudad_id',$ciudad->id);
        }

        if($sector) {
            $centro->where('sector',$sector);
        }

        if($nombre) {
            $centro->where('nombre','like','%'.$nombre.'%');
        }

        return $centro->get();
    }
    public function ciudades()
    {
        return Ciudades::select('id','nombre')->get();
    }
    public function sectores()
    {
        return Centros::select('sector')->groupBy('sector')->get();
    }
    public function niveles()
    {
        return Centros::select('nivel_servicio')->groupBy('nivel_servicio')->get();
    }

    public function años()
    {
        return Cursos::select('anio')->groupBy('anio')->get();
    }
    public function divisiones()
    {
        return Cursos::select('division')->groupBy('division')->where('division','<>','')->get();
    }
    public function turnos()
    {
        return Cursos::select('turno')->groupBy('turno')->get();
    }
    public function tipos()
    {
        return Cursos::select('tipo')->groupBy('tipo')->get();
    }
    public function estado_inscripcion()
    {
        return Inscripcions::select('estado_inscripcion')->groupBy('estado_inscripcion')->get();
    }
}