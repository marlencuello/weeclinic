<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('prepaga_id')->nullable();
            $table->string('nombre');
            $table->string('apellido');
            $table->enum('tipo_doc',['DNI', 'LC', 'LE']);
            $table->char('nro_doc', 8);
            $table->timestamps();
            //RELACIONES ENTRE TABLAS
            $table->foreign('prepaga_id')->references('id')->on('prepagas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
}
