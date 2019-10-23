<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasesTrazabilidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('pases_trazabilidad');

        Schema::create('pases_trazabilidad', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('trazabilidad_id');

            $table->integer('id');
            $table->string('pase_nro')->nullable(); // Legajo concatenado XXXXX/0000
            $table->integer('inscripcion_id'); // Origen
            $table->integer('centro_id'); // Origen

            // Luego de confirmar y genegar la inscripcion por pase en destino
            $table->integer('centro_id_destino')->nullable(); // Destino
            $table->integer('inscripcion_id_destino')->nullable();

            // Destino solicitado por el tutor
            $table->integer('centro_id_destino_a');
            $table->integer('centro_id_destino_b');
            $table->string('anio');

            $table->boolean('nota_pase_tutor')->default(false);

            $table->enum('tipo',['ingreso','egreso','dentro'])->nullable(); // De la provincia
            $table->string('motivo')->nullable(); //

            $table->enum('estado_documentacion',['pendiente','completa'])->default('pendiente');
            $table->enum('estado',['iniciado','evaluacion','confirmado','rechazado','vencido'])->default('iniciado');

            $table->string('observaciones')->nullable();
            $table->integer('user_id');
            $table->integer('familiar_id'); // Familiar encargado de gestionar el pase
            $table->date('fecha_vencimiento');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pases_trazabilidad');
    }
}
