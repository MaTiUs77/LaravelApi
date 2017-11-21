<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Productos\Model\ProductosModel;
use App\Http\Controllers\Ventas\Model\VentasModel;
use App\Http\Controllers\Ventas\ResumenDeVenta;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth');
    }
    
    public function index()
    {
        return ['api'=>'online'];
    }
}
