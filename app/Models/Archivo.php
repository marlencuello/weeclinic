<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use DebugBar\DebugBar;
use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'archivos';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['nombre', 'extension'];
    // protected $casts = [
    //     'nombre' => 'array'
    // ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setNombreAttribute($value)
    {
        $attribute_name = "nombre";
        $disk = "uploads";
        $destination_path = "";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }
}
