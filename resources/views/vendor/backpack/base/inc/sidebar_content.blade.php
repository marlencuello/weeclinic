<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard nav-icon"></i>
		{{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon fa fa-files-o"></i>
		<span>{{ trans('backpack::crud.file_manager') }}</span></a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('prepaga') }}'><i class='nav-icon fa fa-credit-card'></i>
		Prepagas</a></li>
@if(backpack_user()->hasPermissionTo('CRUD Opciones', 'web'))
	<li class='nav-item'><a class='nav-link' href='{{ backpack_url('setting') }}'><i class='nav-icon fa fa-cog'></i>
		Opciones Generales</a></li>
@endif
<li class="nav-item nav-dropdown">
	<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon fa fa-group"></i> Autentificación</a>
	<ul class="nav-dropdown-items">
		<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon fa fa-user"></i>
				<span>Usuarios</span></a></li>
		<li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon fa fa-group"></i>
				<span>Roles</span></a></li>
		<li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i
					class="nav-icon fa fa-key"></i> <span>Permisos</span></a></li>
	</ul>
</li>
@if(backpack_user()->hasPermissionTo('CRUD Pacientes', 'web'))
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('paciente') }}'><i class='nav-icon fa fa-user'></i>
	Pacientes</a></li>
@endif
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('historiaclinica') }}'><i
			class='nav-icon fa fa-folder'></i> Historias Clinicas</a></li>