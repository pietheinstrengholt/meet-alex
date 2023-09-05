<!-- /resources/views/terms/index.blade.php -->
@extends('layouts.app')

<style>
	div.flex-outer {
		display: flex;
		display: -webkit-flex;
	}

	div.flex-inner {
		flex: 1;
		display: flex;
		display: -webkit-flex;
	}
</style>

@section('content')

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
	<li><a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
	<li class="active">{{ $term->term_name }}</li>
	<li class="active">Update link or clone</li>
	</ul>

	<h2>Update link or clone</h2>
	<h4>Make a decision if you want to update the link or clone the term</h4>
	<br>

	<div class="row" class="flex-outer">
		<div class="col-md-6 flex-inner">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Previously linked to</h3>
				</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt>Term name</dt>
						<dd><strong>{{ $oldTerm->term_name }}</strong></dd>
					</dl>
					<dl class="dl-horizontal">
						<dt>Term definition</dt>
						<dd>{{ $oldTerm->term_definition }}</dd>
					</dl>
					<dl class="dl-horizontal">
						<dt>Date created</dt>
						<dd>{{ $oldTerm->created_at }}</dd>
					</dl>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="panel panel-warning">
				<div class="panel-heading">
					<h3 class="panel-title">Currently linked to</h3>
				</div>
				<div class="panel-body">
					<dl class="dl-horizontal">
						<dt>Term name</dt>
						<dd><strong>{{ $term->term_name }}</strong></dd>
					</dl>
					<dl class="dl-horizontal">
						<dt>Term definition</dt>
						<dd>{{ $term->term_definition }}</dd>
					</dl>
					<dl class="dl-horizontal">
						<dt>Date created</dt>
						<dd>{{ $term->created_at }}</dd>
					</dl>
				</div>
			</div>
		</div>
	</div>
	<br>

	{!! Form::open(['action' => ['TermController@updateLink', $collection, $term]]) !!}
	<button type="submit" id="keep" name="keep" value="keep" class="keep btn btn-primary" style="margin-right:5px;">Keep link and dismiss</button>
	<button type="submit" id="clone" name="clone" value="clone" class="clone btn btn-warning">Break link and Clone term</button>
	<input type="hidden" name="_token" value="{!! csrf_token() !!}">
	<input type="hidden" name="version" value="{{ $oldTerm->version }}">
	{!! Form::close() !!}

@endsection
