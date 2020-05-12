<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Paciente extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'pacientes';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'nombre',
        'apellido',
        'tipo_doc',
        'nro_doc',
        'observacion[]',
        'num_hc',
        'sexo',
        'fecha_nacimiento',
        'estado_civil',
        'telefono',
        'num_afiliado',
        'edad_primer_rs',
        'menarca',
        'ritmo',
        'alergias',
        'mac',
        'cirugias',
        'enfermedades',
        'antecedente_personal',
        'antecedente_familiar',
        'tabaquista',
        'alcohol',
        'drogas'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function calcular_edad()
    {
        //$myDate = '1995-07-02';
        $edad = Carbon::parse($this->fecha_nacimiento)->age;
        return $edad;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function prepagas()
    {
        return $this->belongsTo('App\Models\Prepaga', 'prepaga_id');
    }

    public function historiaClinica()
    {
        return $this->hasMany('App\Models\HistoriaClinica');
    }

    public function archivos() {
        return $this->belongsToMany('\App\Models\Archivo','archivo_paciente')->withPivot('paciente_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(){
        return $this->apellido.', '.$this->nombre;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
