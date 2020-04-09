<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HistoriaclinicaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Paciente;

/**
 * Class HistoriaclinicaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HistoriaclinicaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Historiaclinica');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/historiaclinica');
        $this->crud->setEntityNameStrings('historiaclinica', 'historiasclinicas');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        //$this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'nombre_paciente', // The db column name
            'label' => "Paciente", // Table column heading
            'type' => 'closure',
            'function' => function ($entry) {
                $paciente = Paciente::find($entry->paciente_id);
                return $paciente['apellido']." ".$paciente['nombre'];
            }
        ]);
        $this->crud->addColumn([
            'name' => 'observacion', 'type' => 'text', 'label' => 'Observación'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(HistoriaclinicaRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
