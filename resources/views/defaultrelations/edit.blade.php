<!-- /resources/views/defaultrelations/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('defaultrelations.index'); !!}">Default relations</a></li>
	  <li class="active">{{ $defaultRelation->relation_name }}</li>
	</ul>

	<h2>Edit Relation "{{ $defaultRelation->relation_name }}"</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model($defaultRelation, ['method' => 'PATCH', 'route' => ['defaultrelations.update', $defaultRelation->id]]) !!}
	@include('defaultrelations/partials/_form', ['submit_text' => 'Edit Relation'])
	{!! Form::close() !!}
@endsection
