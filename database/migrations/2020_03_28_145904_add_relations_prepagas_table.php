<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationsPrepagasTable extends Migration
{
    public function up()
    {

        Schema::table('prepagas', function (Blueprint $table) {
            $table->integer('paciente_id')->unsigned()->nullable()->change();
            $table->foreign('paciente_id')->references('id')->on('pacientes');
        });
    }

    public function down()
    {
        Schema::table('prepagas', function (Blueprint $table) {
            $table->dropForeign(['paciente_id']);
        });
    }
}
