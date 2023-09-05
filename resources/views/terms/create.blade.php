<!-- /resources/views/terms/create.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
		<li><a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
		<li class="active">Create Term</li>
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

	{!! Form::model(new App\Term, ['route' => ['collections.terms.store', $collection->id]]) !!}
	@include('terms/partials/_form', ['submit_text' => 'Create Term'])
	{!! Form::close() !!}
@endsection
