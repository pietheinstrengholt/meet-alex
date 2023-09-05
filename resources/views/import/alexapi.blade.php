<!-- /resources/views/excel/upload.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="hidden-xs"><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li class="active">Import</li>
	</ul>

	<h2>Import Terms and Relations into a Collection using a meet-Alex API</h2>
	<h4>Use the clipboard function to copy a meet-Alex API. The url from the clipboard should be pasted below.</h4>

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

	{!! Form::open(array('action' => 'ImportController@postalexapi', 'id' => 'form', 'files'=> 'true')) !!}

	<p class="errors">{!! $errors->first('alexfile') !!}</p>

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
			{!! Form::label('alex_url', 'URL to meet-Alex API:', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::text('alex_url', null, ['class' => 'form-control']) !!}
			</div>
		</div>

		<button type="submit" class="btn btn-primary">Upload</button>
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
		{!! Form::close() !!}

	</div>

@endsection
