<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Productos\Model\ProductosModel;
use App\Http\Controllers\Ventas\Model\VentasModel;
use App\Http\Controllers\Ventas\ResumenDeVenta;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('apihome');
    }
}
