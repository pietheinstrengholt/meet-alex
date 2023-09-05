<!-- /resources/views/collections/create.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('collections.index'); !!}">Collections</a></li>
	  <li class="active">Create Collection</li>
	</ul>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model(new App\Collection, ['route' => ['collections.store']]) !!}
	@include('collections/partials/_form', ['submit_text' => 'Create'])
	{!! Form::close() !!}
	<br>

@endsection
