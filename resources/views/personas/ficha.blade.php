<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

<style type="text/css>">
    .tabla tr th {
        padding:5px;
        border-left: 1px solid #efefef;
        border-bottom: 1px solid #efefef;
    }
    .tabla tr td {
        padding:5px;
        border-left: 1px solid #efefef;
        border-bottom: 1px solid #efefef;
    }

    h3 {
        color: #909090;
    }

    .fecha {
        color: #ff2a82;
    }

    .alumno {
        color: #909090;
    }
</style>
</head><body>
    <div style="font-size: 14px;font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif">
        <p>
            COLEGIO PROVINCIAL “ERNESTO SABATO”
        </p>

        <h2 style="text-align: center">
            LEGAJO DEL ALUMNO
        </h2>

        <h2 class="alumno">
           {{ $persona['nombre_completo']  }}
            -
            DNI {{ $persona['documento_nro'] }}
        </h2>

        <hr>

        <p class="fecha">
            4 DE SEPTIEMBRE DEL 20XX A LAS 16:30
        </p>
        <h3>DATOS DE PERSONALES DEL ALUMNO</h3>
        <table class="tabla">
            <tr>
                <th>Fecha de nacimiento</th>
                <th>Lugar de nacimiento</th>
                <th>Nacionalidad</th>
            </tr>
            <tr>
                <td>{{ $persona['fecha_nac'] }}</td>
                <td>{{ $persona['pcia_nac'] }}</td>
                <td>{{ $persona['nacionalidad'] }}</td>
            </tr>
        </table>

        <h3>DATOS DE CONTACTO DEL ALUMNO</h3>
        <table class="tabla">
            <tr>
                <th>Domicilio</th>
                <th>Barrio</th>
                <th>Telefono</th>
                <th>Email</th>
            </tr>
            <tr>
                <td>{{ $persona['calle_nombre'] }} {{ $persona['calle_nro'] }}</td>
                <td>{{ $persona['barrio']['nombre'] }}</td>
                <td>{{ $persona['telefono_nro'] }}</td>
                <td>{{ $persona['email'] }}</td>
            </tr>
        </table>

        <h3>FAMILIARES</h3>
        <table class="tabla">
            <tr>
                <th>Vinculo</th>
                <th>Nombre completo</th>
                <th>Telefono</th>
                <th>Email</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <h3>TRAYECTO ESCOLAR</h3>
        <table class="tabla">
            <tr>
                <th>Ciclo</th>
                <th>Año</th>
                <th>Division</th>
                <th>Turno</th>
                <th>Centro</th>
                <th>Nivel de servicio</th>
                <th>Estado inscripcion</th>
                <th>Legajo</th>
            </tr>
            @foreach(collect($trayectoria)->sortBy('inscripcion.legajo_nro') as $item)
            <tr>
                <td>{{ $item['inscripcion']['ciclo']['nombre'] }}</td>
                <td>{{ $item['curso']['anio'] }}</td>
                <td>{{ $item['curso']['division'] }}</td>
                <td>{{ $item['curso']['turno'] }}</td>
                <td>{{ $item['inscripcion']['centro']['nombre'] }}</td>
                <td>{{ $item['inscripcion']['centro']['nivel_servicio'] }}</td>
                <td>{{ $item['inscripcion']['estado_inscripcion'] }}</td>
                <td>{{ $item['inscripcion']['legajo_nro'] }}</td>
            </tr>
            @endforeach
        </table>

        </div>
</body></html>
