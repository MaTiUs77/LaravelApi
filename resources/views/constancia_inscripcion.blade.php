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
@endphp

<!-- ORIGINAL -->
<div style="font-size: 14px">
    <table style="width: 100%;" class="header">
        <tr>
            <td>
                <img src="img/escudo.png" style="margin-left:30px;width: 100px">
                <div style="font-style: italic">Provincia de Tierra del Fuego,</div>
                <div style="font-style: italic">Antártida e Islas del Atlántico Sur</div>
                <div style="font-style: italic;color: #5e5e5e;">República Argentina</div>
                <div style="font-style: italic">Ministerio de Educación</div>
                @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio!='Común - Secundario')
                    <div style="font-weight: bold;">Supervisión Técnica-Supervisión Escolar</div>
                @endif
            </td>
            <td>
                <h2 style="text-align: right">INSCRIPCIÓN NÚMERO | {{ $cursoInscripcions->inscripcion->legajo_nro }}</h2>
                <div style="text-align: right; font-size: 12px">“2019 – AÑO DEL CENTENARIO DEL NACIMIENTO DE EVA DUARTE DE PERÓN”</div>
            </td>
        </tr>
    </table>
    <br>
    <h3>CONSTANCIA DE INSCRIPCIÓN | Estado: {{ $cursoInscripcions->inscripcion->estado_inscripcion }}</h3>
    <p>
        @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio!='Común - Secundario')
            La Supervisión Técnica de Supervisión Escolar,
        @else
            Se
        @endif
        deja constancia que el/la niño/a <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->apellidos) }}, {{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->nombres) }}</b>,
        documento tipo: <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_tipo) }}</b>, N°
        <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_nro) }}</b>,
        ha sido INSCRIPTO/A en esta dependencia, para la Escuela Provincial/Jardín de Infantes: <b>{{ $cursoInscripcions->inscripcion->centro->nombre }}</b>
        en el grado/sala <b>{{ $cursoInscripcions->curso->anio }} {{ $cursoInscripcions->curso->division }} {{ $cursoInscripcions->curso->turno }}</b>
        para el Ciclo Escolar <b>{{ $cursoInscripcions->inscripcion->ciclo->nombre }}</b>
        en <b>{{ $cursoInscripcions->inscripcion->centro->ciudad->nombre }}</b> el día: <b>{{ ($fecha_inscripcion!=null) ? $fecha_inscripcion->format('d/m/Y') :'__/__/____' }}</b>
    </p>
    <p>
        {{--
        @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio=='Común - Secundario')
            IMPORTANTE: La inscripción toma carácter de <b>NO CONFIRMADA</b> hasta tanto finalicen los plazos de inscripción.
            Se confirmará automáticamente si no se superan las plazas disponibles.<br>

            El padre/madre/tutor, en caso de que las inscripciones superen la cantidad de plazas disponibles, deberá presentarse en esta
            institución el día <b>Miércoles 12 de Diciembre</b>.
        @endif
        --}}
        <br>
        <br>
    </p>
    <div style="float:right;border-top: 1px solid #000;">Sello y firma de autoridad institucional</div>
    <span style="clear:both;color:#3a3a3a;font-size:11px;font-style: italic;font-weight: bold;">Las Islas Malvinas, Georgias, Sandwich del Sur, son y serán Argentinas</span>
    <hr />
</div>

<br>

<!-- COPIA -->
<div style="font-size: 14px">
    <table style="width: 100%;" class="header">
        <tr>
            <td>
                <img src="img/escudo.png" style="margin-left:30px;width: 100px">
                <div style="font-style: italic">Provincia de Tierra del Fuego,</div>
                <div style="font-style: italic">Antártida e Islas del Atlántico Sur</div>
                <div style="font-style: italic;color: #5e5e5e;">República Argentina</div>
                <div style="font-style: italic">Ministerio de Educación</div>
                @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio!='Común - Secundario')
                    <div style="font-weight: bold;">Supervisión Técnica-Supervisión Escolar</div>
                @endif
            </td>
            <td>
                <h2 style="text-align: right">INSCRIPCIÓN NÚMERO | {{ $cursoInscripcions->inscripcion->legajo_nro }}</h2>
                <div style="text-align: right; font-size: 12px">“2019 – AÑO DEL CENTENARIO DEL NACIMIENTO DE EVA DUARTE DE PERÓN”</div>
            </td>
        </tr>
    </table>
    <br>
    <h3>CONSTANCIA DE INSCRIPCIÓN | Estado: {{ $cursoInscripcions->inscripcion->estado_inscripcion }}</h3>
    <p>
        @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio!='Común - Secundario')
            La Supervisión Técnica de Supervisión Escolar,
        @else
            Se
        @endif
        deja constancia que el/la niño/a <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->apellidos) }}, {{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->nombres) }}</b>,
        documento tipo: <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_tipo) }}</b>, N°
        <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_nro) }}</b>,
        ha sido INSCRIPTO/A en esta dependencia, para la Escuela Provincial/Jardín de Infantes: <b>{{ $cursoInscripcions->inscripcion->centro->nombre }}</b>
        en el grado/sala <b>{{ $cursoInscripcions->curso->anio }} {{ $cursoInscripcions->curso->division }} {{ $cursoInscripcions->curso->turno }}</b>
        para el Ciclo Escolar <b>{{ $cursoInscripcions->inscripcion->ciclo->nombre }}</b>
        en <b>{{ $cursoInscripcions->inscripcion->centro->ciudad->nombre }}</b> el día: <b>{{ ($fecha_inscripcion!=null) ? $fecha_inscripcion->format('d/m/Y') :'__/__/____' }}</b>
    </p>
    <p>
        {{--
        @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio=='Común - Secundario')
            IMPORTANTE: La inscripción toma carácter de <b>NO CONFIRMADA</b> hasta tanto finalicen los plazos de inscripción.
            Se confirmará automáticamente si no se superan las plazas disponibles.<br>

            El padre/madre/tutor, en caso de que las inscripciones superen la cantidad de plazas disponibles, deberá presentarse en esta
            institución el día <b>Miércoles 12 de Diciembre</b>.
        @endif
        --}}
        <br>
        <br>
    </p>
    <div style="float:right;border-top: 1px solid #000;">Sello y firma de autoridad institucional</div>
    <span style="clear:both;color:#3a3a3a;font-size:11px;font-style: italic;font-weight: bold;">Las Islas Malvinas, Georgias, Sandwich del Sur, son y serán Argentinas</span>
    <hr />
</div>

</body></html>
