<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head><body>
@php
    $fecha_inscripcion = $cursoInscripcions->inscripcion->modified;
    if($fecha_inscripcion!=null)
    {
        $fecha_inscripcion =  Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $fecha_inscripcion );
    }

    // Alias para un render mas legible
    $inscripcion = $cursoInscripcions->inscripcion;
    $curso= $cursoInscripcions->curso;

    $ciclo = $inscripcion->ciclo;
    $centro = $inscripcion->centro;
    $alumno = $inscripcion->alumno;
    $persona = $alumno->persona;

@endphp

<!-- ORIGINAL -->
@include('slot_constancia_inscripcion')
<br>
<!-- COPIA -->
@include('slot_constancia_inscripcion')

</body></html>
