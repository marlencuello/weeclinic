<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurneroTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turnero', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('inicio-turno');
            $table->datetime('fin-turno');
            $table->integer('user_id');
            $table->integer('paciente_id');
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
        Schema::dropIfExists('turnero');
    }
}
