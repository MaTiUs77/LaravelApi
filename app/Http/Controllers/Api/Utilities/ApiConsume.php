<?php

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ApiConsume extends Controller
{
    private $host;
    private $version;

    private $route;
    private $consume_route;

    private $error;
    private $response;
    private $headers = [];

    public function __construct($host=null,$version=null)
    {
        if(!$host) {
            $this->host = env('SIEP_LARAVEL_API');
        }
        $this->version = $version='api/v1';
        $this->cakeHeader();
    }

    public function hasError() {
        if($this->error!=null) {
            Log::error([$this->consume_route,$this->error]);
            return true;
        } else {
            return false;
        }
    }
    public function getError() {
        return $this->error;
    }
    public function getResponse() {
        return $this->response;
    }
    public function response() {
        return $this->response;
    }

    public function cakeHeader() {
        $this->headers = [
            'headers' => [
                env('XHOSTCAKE') => 'do'
            ]
        ];
    }

    private function generateUri($route) {
        $this->route = $route;

        $this->consume_route = join('/',[
            $this->host,
            $this->version,
            $this->route
        ]);
    }

    public function get($route,$params=[]) {
        $this->generateUri($route);

        // Consume API lista de inscripciones
        try {
            $guzzle = new Client($this->headers);
            $consumeApi = $guzzle->get($this->consume_route,['query' => $params]);

            // Obtiene el contenido de la respuesta, la transforma a json
            $content = $consumeApi->getBody()->getContents();
            $req = json_decode($content,true);
        } catch (BadResponseException $ex) {
            $content = $ex->getResponse();
            $jsonBody = json_decode($content->getBody(), true);

            $jsonBody['api_request'] = 'complete';
            $this->error =  $jsonBody;

            return $this;
        }

        if(isset($req['error'])) {
            $req['api_consume'] = 'complete';
            $this->error = $req;
        } else {
            $this->response = $req;
        }

        return $this;
    }
}