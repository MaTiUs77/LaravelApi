<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function home()
    {
        try {
            $master = json_decode(file_get_contents('http://localhost/master.json'));
            $dev = json_decode(file_get_contents('http://localhost/developer.json'));

            $github = [
                'master' => [
                    'commit' => substr($master->sha,0,7),
                    'sha' => $master->sha,
                    'message' => $master->commit->message
                ],
                'developer' => [
                    'commit' => substr($dev->sha,0,7),
                    'sha' => $dev->sha,
                    'message' => $dev->commit->message
                ]
            ];

        } catch(\Exception $ex)
        {
            $github = ['error'=>'Error al cargar informacion'];
        }

        $service= 'laravelapi';

        $motor= "Laravel ".app()->version();
        $api_gateway = env('API_GATEWAY');
        $server_time = Carbon::now();

        return compact('service','status','motor','api_gateway','server_time','github');
    }
}
