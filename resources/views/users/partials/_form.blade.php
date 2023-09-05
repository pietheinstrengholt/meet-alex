<!-- /resources/views/users/partials/_form.blade.php -->
<div class="form-horizontal">

	<div class="form-group">
		{!! Form::label('name', 'Full name:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::text('name', null, ['class' => 'form-control']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('nickname', 'Nickname:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::text('nickname', null, ['class' => 'form-control']) !!}
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('email', 'Email address:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		@if ($user)
			{!! Form::text('email', null, ['class' => 'form-control', 'id' => 'disabledInput', 'disabled']) !!}
			<input type="hidden" name="email" value="{{ $user->email }}">
		@else
			{!! Form::text('email', null, ['class' => 'form-control']) !!}
		@endif
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('provider', 'Provider:', array('class' => 'col-sm-3 control-label')) !!}
		<div class="col-sm-6">
		{!! Form::text('provider', null, ['class' => 'form-control', 'id' => 'disabledInput', 'disabled']) !!}
		</div>
	</div>

	@if (Settings::get('enable_groups') == "yes")
		@if ($groups->count())
			<div class="form-group">
				{!! Form::label('group_id', 'group:', array('class' => 'col-sm-3 control-label')) !!}
				<div class="col-sm-6">
				{!! Form::select('group_id', $groups->pluck('group_name', 'id'), null, ['id' => 'group_id', 'class' => 'form-control']) !!}
				</div>
			</div>
		@endif
	@endif

	@can('admin')
		<div class="form-group">
			{!! Form::label('role', 'Role:', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::select('role', array('admin' => 'admin', 'guest' => 'guest'), $user->role, ['id' => 'role', 'class' => 'form-control']) !!}
			</div>
		</div>
	@else
		<div class="form-group">
			{!! Form::label('provider', 'Provider:', array('class' => 'col-sm-3 control-label')) !!}
			<div class="col-sm-6">
			{!! Form::text('role', $user->role, ['class' => 'form-control', 'id' => 'disabledInput', 'disabled']) !!}
			</div>
		</div>
	@endcan

	<div class="form-group">
		{!! Form::submit($submit_text, ['class' => 'btn btn-primary']) !!}
	</div>

</div>
