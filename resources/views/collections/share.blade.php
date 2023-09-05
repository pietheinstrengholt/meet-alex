@extends('layouts.app')

@section('content')
	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
		<li><a onclick="trackClick(this)" href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
		<li class="active">Share Collection</li>
	</ul>

	<h2>Share Collection</h2>
	<h4>Use the email form below to make existing or new users aware of this content.</h4>
	<br>

	<form class="form-horizontal" role="form" method="POST" action="{{ url('/collections/' . $collection->id . '/postshare') }}">
		{{ csrf_field() }}

		<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
			<label for="email" class="col-md-2 control-label">E-Mail Address</label>

			<div class="col-md-6">
				<input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

				@if ($errors->has('email'))
					<span class="help-block">
						<strong>{{ $errors->first('email') }}</strong>
					</span>
				@endif
			</div>
		</div>

		<div class="form-group">
			<div class="col-md-6 col-md-offset-2">
				<button type="submit" class="btn btn-primary">
					<i class="fa fa-btn fa-envelope"></i> Send Link to Collection
				</button>
			</div>
		</div>
	</form>

@endsection
