<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtributosToPacientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            //DATOS PERSONALES
            $table->char('num_hc', 20);
            $table->enum('sexo', ['Femenino', 'Masculino']);
            $table->date('fecha_nacimiento')->default('1900-01-01');
            $table->enum('estado_civil', ['Soltero/a', 'Casado/a']);
            $table->char('telefono', 15)->nullable();
            $table->char('num_afiliado', 20)->nullable();
            //ANTECEDENTES PERSONALES
            $table->smallInteger('edad_primer_rs')->nullable();
            $table->smallInteger('menarca')->nullable();
            $table->char('ritmo', 4)->nullable();
            $table->string('alergias', 200)->nullable();
            $table->string('mac', 100)->nullable();
            $table->string('cirugias', 200)->nullable();
            $table->string('enfermedades', 200)->nullable();
            $table->string('antecedente_personal', 200)->nullable();
            $table->string('antecedente_familiar', 200)->nullable();
            $table->enum('tabaquista', ['No', 'Si']);
            $table->enum('alcohol', ['No', 'Si']);
            $table->enum('drogas', ['No', 'Si']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn('num_hc');
            $table->dropColumn('sexo');
            $table->dropColumn('fecha_nacimiento');
            $table->dropColumn('estado_civil');
            $table->dropColumn('telefono');
            $table->dropColumn('num_afiliado');
            $table->dropColumn('edad_primer_rs');
            $table->dropColumn('menarca');
            $table->dropColumn('ritmo');
            $table->dropColumn('alergias');
            $table->dropColumn('mac');
            $table->dropColumn('cirugias');
            $table->dropColumn('enfermedades');
            $table->dropColumn('antecedente_personal');
            $table->dropColumn('antecedente_familiar');
            $table->dropColumn('tabaquista');
            $table->dropColumn('alcohol');
            $table->dropColumn('drogas');
        });
    }
}
