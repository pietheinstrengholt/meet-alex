<!-- /resources/views/users/bookmarks.blade.php -->
@extends('layouts.app')

@section('content')

	<h3>Change views</h3>
	<h4>Which models do you want to follow and show on the home screen?</h4>

	{!! Form::open(['url'=>'/users/postbookmarks', 'method'=>'POST', 'files'=>'true']) !!}

	@if ( $collections->count() )
		<table class="table table-striped table-condensed table-bordered">
		<tr class="success">
		<th><h4>Model name</h4></th>
		<th><h4>Created by</h4></th>
		<th style="text-align: center;"><h4>Follow Y/N</h4></th>
		</tr>
		@foreach( $collections as $collection )
			@if ($collection->created_by == Auth::user()->id)
				<tr style="background-color:#FAFAD2;">
			@else
				<tr>
			@endif
			<td><strong>{{ $collection->collection_name }}</strong></td>
			<td>
			@if (!empty($collection->owner))
				{{ $collection->owner->name }}
			@endif
			</td>
			<td class="rights" style="text-align: center;">
			{{-- Check if the collection object is in the collection user collection --}}
			@if ( Auth::user()->bookmarks->contains($collection) )
				{!! Form::checkbox('collections[]', $collection->id, true) !!}
			@else
				{!! Form::checkbox('collections[]', $collection->id, false) !!}
			@endif
			</td>
			</tr>
		@endforeach
		</table>
	@endif

	<button type="submit" class="btn btn-primary">Update bookmarks</button>
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
	{!! Form::close() !!}
	<br>

@endsection
