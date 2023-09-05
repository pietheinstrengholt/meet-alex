<!-- /resources/views/defaultrelations/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="active">Default relations</li>
	</ul>

	<h2>Default relations</h2>
	<h4>Please make a selection of one of the following default relations</h4>
	<h5>These are the default relations. When a new model is created the following relation types will be created.</h5>

	@if ( !$defaultrelations->count() )
		No relations found in the database!<br><br>
		@else
		<table class="table section-table dialog table-striped" border="1">

		<tr class="info">
		<td class="header">Name</td>
		<td class="header">Description</td>
		<td class="header" style="width: 120px;">Options</td>
		</tr>

		@foreach ($defaultrelations as $relation)
			<tr>
			<td>{{ $relation->relation_name }}</td>
			<td>{{ $relation->relation_description }}</td>
			{!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('defaultrelations.destroy', $relation->id), 'onsubmit' => 'return confirm(\'Are you sure to delete this default relation?\')')) !!}
			<td>
			{!! link_to_route('defaultrelations.edit', 'Edit', array($relation->id), array('class' => 'btn btn-info btn-xs')) !!}
			{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-xs', 'style' => 'margin-left:3px;')) !!}
			</td>
			{!! Form::close() !!}
			</tr>
		@endforeach

		</table>
	@endif

	<p>
	{!! link_to_route('defaultrelations.create', 'Create a new default relation') !!}
	</p>

@endsection
