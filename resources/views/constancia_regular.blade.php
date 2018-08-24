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

    <div style="font-size: 14px">
            <table style="width: 100%;" class="header">
                <tr>
                    <td>
                        <img src="escudo.png" style="margin-left:30px;width: 100px">
                        <div style="font-style: italic">Provincia de Tierra del Fuego,</div>
                        <div style="font-style: italic">Antártida e Islas del Atlántico Sur</div>
                        <div style="font-style: italic;color: #5e5e5e;">República Argentina</div>
                        <div style="font-style: italic">Ministerio de Educación</div>
                        @if(isset($cursoInscripcions->inscripcion->centro->nivel_servicio) && $cursoInscripcions->inscripcion->centro->nivel_servicio!='Común - Secundario')
                            <div style="font-weight: bold;">Supervisión Técnica-Supervisión Escolar</div>
                        @endif
                    </td>
                    <td>
                        <div style="text-align: right;line-height: 150px;">2018- "Año de los 44 Héroes del Submarino ARA San Juan"</div>
                    </td>
                </tr>
            </table>
            <hr>
            <div style="text-align:center">
                <h3>CONSTANCIA DE ALUMNO REGULAR</h3>

                <div>
                    {{ $cursoInscripcions->inscripcion->centro->nombre }}
                    C.U.E. N°
                    {{ $cursoInscripcions->inscripcion->centro->cue }}
                </div>

                <div style="padding:10px; font-size:12px;font-weight: bold;">
                    {{ strtoupper($cursoInscripcions->inscripcion->centro->direccion) }} -
                    {{ strtoupper($cursoInscripcions->inscripcion->centro->ciudad->nombre) }}
                </div>
            </div>
            <p>
                Se hace constar que <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->apellidos) }}, {{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->nombres) }}</b>,
                documento tipo: <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_tipo) }}</b>, N°
                <b>{{ strtoupper($cursoInscripcions->inscripcion->alumno->persona->documento_nro) }}</b>
                es alumno regular de este establecimiento y se encuentra cursando año <b>{{ $cursoInscripcions->curso->anio }}</b>, división <b>{{ $cursoInscripcions->curso->division }}</b>
                del servicio y nivel <b>{{ $cursoInscripcions->inscripcion->centro->nivel_servicio }}</b>
            </p>

            @if(!empty($cursoInscripcions->inscripcion->alumno->observaciones))
            <h4>Datos complementarios</h4>

            <p>
                {{ $cursoInscripcions->inscripcion->alumno->observaciones }}
            </p>
            @endif
            <p>
		A pedido del/a interesado/a y al solo efecto de ser presentada ante quien corresponda 
                se extiende la presente, sin enmiendas ni raspaduras en la ciudad de <b>{{ $cursoInscripcions->inscripcion->centro->ciudad->nombre }}</b>, Provincia de Tierra del Fuego,
                el <b>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</b>.
            </p>

            <h3 style="text-align: center;padding-top:40px;padding-bottom:100px;">DOCUMENTO NO VÁLIDO PARA EL COBRO DE SALARIO FAMILIAR
            </h3>

            <div style="float:right;border-top: 1px solid #000;">Sello y firma de la autoridad institucional</div>

            <span style="clear:both;color:#3a3a3a;font-size:11px;font-style: italic;font-weight: bold;">Las Islas Malvinas, Georgias, Sandwich del Sur, son y serán Argentinas</span>
            <hr />
        </div>
</body></html>
