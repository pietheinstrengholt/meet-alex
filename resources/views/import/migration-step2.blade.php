<!-- /resources/views/collections/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="hidden-xs"><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li class="active">Import</li>
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
	<br>

	{!! Form::open(array('action' => 'ImportController@postmigration2', 'id' => 'form', 'files'=> 'true')) !!}

	@if ($alexArray)
		<h4>Available collections for import</h4>

		<div class="col-xs-12">
			<div class="well" style="max-height: 600px; overflow: auto;">
				<ul class="list-group checked-list-box" style="margin-bottom: 0px !important;">
				@foreach( $alexArray as $collection )
					<tr>
						<li class="list-group-item">
							<input type="checkbox" name="collections[{{ $collection['id'] }}]" value="{{ $collection['id'] }}" />
							<strong style="margin-left: 5px;">{{ $collection['collection_name'] }}</strong> - {{ $collection['collection_description'] }}
						</li>
					</tr>
				@endforeach
				</ul>
			</div>
		</div>
	@endif

	<button type="submit" class="btn btn-primary">Import selected collections</button>
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
	<input type="hidden" name="alex_url" value="{!! $alex_url !!}">
	{!! Form::close() !!}

@endsection
