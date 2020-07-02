<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use DebugBar\DebugBar;
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
        'grupo_sanguineo',
        'factor',
        'fecha_nacimiento',
        'estado_civil',
        'telefono',
        'email',
        'num_afiliado',
        'edad_primer_rs',
        'menarca',
        'ritmo',
        'paridad',
        'alergias',
        'mac',
        'cirugias',
        'enfermedades',
        'antecedente_personal',
        'antecedente_familiar',
        'tabaquista',
        'alcohol',
        'drogas',
        'imagenes'
    ];
    protected $casts = [
        'imagenes' => 'array'
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
        $edad = Carbon::parse($this->fecha_nacimiento)->age;
        return $edad;
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function($obj) {
            if (count((array)$obj->imagenes)) {
                foreach ($obj->imagenes as $file_path) {
                    \Storage::disk('public_folder')->delete($file_path);
                }
            }
        });
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

    public function turno()
    {
        return $this->belongsToMany('App\Models\Turnero', 'paciente_id');
    }

    public function historiaClinica()
    {
        return $this->hasMany('App\Models\HistoriaClinica');
    }

    /*public function archivos() {
        return $this->belongsToMany('\App\Models\Archivo','archivo_paciente')->withPivot('paciente_id');
    }*/

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

    public function setImagenesAttribute($value)
    {
        $attribute_name = "imagenes";
        $disk = "uploads";
        $destination_path = "pacientes";
        //dd($value);
        $this->uploadMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
