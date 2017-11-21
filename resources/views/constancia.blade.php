<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
@php
    $fecha_inscripcion = $cursoInscripcions->inscripcion->modified;
    if($fecha_inscripcion!=null)
    {
        $fecha_inscripcion =  Carbon\Carbon::createFromFormat('Y-m-d h:i:s', $fecha_inscripcion );
    }
@endphp
    <div class="page" style="font-size: 11pt">
            {{--<img src="{{ asset('encabezado.jpg') }}" style="float: center; margin: 0.5em;">--}}
            <table style="width: 100%;" class="header">
                <tr>
                    <td><h1 style="text-align: right">INSCRIPCIÓN NÚMERO | {{ $cursoInscripcions->inscripcion->legajo_nro }}</h1></td>
                </tr>
            </table>
            <br>
            <h3>CONSTANCIA DE INSCRIPCIÓN {{ $cursoInscripcions->inscripcion->estado_inscripcion }}</h3>
            <br>
            <p> La Supervisión Técnica de Supervisión Escolar, deja constancia que el/la niño/a <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->apellidos) }}, {{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->nombres) }}</b>,
                ha sido INSCRIPTO/A en esta dependencia, para la Escuela Provincial/Jardín de Infantes: <b>{{ $cursoInscripcions->curso->centro->nombre }}</b>
                en el grado/sala <b>{{ $cursoInscripcions->curso->anio }} {{ $cursoInscripcions->curso->division }} {{ $cursoInscripcions->curso->turno }}</b>
                para el Ciclo Escolar <b>{{ $cursoInscripcions->inscripcion->ciclo->nombre }}</b>
                en Ushuaia el día: <b>{{ ($fecha_inscripcion!=null) ? $fecha_inscripcion->format('d/m/Y') :'__/__/____' }}</b>
            </p>
            <br>
            <p>IMPORTANTE: El padre/tutor tiene 24 horas para presentarse en la Escuela indicada, caso contrario el/la niño/a perderá la vacante.</p>
            <br>
            <br>
            <table style="width: 100%;" class="header">
                <tr>
                    <td><h1 style="text-align: right" class="overline stylex block">Sello y firma de supervisor</h1></td>
                </tr>
            </table>
        {{--    <img src="{{ asset('footer.png') }}" style="float: center; margin: 0.5em;">--}}
            <hr />
        </div>
    <div class="page" style="font-size: 11pt">
            {{--<img src="{{ asset('encabezado.jpg') }}" style="float: center; margin: 0.5em;">--}}
            <table style="width: 100%;" class="header">
                <tr>
                    <td><h1 style="text-align: right">INSCRIPCIÓN NÚMERO | {{ $cursoInscripcions->inscripcion->legajo_nro }}</h1></td>
                </tr>
            </table>
            <br>
            <h3>CONSTANCIA DE INSCRIPCIÓN {{ $cursoInscripcions->inscripcion->estado_inscripcion }}</h3>
            <br>
            <p> La Supervisión Técnica de Supervisión Escolar, deja constancia que el/la niño/a <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->apellidos) }}, {{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->nombres) }}</b>,
                ha sido INSCRIPTO/A en esta dependencia, para la Escuela Provincial/Jardín de Infantes: <b>{{ $cursoInscripcions->curso->centro->nombre }}</b>
                en el grado/sala <b>{{ $cursoInscripcions->curso->anio }} {{ $cursoInscripcions->curso->division }} {{ $cursoInscripcions->curso->turno }}</b>
                para el Ciclo Escolar <b>{{ $cursoInscripcions->inscripcion->ciclo->nombre }}</b>
                en Ushuaia el día: <b>{{ ($fecha_inscripcion!=null) ? $fecha_inscripcion->format('d/m/Y') :'__/__/____' }}</b>
            </p>
            <br>
        </div>
</body>
</html>