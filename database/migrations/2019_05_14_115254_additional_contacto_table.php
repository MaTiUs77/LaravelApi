<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionalContactoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacto', function (Blueprint $table) {
            // Se agregan campos de username y correo para facilitar las respuestas
            $table->string('username')->nullable()->after('message');
            $table->string('email')->nullable()->after('username');
            $table->boolean('answered')->default(false)->after('origin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('contacto', 'username') && 
            Schema::hasColumn('contacto', 'email') && 
            Schema::hasColumn('contacto', 'answered')) {
            Schema::table('contacto', function (Blueprint $table) {
                $table->dropColumn(['username','email','answered']);
            });
        }
    }
}
