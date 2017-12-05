<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Productos\Model\ProductosModel;
use App\Http\Controllers\Ventas\Model\VentasModel;
use App\Http\Controllers\Ventas\ResumenDeVenta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }
    
    public function index()
    {
        $motor = 'laravel';
        $version = app()::VERSION;
        $github = env('GITHUB');
        $api_tag = env('API_TAG');
        $server_time = Carbon::now();

        return compact('motor','version','github','api_tag','server_time');
    }
}
