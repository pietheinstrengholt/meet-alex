<!-- /resources/views/excel/upload.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="hidden-xs"><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li class="active">Import</li>
	</ul>

	<h2>Migrate content using the meet-Alex API</h2>
	<h4>Use meet-Alex collection's API. This will be probably https://www.meet-alex.org. The url from the clipboard should be pasted below.</h4>

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

	{!! Form::open(array('action' => 'ImportController@postmigration1', 'id' => 'form', 'files'=> 'true')) !!}

	<p class="errors">{!! $errors->first('alexfile') !!}</p>

	<div class="form-horizontal">
		<div class="form-group">
			{!! Form::label('alex_url', 'URL to meet-Alex API (index):', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::text('alex_url', null, ['class' => 'form-control']) !!}
			</div>
		</div>
		<br>

		<button type="submit" class="btn btn-primary">Query API</button>
		<input type="hidden" name="_token" value="{!! csrf_token() !!}">
	{!! Form::close() !!}

	</div>

@endsection
