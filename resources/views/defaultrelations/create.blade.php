<!-- /resources/views/defaultrelations/create.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('defaultrelations.index'); !!}">Default relations</a></li>
	  <li class="active">Create new relation</li>
	</ul>

	<h2>Create Relation</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model(new App\DefaultRelation, ['route' => ['defaultrelations.store']]) !!}
	@include('defaultrelations/partials/_form', ['submit_text' => 'Create Relation'])
	{!! Form::close() !!}
@endsection
