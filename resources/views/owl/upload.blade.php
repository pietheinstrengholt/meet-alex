<!-- /resources/views/owl/upload.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="active">Import OWL Taxonomy</li>
	</ul>

	<h2>Import terms and relations into a Model using OWL import</h2>
	<h4>Please make use of the upload form below</h4>

	<p>This page can be used to import an OWL file and taxonomy to a new Model. Make sure the Model doesn't contain any terms.</p>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::open(array('action' => 'OWLController@postowl', 'id' => 'form', 'files'=> 'true')) !!}

	<br>
	{!! Form::file('owl') !!}
	<p class="errors">{!! $errors->first('owl') !!}</p>

	<div class="form-group">
	<label for="caption">Model name</label>
	{!! Form::select('collection_id', $collections->lists('collection_name', 'id'), null, ['id' => 'collection_id', 'class' => 'form-control']) !!}
	</div>

	<button type="submit" class="btn btn-primary">Upload</button>
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
	{!! Form::close() !!}

@endsection
