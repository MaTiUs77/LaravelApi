<?php
namespace App\Http\Controllers\Api\Forms;

use App\Centros;
use App\Ciclos;
use App\Ciudades;
use App\Cursos;
use App\CursosInscripcions;
use App\Http\Controllers\Controller;
use App\Inscripcions;

class Forms extends Controller
{
    public function ciclos()
    {
        return Ciclos::select('id','nombre')->get();
    }

    public function centros()
    {
        return Centros::select('id','nombre')->get();
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
        return Cursos::select('division')->groupBy('division')->get();
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
