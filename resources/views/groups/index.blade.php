<!-- /resources/views/groups/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="active">Groups</li>
	</ul>

	<h2>Groups</h2>
	<h4>Please make a selection of one of the following groups</h4>

	@if ( !$groups->count() )
		No groups found in the database!<br><br>
	@else
		<table class="table section-table dialog table-striped" border="1">

		<tr class="info">
		<td class="header">Name</td>
		<td class="header">Description</td>
		<td class="header" style="width: 120px;">Options</td>
		</tr>

		@foreach( $groups as $group )
			<tr>
			<td>{{ $group->group_name }}</td>
			<td>{{ $group->group_description }}</td>
			{!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('groups.destroy', $group->id), 'onsubmit' => 'return confirm(\'Are you sure to delete this group?\')')) !!}
			<td>
			{!! link_to_route('groups.edit', 'Edit', array($group->id), array('class' => 'btn btn-info btn-xs')) !!}
			{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-xs', 'style' => 'margin-left:3px;')) !!}
			</td>
			{!! Form::close() !!}
			</tr>
		@endforeach

		</table>
	@endif

	<p>
	{!! link_to_route('groups.create', 'Create Group') !!}
	</p>

@endsection
