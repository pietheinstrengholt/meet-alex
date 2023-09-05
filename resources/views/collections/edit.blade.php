<!-- /resources/views/collections/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
		<li class="active">{{ $collection->collection_name }}</li>
		<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{{ url('collections') . '/' . $collection->id . '/relations' }}"><span title="Click to manage the Relations in this Collection" class="btn btn-info btn-xs">Relations</span></a></div></li>
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

	{!! Form::model($collection, ['method' => 'PATCH', 'route' => ['collections.update', $collection->id]]) !!}
	@include('collections/partials/_form', ['submit_text' => 'Update'])
	{!! Form::close() !!}
	<br>
@endsection
