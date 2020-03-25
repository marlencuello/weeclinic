<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;

trait PacienteHCOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPacienteHCRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/pacientehc', [
            'as'        => $routeName.'.pacientehc',
            'uses'      => $controller.'@pacientehc',
            'operation' => 'pacientehc',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPacienteHCDefaults()
    {
        $this->crud->allowAccess('pacientehc');

        $this->crud->operation('pacientehc', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'pacientehc', 'view', 'crud::buttons.pacientehc');
            //$this->crud->addButton('line', 'pacientehc', 'view', 'crud::buttons.pacientehc');
            $this->crud->addButton('line', 'pacientehc', 'view', 'buttons.pacientehc', 'beginning');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function pacientehc()
    {
        $this->crud->hasAccessOrFail('pacientehc');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'pacientehc '.$this->crud->entity_name;

        // load the view
        return view("crud::operations.pacientehc", $this->data);
    }
}
