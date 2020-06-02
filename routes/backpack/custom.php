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
}); // this should be the absolute last line of this file