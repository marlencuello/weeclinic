<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TurneroRequest;
use App\Models\BackpackUser;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Paciente;

/**
 * Class TurneroCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TurneroCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Turnero');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/turnero');
        $this->crud->setEntityNameStrings('turno', 'turnos');
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        //$this->crud->setFromDb();
        $this->crud->addColumn([
            'name' => 'paciente_id', // The db column name
            'label' => "Paciente", // Table column heading
            'type' => 'closure',
            'function' => function ($entry) {
                $paciente = Paciente::find($entry->paciente_id);
                return $paciente['apellido'] . " " . $paciente['nombre'];
            },
            'model' => 'App\Models\Paciente',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->whereHas('pacientes', function ($q) use ($column, $searchTerm) {
                    $q->where('nombre', 'like', '%' . $searchTerm . '%');
                    $q->orWhere('apellido', 'like', '%' . $searchTerm . '%');
                });
            }
        ]);
        $this->crud->addColumn([
            'name' => 'user_id', // The db column name
            'label' => "Profesional", // Table column heading
            'type' => 'closure',
            'function' => function ($entry) {
                $profesional = BackpackUser::find($entry->user_id);
                return $profesional['name'];
            },
            'model' => 'App\Models\BackpackUser',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->whereHas('users', function ($q) use ($column, $searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            }
        ]);
        $this->crud->addColumn([
            'name' => 'user_id', // The db column name
            'label' => "Profesional", // Table column heading
            'type' => 'text',
        ]);
        $this->crud->addColumn([
            'name' => 'inicio_turno', // The db column name
            'label' => "Horario", // Table column heading
            'type' => 'datetime',
        ]);
        //FILTROS DE TABLAS
        $this->crud->addFilter(
            [
                'type'  => 'date',
                'name'  => 'inicio_turno',
                'label' => 'Fecha del turno'
            ],
            false,
            function ($value) { // if the filter is active, apply these constraints
                $this->crud->addClause('where', 'inicio_turno', 'LIKE', $value . '%');
            }
        );
        $this->crud->addFilter([
            'name' => 'user_id',
            'type' => 'select2',
            'label' => 'Profesional'
        ], function () {
            $profesionales = BackpackUser::all();
            $arr_profesionales = array();
            foreach ($profesionales as $v) {
                $arr_profesionales[] = $v->name;
            }
            return $arr_profesionales;
        }, function ($value) { // if the filter is active
            //$this->crud->addClause('where', 'user_id', $value);
        });
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(TurneroRequest::class);

        $this->crud->addField([  // Select2
            'label' => "Profesional",
            'type' => 'select2',
            'name' => 'user_id', // the db column for the foreign key
            'entity' => 'paciente', // the method that defines the relationship in your Model
            'attribute' => 'name', // foreign key attribute that is shown to user
            // optional
            'model' => "App\Models\BackpackUser", // foreign key model
            'options'   => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->addField([
            'name' => 'dia-inicio-turno',
            'type' => 'date',
            'label' => "Elija el día"
        ]);

        $this->crud->addField([
            'label' => "Horario del turno", // Table column heading
            'type' => "select2_from_ajax",
            'name' => 'inicio_turno', // the column that contains the ID of that connected entity
            'entity' => 'turnosDisponibles', // the method that defines the relationship in your Model
            'attribute' => "inicio_turno", // foreign key attribute that is shown to user
            'data_source' => url("admin/api/turnos"),
            'placeholder' => "Elija un horario de inicio",
            'minimum_input_length' => 0,
            //'method' => 'GET',
            //'include_all_form_fields'  => false, // optional - only send the current field through AJAX (for a smaller payload if you're not using multiple chained select2s)
        ]);

        $this->crud->addField([  // Select2
            'label' => "Paciente",
            'type' => 'select2',
            'name' => 'paciente_id', // the db column for the foreign key
            'entity' => 'paciente', // the method that defines the relationship in your Model
            'attribute' => 'FullName', // foreign key attribute that is shown to user
            // optional
            'model' => "App\Models\Paciente", // foreign key model
            'options'   => (function ($query) {
                return $query->orderBy('apellido', 'ASC')->get();
            }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
