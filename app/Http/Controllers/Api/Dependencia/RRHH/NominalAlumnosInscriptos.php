<?php
namespace App\Http\Controllers\Api\Dependencia\RRHH;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class NominalAlumnosInscriptos extends Controller
{
    public function __construct()
    {
        //$this->middleware('jwt');
    }

    public function start()
    {
        // Por defecto API Consume ejecuta la ruta /api/v1/{path}
        $params = Input::all();
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);
        if($api->hasError()) { return $api->getError(); }
        $response= $api->response();

        // Transforma los datos a collection para realizar un mapeo
        $data = collect($response['data']);

        // Optimizar:: Usar Resource en vez de un mapeo por array
        $formatted = $data->map(function($item){
            $inscripcion = $item['inscripcion'];
            $curso = $item['curso'];

            $ciclo = $inscripcion['ciclo'];
            $centro = $inscripcion['centro'];
            $persona = $inscripcion['alumno']['persona'];

            return [
                'documento_tipo' => $persona['documento_tipo'],
                'inscripcion_id' => $inscripcion['id'],
                'dni' => $persona['documento_nro'],
                'nombres' => $persona['nombres'],
                'apellidos' => $persona['apellidos'],
                'nombre_completo' => $persona['nombre_completo'],
                'sexo' => $persona['sexo'],
                'edad' => $persona['edad'],
                'ciclo' => $ciclo['nombre'],
                'centro' => $centro['nombre'],
                'nivel_servicio' => $centro['nivel_servicio'],
                'año' => $curso['anio'],
                'division' => $curso['division'],
                'turno' => $curso['turno'],
                'fecha_alta' => $inscripcion['fecha_alta'],
                'fecha_baja' => $inscripcion['fecha_baja'],
                'fecha_egreso' => $inscripcion['fecha_egreso'],
                'calle_nombre' =>$persona['calle_nombre'],
                'calle_nro' =>$persona['calle_nro'],
                'pcia_nac' =>$persona['pcia_nac'],
                'email' =>$persona['email'],
                'telefono_nro' =>$persona['telefono_nro'],
                'observaciones' =>$persona['observaciones'],
                'fecha_nac' => $persona['fecha_nac']
            ];
        });

        $response['data'] = $formatted;

        // Exportacion a Excel si es solicitado
        $this->exportar($formatted);

        return $response;
    }

    private function exportar($lista) {
        $ciclo = Input::get('ciclo');
        // Exportacion a Excel
        if(Input::get('export')) {
            $content = [];
            $content[] = [
                'Ciclo',
                'Centro',
                'Nivel Servicio',
                'Nombre',
                'Apellido',
                'DNI',
                'Año',
                'Division',
                'Turno',
                'Fecha Alta',
                'Fecha Baja',
                'Fecha Egreso',
            ];
            // Contenido
            foreach($lista as $item) {
                $item = (object) $item;
                $content[] = [
                    $item->ciclo,
                    $item->centro,
                    $item->nivel_servicio,
                    $item->nombres,
                    $item->apellidos,
                    $item->dni,
                    $item->año,
                    $item->division,
                    $item->turno,
                    $item->fecha_alta,
                    $item->fecha_baja,
                    $item->fecha_egreso
                ];
            }

            Export::toExcel("RRHH_AlumnosNominal","RRHH_AlumnosNominal",$content);
        }
    }
}
