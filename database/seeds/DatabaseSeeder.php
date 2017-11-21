<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Marcas\Model\MarcasModel;
use App\Http\Controllers\Almacenes\Model\AlmacenesModel;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $this->call(UsersSeeder::class);
  }
}
