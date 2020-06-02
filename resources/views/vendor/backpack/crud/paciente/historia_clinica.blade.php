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
			<div class="col-12 col-md-4">
				N° Historia Clinica: <strong>{!! $crud->entry->num_hc !!}</strong>
				<br />
				DNI: <strong>{!! $crud->entry->nro_doc !!}</strong>
				<br />
				Sexo: <strong>{!! $crud->entry->sexo !!}</strong>
				<br />
				Edad: <strong>{!! $crud->entry->calcular_edad() !!} años</strong> <small>({!!
					$crud->entry->fecha_nacimiento !!})</small>
				<br />
				Prepaga:
				@if(isset($crud->entry->prepagas()->first()->name))
				<strong>{{$crud->entry->prepagas()->first()->name}}</strong>
				@else
				Ninguna
				@endif
				<br />
				N° Afiliado: <strong>{!! $crud->entry->num_afiliado !!}</strong>
				<br />
				Teléfono: <strong>{!! $crud->entry->telefono !!}</strong>
				<br />
				Correo electrónico: <strong>{!! $crud->entry->email !!}</strong>
			</div>
			<div class="col-12 col-md-4">
				Tabaquista: <strong>{!! $crud->entry->tabaquista !!}</strong>
				<br />
				Consume alcohol: <strong>{!! $crud->entry->alcohol !!}</strong>
				<br />
				Consume drogas: <strong>{!! $crud->entry->drogas !!}</strong>
				<br />
				Primera R. sexual: <strong>{!! $crud->entry->edad_primera_rs !!}</strong>
				<br />
				Menarca: <strong>{!! $crud->entry->menarca !!}</strong>
				<br />
				Ritmo: <strong>{!! $crud->entry->ritmo !!}</strong>
				<br />
				MAC: <strong>{!! $crud->entry->mac !!}</strong>
				<br />
				Paridad: <strong>{!! $crud->entry->paridad !!}</strong>
			</div>
			<div class="col-12 col-md-4">
				Alergias: <strong>{!! $crud->entry->alergias !!}</strong>
				<br />
				Cirugias: <strong>{!! $crud->entry->cirugias !!}</strong>
				<br />
				Enfermedades: <strong>{!! $crud->entry->enfermedades !!}</strong>
				<br />
				Antecedentes personales: <strong>{!! $crud->entry->antecedente_personal !!}</strong>
				<br />
				Antecedentes familiares: <strong>{!! $crud->entry->antecedente_familiar !!}</strong>
			</div>
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
				<div class="card-body pt-2 row">
					@foreach ($paciente->historiaclinica as $v => $item)
					<div class="col-sm-12 pb-1 mb-2 border border-info rounded">
						<div class="row mt-1">
							<div class="col-6 p-1">
								<i class="fa fa-clock-o"></i> <small>Realizada el
									@if($item->updated_at)
									{{$item->updated_at}}
									@else
									{{$item->created_at}}
									@endif
								</small>
							</div>
							<div class="col-6 p-1 text-right">
								<a href="/admin/historiaclinica/{{$item->id}}/edit" class="btn btn-sm btn-primary btn-editar-hc">Editar</a>
							</div>
						</div>
						<div class="col-12 p-1 font-110">{!!$item->observacion!!}</div>
					<div class="row bg-light">
						<div class="col-6 pr-3 pl-3">FUM: 
							@if(($item->fum != "1900-01-01") && ($item->fum != ""))
								{{$item->fum}}
							@else
								No registrada
							@endif
						</div>
						<div class="col-6 text-danger">
							@if($item->embarazada)
								<i class="fa fa-info-circle"></i> Embarazada
							@endif
						</div>
					</div>
						@if($item->archivos)
						<div class="row m-1 pt-2 border-top border-info">
							@foreach($item->archivos as $media)
							<div class="col-12 col-md-2 pl-1 pr-1">
								<figure class="figure">
									<a href="/uploads/{{$media}}" target="_blank" class="">
										<img src="/uploads/{{$media}}" class="figure-img img-fluid rounded"
											alt="imágen de historia clinica" />
									</a>
								</figure>
							</div>
							@endforeach
						</div>
						@endif
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