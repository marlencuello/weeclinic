<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Historiaclinica;
use Illuminate\Support\Facades\Route;
use App\Models\Paciente;
use Illuminate\Support\Facades\Request;

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
        Route::get($segment . '/{id}/paciente_hc', [
            'as'        => $routeName . '.paciente_hc',
            'uses'      => $controller . '@PacienteHc',
            'operation' => 'PacienteHC',
        ]);

        Route::put($segment . '/{id}/paciente_hc', [
            'as'        => $routeName . '.savepaciente_hc',
            'uses'      => $controller . '@savePacienteHc',
            'operation' => 'PacienteHC',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPacienteHCDefaults()
    {
        $this->crud->allowAccess('PacienteHC');

        $this->crud->addField([
            'name' => 'observacion',
            'label' => 'Nueva observación',
            'type' => 'ckeditor',
            'options' => [
                'removePlugins' => '',
                'removeButtons' => 'About,Strike,Maximize,ShowBlocks,BGColor,FontSize,Font,Format,Styles,Image,Flash,Table,HorizontalRule, Smiley, SpecialChar, PageBreak, Iframe,Link,Unlink,Anchor,NumberedList,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Source'
            ]
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

        $this->crud->operation('PacienteHC', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('line', 'PacienteHC', 'view', 'crud::buttons.historia_clinica');
        });
    }

    public function PacienteHc($id)
    {
        $this->crud->hasAccessOrFail('PacienteHC');
        $this->crud->setOperation('PacienteHC');
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
        $this->data['urlSave'] = "/$id/paciente__hc/";
        $this->data['paciente'] = $paciente;

        $this->data['id'] = $id;

        //dd($this->data);

        // load the view
        return view("crud::paciente.historia_clinica", $this->data);
    }

    public function savePacienteHc(Request $request = null)
    {
        $this->crud->hasAccessOrFail('PacienteHC');
        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        $form = $request->request->all();
        //dd($form);
        foreach ($form['observacion_id'] as $k => $observacion) {
            $historia_clinica = Historiaclinica::updateOrCreate(
                ['id' => $k],
                ['observacion' => $observacion]
            );
        }

        if ($form['observacion'] != "") {
            $hc = new Historiaclinica;
            $hc->observacion = $form['observacion'];
            $hc->paciente_id = $request->get($this->crud->model->getKeyName());
            $hc->save();
        }

        \Alert::success('Historia clinica actualizada')->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return \Redirect::to($this->crud->route);
    }
}
