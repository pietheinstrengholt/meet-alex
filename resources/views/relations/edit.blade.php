<!-- /resources/views/relations/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Models</a></li>
		<li><a href="{{ route('collections.show', $collection) }}">{{ $collection->collection_name }}</a></li>
		<li><a href="{!! route('collections.relations.index', $collection->id); !!}">Relations</a></li>
		<li class="active">{{ $relation->relation_name }}</li>
	</ul>

	<h2>Edit Relation "{{ $relation->relation_name }}"</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model($relation, ['method' => 'PATCH', 'route' => ['collections.relations.update', $collection->id, $relation->id]]) !!}
	@include('relations/partials/_form', ['submit_text' => 'Edit Relation'])
	{!! Form::close() !!}
@endsection
