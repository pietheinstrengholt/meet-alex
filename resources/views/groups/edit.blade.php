<!-- /resources/views/groups/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('groups.index'); !!}">Groups</a></li>
	  <li class="active">{{ $group->group_name }}</li>
	</ul>

	<h2>Edit group "{{ $group->group_name }}"</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model($group, ['method' => 'PATCH', 'route' => ['groups.update', $group->id]]) !!}
	@include('groups/partials/_form', ['submit_text' => 'Edit group'])
	{!! Form::close() !!}
@endsection
