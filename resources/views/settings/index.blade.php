<!-- /resources/views/settings/index.blade.php -->
@extends('layouts.app')

@section('content')

<ul class="breadcrumb breadcrumb-section">
<li><a href="{!! url('/'); !!}">Home</a></li>
<li class="active">Settings</li>
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

<h2>Settings</h2>
<h4>Manage configuration settings</h4><br>

{!! Form::open(array('action' => 'SettingController@store', 'id' => 'form')) !!}

<div class="form-group">
  <label for="usr">Welcome screen header text:</label>
  <input name="main_message1" type="text" style="width:60%;" class="form-control" id="usr" value="{!! Settings::get('main_message1') !!}" placeholder="{!! Settings::get('main_message1') !!}">
</div>

<div class="form-group">
  <label for="usr">Welcome screen sub text:</label>
  <input name="main_message2" type="text" style="width:60%;" class="form-control" id="usr" value="{!! Settings::get('main_message2') !!}" placeholder="{!! Settings::get('main_message2') !!}">
</div>

<div class="form-group">
  <label for="usr">Welcome text below search bar:</label>
  <input name="main_message3" type="text" style="width:80%" class="form-control" id="usr" value="{!! Settings::get('main_message3') !!}" placeholder="{!! Settings::get('main_message3') !!}">
</div>

<div class="form-group">
  <label for="usr">Administrator email message:</label>
  <input name="administrator_email" type="email" style="width:350px;" class="form-control" id="usr" value="{!! Settings::get('administrator_email') !!}" placeholder="{!! Settings::get('administrator_email') !!}">
</div>

<h5><label for="usr">Allow users to approve their own changes:</label></h5>
{{ Form::select('approve_own_changes', ['yes' => 'Yes', 'no' => 'No'], Settings::get('approve_own_changes'), ['class' => 'form-control', 'style' => 'width: 100px; margin-top: -10px;']) }}

<h5><label for="usr">Allow subsets of models:</label></h5>
{{ Form::select('allow_subsets_of_models', ['yes' => 'Yes', 'no' => 'No'], Settings::get('allow_subsets_of_models'), ['class' => 'form-control', 'style' => 'width: 100px; margin-top: -10px;']) }}

<h5><label for="usr">Enable groups:</label></h5>
{{ Form::select('enable_groups', ['yes' => 'Yes', 'no' => 'No'], Settings::get('enable_groups'), ['class' => 'form-control', 'style' => 'width: 100px; margin-top: -10px;']) }}

<button style="margin-bottom:15px; margin-top:20px;" type="submit" class="btn btn-primary">Submit new settings</button>

<input type="hidden" name="_token" value="{!! csrf_token() !!}">
{!! Form::close() !!}

@endsection
