<!-- /resources/views/terms/index.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li><a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
	<li class="active">{{ $term->term_name }}</li>
	<li class="active">Historical information</li>
	</ul>

	<h2>{{ $term->term_name }}</h2>
	<h4>The table below shows the historical data and audit trail</h4>

	@if ( !$terms->count() )
		No term historical information found in the database!<br><br>
	@else
		<table class="table section-table dialog" border="1">

		<tr class="info">
			<td class="header">Version</td>
			<td class="header">Term name</td>
			<td class="header" style="width:30%;">Definition</td>
			<td class="header">Archived</td>
			<td class="header">Date</td>
			<td class="header">Creator</td>
		</tr>

		@foreach( $terms as $version )
			<tr>
			<td>{{ $version->version }}</td>
			<td><a href="{!! route('collections.terms.show',['collection'=>$collection,'term'=>$term]); !!}">{{ $version->term_name }}</a></td>
			<td>{{ strip_tags($version->term_definition) }}</td>
			<td>
				@if ($version->archived == 1)
					<p>Yes</p>
				@else
					<p>No</p>
				@endif
			</td>
			<td>{{ $version->created_at }}</td>
			<td>{{ $version->owner->full_details }}</td>
			</tr>
		@endforeach

		</table>
	@endif
@endsection
