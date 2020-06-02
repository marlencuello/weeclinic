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
                return $entry->calcular_edad() . " años <small>(" . $entry->fecha_nacimiento . ")</small>";
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
        $this->crud->addField([
            'name' => 'email',
            'type' => 'email',
            'label' => "Correo electrónico",
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

        $this->crud->addField(
            [   // Upload
                'name' => 'imagenes',
                'label' => 'Imágenes',
                'type' => 'upload_multiple',
                'upload' => true,
                'disk' => 'uploads', // if you store files in the /public folder, please ommit this; if you store them in /storage or S3, please specify it;
                // optional:
                //'temporary' => 10 // if using a service, such as S3, that requires you to make temporary URL's this will make a URL that is valid for the number of minutes specified
            ]
        );

        $this->crud->removeField('observacion');
        $this->crud->removeField('embarazada');
        $this->crud->removeField('fum');
        $this->crud->removeField('archivos');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        $this->crud->set('show.setFromDb', false);
        $this->crud->addColumn([
            'name' => 'nombre',
            'label' => 'Nombre',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'apellido',
            'label' => 'Apellido',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'num_hc',
            'label' => 'Número de historia clínica',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'sexo',
            'label' => 'Sexo',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'fecha_nacimiento',
            'label' => 'Fecha de nacimiento',
            'type' => 'date'
        ]);
        $this->crud->addColumn([
            'name' => 'tipo_doc',
            'label' => 'Tipo de documento',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'nro_doc',
            'label' => 'Número de documento',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'estado_civil',
            'label' => 'Estado civil',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'telefono',
            'label' => 'Teléfono de contacto',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'email',
            'label' => 'Correo electrónico',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'prepaga', // the db column for the foreign key
            'type' => 'closure',
            'function' => function ($entry) {
                $prepaga = "";
                if($entry->prepagas()->first()) {
                    $prepaga = $entry->prepagas()->first()->name;
                }
                return $prepaga;
            }
        ]);
        $this->crud->addColumn([
            'name' => 'num_afiliado',
            'label' => 'Número de afiliado',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'menarca',
            'label' => 'Edad de menarca',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'ritmo',
            'label' => 'Ritmo',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'edad_primer_rs',
            'label' => 'Edad de Primera Relación Sexual',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'paridad',
            'label' => 'Paridad',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'mac',
            'label' => 'MAC',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'alergias',
            'label' => 'Alergias',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'cirugias',
            'label' => 'Cirugias',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'enfermedades',
            'label' => 'Enfermedades que padece',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'antecedente_personal',
            'label' => 'Antedecentes personales',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'antecedente_familiar',
            'label' => 'Antedecentes familiares',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'tabaquista',
            'label' => 'Tabaquista',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'alcohol',
            'label' => 'Consume alcohol',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'drogas',
            'label' => 'Consume drogas',
            'type' => 'text'
        ]);
        $this->crud->addColumn([
            'name' => 'imagenes', // The db column name
            'label' => "Imágenes", // Table column heading
            'type' => 'upload_multiple',
            'disk' => 'uploads',
        ]);
    }
}
