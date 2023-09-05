<!-- /resources/views/groups/partials/_form.blade.php -->
<div class="form-horizontal">

	<br>
	<div class="form-group">
		{!! Form::label('group_name', 'Group name:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
			{!! Form::text('group_name', null, ['class' => 'form-control']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('group_description', 'Group description:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
			{!! Form::textarea('group_description', null, ['class' => 'form-control', 'rows' => '4']) !!}
		</div>
	</div>

	@if ( $group->users->count() )
		<br>
		<h4>The following users are part of this group</h4>
		<table class="table section-table dialog table-striped" border="1">

		<tr class="info">
		<td class="header">E-mail address</td>
		<td class="header">Full name</td>
		<td class="header">Provider</td>
		<td class="header">Role</td>
		</tr>

		@foreach( $group->users as $user )
			<tr>
			<td>{!! link_to_route('users.edit', $user->email, array($user->id)) !!}</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->provider }}</td>
			<td>{{ $user->role }}</td>
			</tr>
		@endforeach

		</table>
		<br>
	@endif

	<div class="form-group">
		{!! Form::submit($submit_text, ['class' => 'btn btn-primary']) !!}
	</div>

</div>
