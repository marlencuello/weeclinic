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
        $this->crud->setEntityNameStrings('historia clinica', 'historias clinicas');
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
            'name' => 'observacion', 'type' => 'text', 'label' => 'Observación'
        ]);
        $this->crud->addColumn([
            'name' => 'archivos',
            'type' => 'closure',
            'label' => 'Documentación multimedia',
            'function' => function ($entry) {
                if ($entry->archivos != "") {
                    return count($entry->archivos)." archivo/s";
                }
            }
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(HistoriaclinicaRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        //$this->crud->setFromDb();
        $this->crud->addField([  // Select
            'label' => "Paciente",
            'type' => 'select',
            'name' => 'paciente_id', // the db column for the foreign key
            'entity' => 'pacientes', // the method that defines the relationship in your Model
            'attribute' => 'FullName', // foreign key attribute that is shown to user
        ]);
        $this->crud->addField(
            [
                'name' => 'observacion',
                'label' => 'Observación',
                'type' => 'ckeditor',
                'options' => [
                    'removePlugins' => '',
                    'removeButtons' => 'About,Strike,Maximize,ShowBlocks,BGColor,FontSize,Font,Format,Styles,Image,Flash,Table,HorizontalRule, Smiley, SpecialChar, PageBreak, Iframe,Link,Unlink,Anchor,NumberedList,Outdent,Indent,Blockquote,CreateDiv,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,BidiLtr,BidiRtl,Language,Source'
                ]
            ]
        );
        $this->crud->addField(
            [   // Upload
                'name' => 'archivos',
                'label' => 'Archivos',
                'type' => 'upload_multiple',
                'upload' => true,
                'disk' => 'uploads', // if you store files in the /public folder, please ommit this; if you store them in /storage or S3, please specify it;
                // optional:
                //'temporary' => 10 // if using a service, such as S3, that requires you to make temporary URL's this will make a URL that is valid for the number of minutes specified
            ]
        );
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
