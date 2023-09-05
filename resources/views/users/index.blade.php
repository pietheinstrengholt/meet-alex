<!-- /resources/views/users/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li class="active">Users</li>
	</ul>

	<h2>Users</h2>
	<h4>Please make a selection of one of the following users</h4>

	@if ( !$users->count() )
		No users found in the database!<br><br>
	@else
		<table class="table section-table dialog table-striped" border="1">

		<tr class="info">
		<td class="header">E-mail address</td>
		<td class="header">Full name</td>
		<td class="header">Provider</td>
		<td class="header">Role</td>
		<td class="header">Group</td>
		<td class="header" style="width: 242px;">Options</td>
		</tr>

		@foreach( $users as $user )
			<tr>
			<td>{{ $user->email }}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->provider }}</td>
			<td>{{ $user->role }}</td>
			<td>
			@if ($user->group)
				{{ $user->group->group_name }}
			@endif
			</td>
			{!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('users.destroy', $user->id), 'onsubmit' => 'return confirm(\'Are you sure to delete this user?\')')) !!}
			<td>
			{!! link_to_route('users.edit', 'Edit', array($user->id), array('class' => 'btn btn-info btn-xs')) !!}
			{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-xs', 'style' => 'margin-left:3px;')) !!}
			</td>
			{!! Form::close() !!}
			</tr>
		@endforeach

		</table>
	@endif

@endsection
