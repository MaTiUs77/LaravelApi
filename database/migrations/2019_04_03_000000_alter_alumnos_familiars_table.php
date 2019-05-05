<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAlumnosFamiliarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_familiars', function (Blueprint $table) {
            $table->enum('status',['confirmada','pendiente','revisar'])->default('confirmada');
            $table->text('observaciones')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos_familiars', function (Blueprint $table) {
            $table->dropColumn(['status','observaciones']);
        });
    }
}
