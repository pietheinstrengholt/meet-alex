<!-- /recollections/views/collections/partials/_form.blade.php -->
<div class="form-horizontal">

	<div class="form-group">
		{!! Form::label('collection_name', 'Collection:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::text('collection_name', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('collection_description', 'Description:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::textarea('collection_description', null, ['class' => 'form-control', 'rows' => '4']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('status_id', 'Visability', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::select('public', ['1' => 'Public available', '0' => 'Private and hidden for other users'], $collection->public, ['id' => 'public', 'class' => 'form-control']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('receive_notifications', 'Receive notifications', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::select('receive_notifications', ['1' => 'Yes', '0' => 'No'], $collection->receive_notifications, ['id' => 'receive_notifications', 'class' => 'form-control']) !!}
		<small>Select "yes" if you want to receive e-mail notifications when users start following your collection.</small>
		</div>
	</div>

	@if (Settings::get('allow_subsets_of_models') == "yes")
		{{-- If the template does not have any children, show drop down below --}}
		@if ( !($collection->children->count()) )
			<div class="form-group">
				{!! Form::label('parent_id', 'Link to Model:', array('class' => 'col-sm-3 control-label')) !!}
				<div class="col-sm-5">
				{!! Form::select('parent_id', $collections->pluck('collection_name', 'id'), $collection->parent_id, ['id' => 'parent_id', 'placeholder' => '', 'class' => 'form-control']) !!}
				</div>
			</div>
		@endif
	@endif

	@if (Settings::get('enable_groups') == "yes")
		@if ( $groups->count() )
			<br><h4>Which groups are allowed to edit this Collection?</h4>
			<table class="table table-striped table-condensed table-bordered">
			<tr class="success">
			<th>Group</th>
			<th style="text-align: center;">Allow Editing</th>
			</tr>
			@foreach( $groups as $group )
				<tr>
				<td>{{ $group->group_name }}</td>
				<td class="rights" style="text-align: center;">
				{{-- Check if the group object is in the groups collection --}}
				@if ( $collection->groups->contains($group) )
					{!! Form::checkbox('groups[]', $group->id, true) !!}
				@else
					{!! Form::checkbox('groups[]', $group->id, false) !!}
				@endif
				</td>
				</tr>
			@endforeach
			</table>
		@endif
	@endif

	@if (isset($contributors))
		@if ( $contributors->count() )
			<br><h4>Followers</h4>
			<table class="table table-striped table-condensed table-bordered">
			<tr class="success">
			<th>User</th>
			<th>Provider</th>
			<th style="text-align: center;">Allow Editing</th>
			</tr>
			@foreach( $contributors as $user )
				<tr>
				<td>{{ $user->fulldetails }}</td>
				<td>{{ $user->provider }}</td>
				<td class="rights" style="text-align: center;">
					{{-- Check if the group object is in the groups collection --}}
					@if ( $collection->users->contains($user) )
						{!! Form::checkbox('users[]', $user->id, true) !!}
					@else
						{!! Form::checkbox('users[]', $user->id, false) !!}
					@endif
				</td>
				</tr>
			@endforeach
			</table>
		@endif
	@endif

	{!! Form::submit($submit_text, ['class' => 'btn btn-primary']) !!}
	<input action="action" onclick="history.go(-1);" class="btn btn-default" style="margin-left:7px;" type="button" value="Cancel" />

</div>
