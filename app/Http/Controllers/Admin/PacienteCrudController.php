<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PacienteRequest;
use App\Models\Historiaclinica;
use App\Models\Paciente;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use DebugBar\DebugBar;

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
            'type' => 'Text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('nombre', 'like', '%' . $searchTerm . '%');
            }
        ]);
        $this->crud->addColumn([
            'name' => 'apellido', // The db column name
            'label' => "Apellido", // Table column heading
            'type' => 'Text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('apellido', 'like', '%' . $searchTerm . '%');
            }
        ]);
        $this->crud->addColumn([
            'name' => 'nro_doc', // The db column name
            'label' => "Numero Documento", // Table column heading
            'type' => 'Text',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhere('nro_doc', 'like', $searchTerm . '%');
            }
        ]);
        
        $this->crud->addColumn([
            'name' => 'edad', // The db column name
            'label' => "Edad", // Table column heading
            'type' => 'closure',
            'function' => function ($entry) {
                return $entry->calcular_edad()." años <small>(".$entry->fecha_nacimiento.")</small>";
            }
        ]);
        
        $this->crud->addColumn([
            'name' => 'telefono', // The db column name
            'label' => "Teléfono", // Table column heading
            'type' => 'text',
        ]);

        $this->crud->enableExportButtons();
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(PacienteRequest::class);

        //dd(Paciente::latest()->first()->id);

        $this->crud->addField([
            'name' => 'num_hc',
            'type' => 'text',
            'label' => "Número de Historia Clínica",
            'attributes' => []
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<h5><i class="fa fa-dot-circle-o"></i> Datos personales</h5>'
        ]);

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
            'name' => 'sexo',
            'type' => 'enum',
            'label' => "Sexo",
            'attributes' => [
                'required' => 'required'
            ]
        ]);
        $this->crud->addField([
            'name' => 'fecha_nacimiento',
            'type' => 'date',
            'label' => "Fecha de nacimiento",
            'attributes' => [
                'required' => 'required'
            ],
        ]);
        $this->crud->addField([
            'name' => 'tipo_doc',
            'type' => 'enum',
            'label' => "Tipo de documento",
            'attributes' => [
                'required' => 'required'
            ]
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
        $this->crud->addField([
            'name' => 'estado_civil',
            'type' => 'enum',
            'label' => "Estado Civil",
            'attributes' => [
                'required' => 'required'
            ]
        ]);
        $this->crud->addField([
            'name' => 'telefono',
            'type' => 'text',
            'label' => "Teléfono de contacto",
            'attributes' => []
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
        );

        $this->crud->addField([
            'name' => 'num_afiliado',
            'label' => 'Número de afiliado',
            'type' => 'text'
        ]);

        $this->crud->addField([   // CustomHTML
            'name' => 'separator',
            'type' => 'custom_html',
            'value' => '<hr><h5><i class="fa fa-dot-circle-o"></i> Antecedentes</h5>'
        ]);

        $this->crud->addField([
            'name' => 'edad_primer_rs',
            'label' => 'Edad de primera relación sexual',
            'type' => 'number',
            'attributes' => [
                'step' => 1,
                'min' => 0,
                'max' => 100
            ]
        ]);
        $this->crud->addField([
            'name' => 'menarca',
            'label' => 'Edad de menarca',
            'type' => 'number',
            'attributes' => [
                'step' => 1,
                'min' => 0,
                'max' => 100
            ]
        ]);

        $this->crud->addField([
            'name' => 'ritmo',
            'label' => 'Ritmo menstrual',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'alergias',
            'label' => 'Alergias que padece',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'mac',
            'label' => 'MAC',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'cirugias',
            'label' => 'Cirugias',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'enfermedades',
            'label' => 'Enfermedades',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'antecedente_personal',
            'label' => 'Antecedentes personales',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'antecedente_familiar',
            'label' => 'Antecedentes familiares',
            'type' => 'text',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'tabaquista',
            'label' => '¿Consume tabaco?',
            'type' => 'enum',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'alcohol',
            'label' => '¿Consume alcohol?',
            'type' => 'enum',
            'attributes' => []
        ]);

        $this->crud->addField([
            'name' => 'drogas',
            'label' => '¿Consume drogas?',
            'type' => 'enum',
            'attributes' => []
        ]);

        $this->crud->removeField('observacion');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
