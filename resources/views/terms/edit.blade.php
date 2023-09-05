<!-- /resources/views/terms/edit.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
		<li><a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
		<li class="active">{{ $term->term_name }}</li>
	</ul>

	{{-- Changing the owner is only allowed when editing --}}
	@if ( $term->id )
		@if ( $term->status_id == 1 && $collection->workflow == 1)
			<div id="session-alert" class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<p><strong>Be aware!</strong> This term already has been approved. A new draft term will be created instead!</p>
			</div>
		@endif

		@if ( $term->status_id == 2 && $collection->workflow == 1)
			<div id="session-alert" class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<p><strong>Be aware!</strong> This term already has been proposed. Existing review comments will be removed when editing this term!</p>
			</div>
		@endif
	@endif

	<h2>Edit Term</h2>

	@if (count($errors) > 0)
		<div class="alert alert-danger">
		<ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
		</ul>
		</div>
	@endif

	{!! Form::model($term, ['method' => 'PATCH', 'route' => ['collections.terms.update', $collection->id, $term->id]]) !!}
	@include('terms/partials/_form', ['submit_text' => 'Update Term', 'propose_text' => 'Propose Term'])
	{!! Form::close() !!}
@endsection
