<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PacienteRequest;
use App\Models\Historiaclinica;
use App\Models\Paciente;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PacienteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PacienteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \App\Http\Controllers\Admin\Operations\PacienteHCOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Paciente');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/paciente');
        $this->crud->setEntityNameStrings('paciente', 'pacientes');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        //$this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'nombre', // The db column name
            'label' => "Nombre", // Table column heading
            'type' => 'Text'
        ]);
        $this->crud->addColumn([
            'name' => 'apellido', // The db column name
            'label' => "Apellido", // Table column heading
            'type' => 'Text'
        ]);
        $this->crud->addColumn([
            'name' => 'tipo_doc', // The db column name
            'label' => "T. Documento", // Table column heading
            'type' => 'Text'
        ]);
        $this->crud->addColumn([
            'name' => 'nro_doc', // The db column name
            'label' => "N. Documento", // Table column heading
            'type' => 'Text'
        ]);
        $this->crud->enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(PacienteRequest::class);
        // TODO: remove setFromDb() and manually define Fields
        //$this->crud->setFromDb();
        $this->crud->addField([
            'name' => 'nombre',
            'type' => 'text',
            'label' => "Nombre",
            'attributes' => [
                'required' => 'required'
            ]
        ]);
        $this->crud->addField([
            'name' => 'apellido',
            'type' => 'text',
            'label' => "Apellido",
            'attributes' => [
                'required' => 'required'
            ]
        ]);
        $this->crud->addField([
            'name' => 'tipo_doc',
            'type' => 'enum',
            'label' => "Tipo de documento"
        ]);
        $this->crud->addField([
            'name' => 'nro_doc',
            'type' => 'text',
            'label' => "Número de documento",
            'attributes' => [
                'minlength' => '7',
                'maxlength' => '11',
                'required' => 'required'
            ],
        ]);
        $this->crud->addField(
            [  // Select
                'label' => "Seleccione prepaga",
                'type' => 'select',
                'name' => 'prepaga_id', // the db column for the foreign key
                'entity' => 'prepagas', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user

                // optional
                'model' => "App\Models\Prepaga",
                'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ]
        )->afterField('nro_doc');

        $this->crud->removeField('observacion');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
