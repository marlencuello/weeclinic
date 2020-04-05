@if ($crud->hasAccess('update'))
<a href="{{ url($crud->route.'/'.$entry->getKey().'/paciente_hc') }} " class="btn btn-xs btn-light"><i class="fa fa-folder"></i> Historia Clinica</a>
@endif