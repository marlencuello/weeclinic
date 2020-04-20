@if ($crud->hasAccess('update'))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/paciente_hc') }} " class="btn btn-sm btn-light mr-1"><i class="fa fa-folder"></i> Historia Clinica</a>
@endif