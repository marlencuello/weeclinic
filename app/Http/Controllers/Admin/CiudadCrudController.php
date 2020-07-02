<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CiudadRequest;
use App\Models\Provincia;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CiudadCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CiudadCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Ciudad');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/ciudad');
        $this->crud->setEntityNameStrings('ciudad', 'ciudades');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'nombre_provincia', // The db column name
            'label' => "Provincia", // Table column heading
            'type' => 'closure',
            'function' => function ($entry) {
                $provincia = Provincia::find($entry->provincia);
                return $provincia->nombre;
            },
            'model' => 'App\Models\Provincia',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->whereHas('provincias', function ($q) use ($column, $searchTerm) {
                    $q->where('nombre', 'like', '%' . $searchTerm . '%');
                });
            }
        ]);
        $this->crud->addColumn([
            'name' => 'nombre',
            'type' => 'text',
            'label' => 'Ciudad'
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(CiudadRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        $this->crud->setFromDb();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
