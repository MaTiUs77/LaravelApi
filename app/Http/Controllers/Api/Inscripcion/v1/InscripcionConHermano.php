<?php
namespace App\Http\Controllers\Api\Inscripcion\v1;

use App\Http\Controllers\Api\Utilities\ApiConsume;
use App\Http\Controllers\Api\Utilities\Export;
use App\Http\Controllers\Controller;
use App\Resources\ConHermanoResource;

class InscripcionConHermano extends Controller
{
    public function index($ciclo, $centro_id=null, $curso_id=null)
    {
        $models = [
            'inscripcion.hermano.persona',
            'inscripcion.hermano.familiares.familiar.persona',
            'inscripcion.alumno.familiares.familiar.persona'
        ];

        $params = request()->all();
        $default['hermano'] = 'con';

        $default['ciclo'] = $ciclo;
        $default['centro_id'] = $centro_id;
        $default['curso_id'] = $curso_id;

        $default['with'] = join(',',$models);

        /*
        $default['estado_inscripcion'] = 'CONFIRMADA';
        $default['nivel_servicio'] = [
            'Comun - Primario',
            'Comun - Secundario',
        ];
        */

        //Adjunta parametros por default, en los solicitados por request
        $params = array_merge($params,$default);

        // Consumo API Personas
        $api = new ApiConsume();
        $api->get("inscripcion/lista",$params);

        if($api->hasError()) { return $api->getError(); }

        $result = $api->response();

        $data = collect($result['data']);

        // Transforma el resultado, con un formato pre-establecido por el resource
        $alumnos = ConHermanoResource::collection($data);

        if(count($alumnos)>0)
        {
            if(request('exportar'))
            {
                $toExport = $this->prepareExport($alumnos);
                Export::toExcel('AlumnosConHermano','Lista de Alumnos con Hermanos',$toExport);
            } else {
                return $alumnos;
            }
        } else {
            return ['error' => 'No se obtuvieron resultados con el filtro aplicado'];
        }
    }

    private function prepareExport($data)
    {
        $resultados = collect($data);

        $excelSheet = [
            'Alumno.ciudad',
            'Alumno.centro',
            'Alumno.documento_nro',
            'Alumno.nombre',
            'Alumno.fecha_nacimiento',
            'Alumno.telefono_nro',
            'Alumno.direccion',
            'Alumno.familiares',

            'Hermano.documento_nro',
            'Hermano.nombre',
            'Hermano.fecha_nacimiento',
            'Hermano.telefono_nro',
            'Hermano.direccion',
            'Hermano.familiares',
        ];

        $excelData = $resultados->map(function($v) {
            $inscripcion = $v['inscripcion'];
            $hermano = $v['hermano'];

            return [
                $inscripcion['ciudad'],
                $inscripcion['centro'],
                $inscripcion['documento_nro'],
                $inscripcion['nombre_completo'],
                $inscripcion['fecha_nac'],
                $inscripcion['telefono_nro'],
                $inscripcion['direccion'],
                collect($inscripcion['familiares'])->implode(' - '),

                $hermano['documento_nro'],
                $hermano['nombre_completo'],
                $hermano['fecha_nac'],
                $hermano['telefono_nro'],
                $hermano['direccion'],
                collect($inscripcion['familiares'])->implode(' - '),
            ];

        })->toArray();

        array_unshift($excelData,$excelSheet);
        return $excelData;
    }
}