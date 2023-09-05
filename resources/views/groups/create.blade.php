<!-- /resources/views/groups/create.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	  <li><a href="{!! url('/'); !!}">Home</a></li>
	  <li><a href="{!! route('groups.index'); !!}">Groups</a></li>
	  <li class="active">Create new group</li>
	</ul>

	<h2>Create Group</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model(new App\Group, ['route' => ['groups.store']]) !!}
	@include('groups/partials/_form', ['submit_text' => 'Create Group'])
	{!! Form::close() !!}
@endsection
