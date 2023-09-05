<!-- /resources/views/excel/upload.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="hidden-xs"><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li class="active">Import</li>
	</ul>

	<h2>Import Terms and Relations into a Collection (XSL file)</h2>
	<h4>Use the form to create a new Collection and upload a .xls (Excel) file.</h4>

	<p>This page can be used to import to a new Collection. The Excel file to be used contains two different sheets. The terms sheet is used for the terms and definitions. Be sure that every term is unique. The ontology sheet is used for importing all relations between terms. There's a validation on the integrity of the content, so in any case the integrity is not correct an error message will be displayed and the upload process will be aborted.</p>
	<p><a href="{{ url('downloadexcel') }}">Download the excel template</a></p>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif
	<br>

	{!! Form::open(array('action' => 'ImportController@postexcel', 'id' => 'form', 'files'=> 'true')) !!}

	<p class="errors">{!! $errors->first('excelfile') !!}</p>

	<div class="form-horizontal">
		<div class="form-group">
			{!! Form::label('collection_name', 'Collection Name:', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::text('collection_name', null, ['class' => 'form-control']) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('collection_description', 'Collection Description:', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::textarea('collection_description', null, ['class' => 'form-control', 'rows' => '4']) !!}
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-3"></div>
			<div class="col-sm-3">
			{!! Form::file('excelfile') !!}
			<br>
			<div>
		</div>

		<button type="submit" class="btn btn-primary">Upload</button>
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		{!! Form::close() !!}

	</div>

@endsection
