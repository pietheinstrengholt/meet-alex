<!-- /resources/views/relations/create.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('collections.index'); !!}">Models</a></li>
	  <li><a href="{{ route('collections.show', $collection) }}">{{ $collection->collection_name }}</a></li>
	  <li><a href="{!! route('collections.relations.index', $collection->id); !!}">Relations</a></li>
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

	{!! Form::model(new App\Relation, ['route' => ['collections.relations.store', $collection->id]]) !!}
	@include('relations/partials/_form', ['submit_text' => 'Create Relation'])
	{!! Form::close() !!}
@endsection
