<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PacienteRequest;
use App\Models\Historiaclinica;
use App\Models\Paciente;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Route;

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

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupHistoriaClinicaDefaults()
    {
        $this->crud->allowAccess('HistoriaClinica');

        $this->crud->addField([
            'name' => 'observacion',
            'label' => 'Nueva observación',
            'type' => 'tinymce',
        ]);

        /*$this->crud->addField([   // Upload
            'name' => 'archivo',
            'label' => 'Archivo (opcional)',
            'type' => 'upload',
            'upload' => true,
            'disk' => 'uploads', // if you store files in the /public folder, please ommit this; if you store them in /storage or S3, please specify it;
            // optional:
            'temporary' => 10 // if using a service, such as S3, that requires you to make temporary URL's this will make a URL that is valid for the number of minutes specified
        ]);*/

        $this->crud->operation('HistoriaClinica', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('line', 'HistoriaClinica', 'view', 'crud::buttons.historia_clinica');
        });
    }

    protected function setupHistoriaClinicaRoutes($segment, $routeName, $controller)
    {
        //dd($routeName.'.historiaClinica');
        //dd($controller.'.historiaClinica');
        Route::get($segment . '/{id}/historia_clinica', [
            'as'        => $routeName . '.historiaClinica',
            'uses'      => $controller . '@historiaClinica',
            'operation' => 'historiaClinica',
        ]);

        Route::put($segment . '/{id}/historia_clinica', [
            'as'        => $routeName . '.saveHistoriaClinica',
            'uses'      => $controller . '@saveHistoriaClinica',
            'operation' => 'historiaClinica',
        ]);
    }

    public function historiaClinica($id)
    {
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        $pacientes = Paciente::where(array('id' => $id))->with('historiaClinica')->get();
        $paciente = $pacientes[0];
        $this->data['title'] = $this->crud->getTitle() ?? 'Historia clinica ' . $this->crud->entity_name;
        //$this->crud->setOperationSetting('fields', $this->crud->getUpdateFields());

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['saveAction'] = $this->crud->getSaveAction();
        $this->data['urlSave'] = "/$id/historia_clinica/";
        $this->data['paciente'] = $paciente;

        $this->data['id'] = $id;

        // load the view
        return view("crud::paciente.historia_clinica", $this->data);
    }
    public function saveHistoriaClinica(Request $request = null)
    {
        $this->crud->hasAccessOrFail('update');

        \Alert::success('Moderation saved for this entry.')->flash();

        return \Redirect::to($this->crud->route);
    }
}
