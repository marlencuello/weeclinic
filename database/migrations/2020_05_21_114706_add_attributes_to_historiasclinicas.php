<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesToHistoriasclinicas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historiasclinicas', function (Blueprint $table) {
            $table->boolean('embarazada');
            $table->date('fum')->default('1900-01-01');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historiasclinicas', function (Blueprint $table) {
            $table->dropColumn('embarazada');
            $table->dropColumn('fum');
        });
    }
}
