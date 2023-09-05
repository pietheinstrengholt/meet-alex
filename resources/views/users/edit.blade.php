<!-- /resources/views/users/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('users.index'); !!}">Users</a></li>
		<li class="active">{{ $user->name }}</li>
	</ul>

	<h2>Edit User "{{ $user->name }}"</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model($user, ['method' => 'PATCH', 'route' => ['users.update', $user->id]]) !!}
	@include('users/partials/_form', ['submit_text' => 'Update User'])
	{!! Form::close() !!}
@endsection
