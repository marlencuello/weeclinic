@extends(backpack_view('layouts.top_left'))

@php
$defaultBreadcrumbs = [
trans('backpack::crud.admin') => backpack_url('dashboard'),
$crud->entity_name_plural => url($crud->route),
'Historia Clinica' => false,
];

// if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
<section class="container-fluid">
	<h2>
		<span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name !!} {!! $crud->entry->apellido !!}
			{!! $crud->entry->nombre !!}</span>
		<small>Historia clinica</small>

		@if ($crud->hasAccess('list'))
		<small><a href="{{ url($crud->route) }}" class="hidden-print font-sm"><i class="fa fa-angle-double-left"></i>
				{{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
		@endif
	</h2>
</section>
<div class="container-fluid animated fadeIn">
	<div class="card">
		<div class="card-body row">
			<div class="col-4">
				
			</div>
			<div class="col-4"></div>
			<div class="col-4"></div>
		</div>
	</div>
</div>

@endsection

@section('content')
<div class="row">
	<div class="{{ $crud->getEditContentClass() }}">
		<!-- Default box -->

		@include('crud::inc.grouped_errors')

		<!--<form method="post" action="{{ url($crud->route.'/'.$entry->getKey()) }}" @if ($crud->hasUploadFields('update',
			$entry->getKey())) enctype="multipart/form-data"
			@endif
			>-->
		<form method="post" action="{{ url($crud->route.'/'.$entry->getKey()) }}/paciente_hc" @if ($crud->
			hasUploadFields('update', $entry->getKey()))
			enctype="multipart/form-data"
			@endif
			>
			{!! csrf_field() !!}
			{!! method_field('PUT') !!}
			@if ($crud->model->translationEnabled())
			<div class="mb-2 text-right">
				<!-- Single button -->
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown"
						aria-haspopup="true" aria-expanded="false">
						{{trans('backpack::crud.language')}}:
						{{ $crud->model->getAvailableLocales()[$crud->request->input('locale')?$crud->request->input('locale'):App::getLocale()] }}
						&nbsp; <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						@foreach ($crud->model->getAvailableLocales() as $key => $locale)
						<a class="dropdown-item"
							href="{{ url($crud->route.'/'.$entry->getKey().'/historiaClinica') }}?locale={{ $key }}">{{ $locale }}</a>
						@endforeach
					</ul>
				</div>
			</div>
			@endif
			<!-- load the view from the application if it exists, otherwise load the one in the package -->
			@if(view()->exists('vendor.backpack.crud.form_content'))
			@include('vendor.backpack.crud.form_content', ['fields' => $crud->fields(), 'action' =>
			'saveHistoriaClinica'])
			@else
			<div class="card">
				<div class="card-body row">
					@foreach ($paciente->historiaclinica as $v => $item)
					<div class="form-group col-sm-12">
						<label for="">
							Última modificación
							@if($item->updated_at)
							{{$item->updated_at}}
							@else
							{{$item->created_at}}
							@endif
						</label>
						<textarea name="observacion_id[{{$item->id}}]" id="ckeditor-observacion_id_{{$item->id}}"
							class="form-control sr-only"
							data-init-function="bpFieldInitCKEditorElement">{{$item->observacion}}</textarea>
					</div>
					@endforeach
				</div>
			</div>
			<?php //dd($crud->fields()); ?>
			@include('crud::form_content', ['fields' => $crud->fields(), 'action' => 'edit'])
			<div class="hidden">
				<input type="hidden" value="{{$paciente->id}}" name="id" class="form-control">
			</div>
			@endif

			@include('crud::inc.form_save_buttons')
		</form>
	</div>
</div>
@endsection