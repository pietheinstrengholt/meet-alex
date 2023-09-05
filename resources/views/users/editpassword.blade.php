<!-- /resources/views/users/editpassword.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li class="active">Edit password</li>
	</ul>

	<div class="form-horizontal">

	<h3>Reset password for user "{{ $user->name }}"</h3>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::open(array('action' => array('UserController@updatepassword', $user))) !!}

	<div class="form-group">
		<label class="col-md-4 control-label">Password</label>
		<div class="col-md-6">
			<input type="password" class="form-control" name="password">
		</div>
	</div>

	<div class="form-group">
		<label class="col-md-4 control-label">Confirm Password</label>
		<div class="col-md-6">
			<input type="password" class="form-control" name="password_confirmation">
		</div>
	</div>

	<input type="hidden" name="_token" value="{!! csrf_token() !!}">

	<button type="submit" class="btn btn-primary">Submit new password</button>

	{!! Form::close() !!}

	</div>

@endsection
