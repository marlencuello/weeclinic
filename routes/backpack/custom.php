<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('prepaga', 'PrepagaCrudController');
    Route::crud('paciente', 'PacienteCrudController');
    //Route::get('paciente/{id}/historia_clinica', 'PacienteCrudController@historiaClinica');
    //Route::post('paciente/{id}/historia_clinica', 'PacienteCrudController@historiaClinica');
    Route::crud('historiaclinica', 'HistoriaclinicaCrudController');
    Route::crud('archivo', 'ArchivoCrudController');
    Route::crud('turnero', 'TurneroCrudController');
    Route::get('api/turnos', 'Api\TurnosController@index');
    Route::get('api/turnos/{id}', 'Api\TurnosController@show');
    Route::crud('provincia', 'ProvinciaCrudController');
    Route::crud('ciudad', 'CiudadCrudController');
    //IMPORTACIONES DE SISTEMA ANTERIOR
    Route::get('paciente/importar', 'PacienteCrudController@importarPacientes');
    Route::get('historiaclinica/importar', 'HistoriaclinicaCrudController@importarHC');
}); // this should be the absolute last line of this file