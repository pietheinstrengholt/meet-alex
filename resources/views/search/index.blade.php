<!-- /resources/views/search/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="active">Search Results</li>
	</ul>

	@if ( $results->count() )
		<h5>Results based on your search</h5>
		<table class="table section-table dialog table-striped" border="1">
		<tr class="success">
		<td class="header">Type</td>
		<td class="header" style="width: 13%;">Name</td>
		<td class="header">Description</td>
		<td class="header" style="width: 13%;">Collection</td>
		@if ($editableCollections->count())
			<td class="header hidden-xs" style="width: 140px;">Options</td>
		@endif
		</tr>

		@foreach( $results as $result )
			<tr>
			@if (isset($result->collection_name))
				<td>Collection</td>
			@else
				<td>Term</td>
			@endif

			@if (isset($result->collection_name))
				<td><a href="{!! url('collections') . '/' . $result->id; !!}">{{ $result->collection_name }}</a></td>
			@else
				<td><a href="{!! url('collections') . '/' . $result->collection_id . '/terms/' . $result->id; !!}">{{ $result->term_name }}</a></td>
			@endif

			@if (isset($result->collection_name))
				<td>{{ strip_tags($result->collection_description) }}</td>
			@else
				<td>{{ strip_tags($result->term_definition) }}</td>
			@endif

			@if (isset($result->collection_name))
				<td></td>
			@else
				<td><a href="{!! url('collections') . '/' . $result->collection->id; !!}">{{ $result->collection->collection_name }}</a></td>
			@endif

			@if ($editableCollections->count())

				@if (isset($result->collection_name))
					<td>{!! link_to_route('collections.show', 'Visualise', array($result->id, 'visualShow=yes'), array('class' => 'btn btn-info btn-xs')) !!}</td>
				@else
					<td class="hidden-xs">
						<div class="copy-button-round"><span title="Click to copy term to other Collection" class="clone label label-warning label-as-badge" id="{{ $result->id }}" data-toggle="modal" data-target="#myModal">Copy</span></div>
						@if ($editableCollections->contains($result->collection))
							<div class="edit-button-round"><a href="{!! url('collections') . '/' . $result->collection_id . '/terms/' . $result->id . '/edit'; !!}"><span title="Click to edit term" class="label label-info label-as-badge">Edit</span></a></div>
						@endif
					</td>
				@endif

			@endif
			</tr>
		@endforeach

		</table>
	@else
		<p>No results have been found. Please try to refine search.</p>
	@endif

	@if ($editableCollections->count())
		@include('modal')
	@endif

@endsection
