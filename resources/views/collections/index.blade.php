<!-- /resources/views/collections/index.blade.php -->
@extends('layouts.app')

@section('content')

<style>
	.panel-collectiondescription {
		border: 0px solid transparent;
		-webkit-box-shadow: 0 0px 0px rgba(0, 0, 0, 0);
		box-shadow: 0 0px 0px rgba(0, 0, 0, 0);
	}
</style>

	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="active">Collections</li>
	@if (Auth::check())
		<li class="right dropdown hidden-xs" id="status">
			<div class="button-right" class="dropdown">
				<button class="btn btn-info btn-xs dropdown-toggle" type="button" data-toggle="dropdown"><span class="glyphicon glyphicon-import"></span> Import
				<span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href="{{ URL::to('/import/excel') }}">Import XLS</a></li>
					<li><a href="{{ URL::to('/import/alexfile') }}">Import .alex file</a></li>
					<li><a href="{{ URL::to('/import/uploadowl') }}">Import .OWN file</a></li>
					<li><a href="{{ URL::to('/import/alexapi') }}">Import using a meet-alex API</a></li>
					<li><a href="{{ URL::to('/import/migration1') }}">Migration using the meet-alex API</a></li>
				</ul>
			</div>
		</li>
		<li class="right dropdown" id="status"><div class="button-right"><a href="{{ route('collections.create') }}"><span title="Click to create a new Collection" class="btn btn-info btn-xs">Create</span></a></div></li>
	@endif
	</ul>

	@if ( $editableCollections->count() )
		<div class="search" style="height: 60px; margin-left:20%;">
			{!! Form::open(array('action' => 'SearchController@search', 'class' => 'navbar-form navbar-left', 'style'=>'width:100%; padding:0px; margin-left:-2px; margin-right:0px;')) !!}
			<input type="hidden" name="_token" value="{!! csrf_token() !!}">
			<input type="hidden" name="advanced-search" value="no">
				<div class="form-group" style="width:100%;">
					<div style="width:60%; float: left;">
						<input type="text" name="search" class="form-control" placeholder="Search for content" style="width: 100%;">
					</div>
					<button type="submit" class="btn btn-success" style="margin-left:4px; float: left;">Submit</button>
				</div>
			{!! Form::close() !!}
		</div>
	@endif

	@if ( $editableCollections->count() )
		<h4>My Collections</h4>

		<div class="row">
			@foreach( $editableCollections as $collection )
				<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
					@can('edit-collection', $collection)
						<div class="panel panel-info" style="word-wrap: break-word;">
					@endcan

					@cannot('edit-collection', $collection)
						<div class="panel panel-success" style="word-wrap: break-word;">
					@endcan
						<div style="min-height: 36px;" class="panel-heading">
							<span class="pull-left"><h3 class="panel-title">{{ $collection->short_name }}</h3></span>
							<div class="dropdown">
								<span class="glyphicon glyphicon-cog pull-right" aria-hidden="true" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></span>
								<ul class="dropdown-menu pull-right" aria-labelledby="dropdownMenu1">
									{{-- There is no need to show the follow button if the collection is a private collection --}}
									@if (Auth::user()->bookmarks->contains($collection) && !Auth::user()->collections->contains($collection))
										<li><a href="#"><span title="Bookmark" class="bookmark refresh">Unfollow</span></a></li>
									@endif
									@can('edit-collection', $collection)
										<li><a href="{{ route('collections.edit', $collection->id) }}">Edit collection</a></li>
										<li><a href="{{ URL::to('/collections/' . $collection->id . '/bulkcreate') }}">Create multiple terms</a></li>
										<li role="separator" class="divider"></li>
										{!! Form::open(array('class' => 'form-inline', 'method' => 'DELETE', 'route' => array('collections.destroy', $collection->id), 'onsubmit' => 'return confirm(\'Are you sure to delete this collection?\')')) !!}
										<li>{!! Form::submit('Delete', array('class' => 'submitLink', 'style' => 'margin-left:2px;')) !!}</li>
										{!! Form::close() !!}
									@endcan
								</ul>
							</div>
						</div>
						<div class="panel-body">
							@if ( strlen($collection->collection_description) > 100 )
								<div class="panel-group" id="accordion{{$collection->id}}" role="tablist" aria-multiselectable="true">
									<div class="panel panel-collectiondescription">
										<div id="collapseOne{{$collection->id}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
											{{ $collection->short_description }}
											<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion{{$collection->id}}" href="#collapseTwo{{$collection->id}}" aria-expanded="false" aria-controls="collapseTwo{{$collection->id}}">read more</a>
										</div>
									</div>
									<div class="panel panel-collectiondescription" style="border:0;">
										<div id="collapseTwo{{$collection->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
											{{ $collection->collection_description }}
											<a role="button" data-toggle="collapse" data-parent="#accordion{{$collection->id}}" href="#collapseOne{{$collection->id}}" aria-expanded="true" aria-controls="collapseOne{{$collection->id}}">read less</a>
										</div>
									</div>
								</div>
							@else
								<p>{{ $collection->collection_description }}</p>
							@endif
							<p>
								<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
								<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
								<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
								<span class="glyphicon glyphicon-star" aria-hidden="true"></span>
							</p>
							<p>
							{!! link_to_route('collections.show', 'Visualise', array($collection->id, 'visualShow=yes'), array('class' => 'btn btn-info btn-xs')) !!}
							{!! link_to_route('collections.show', 'List terms', array($collection->id), array('class' => 'btn btn-info btn-xs')) !!}
							<span class="badge">{{ $collection->term_count }}</span>
							</p>
							<h6>Last modified: {{ date_format( $collection->updated_at,"d M Y") }}</h6>
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif

	@if ( $closedCollections->count() )
		<h4>Other Public Collections</h4>
		<table class="table section-table dialog table-striped" border="1">

		<tr class="success">
		<td class="header">Name</td>
		<td class="header">Description</td>
		<td class="header hidden-xs">Owner</td>
		<td class="header hidden-xs">Terms</td>
		@if (Auth::check())
			<td class="header hidden-xs">Bookmarks</td>
		@endif
		<td class="header hidden-xs hidden-sm" style="min-width: 245px;">Options</td>
		</tr>

		@foreach( $closedCollections as $collection )
			<tr>
			<td>
			<a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a>
			</td>
			<td>{{ $collection->collection_description }}</td>
			<td class="hidden-xs">
			@if ($collection->owner)
				{{ $collection->owner->display_name }}
			@endif
			</td>
			<td class="hidden-xs">{{ $collection->term_count }}</td>

			@if (Auth::check())
				<td class="hidden-xs">
					<div onclick="bookmarkCollection(this)" id="{{ $collection->id }}">
						<span title="Bookmark" class="bookmark refresh btn btn-info btn-xs">Follow</span>
					</div>
				</td>
			@endif

			<td class="hidden-xs hidden-sm">
				{!! link_to_route('collections.show', 'Visualise', array($collection->id, 'visualShow=yes'), array('class' => 'btn btn-info btn-xs')) !!}
			</td>
			</tr>
		@endforeach

		</table>
	@endif

	@if ( !$editableCollections->count() && !$closedCollections->count() )
		No collections found in the database!<br><br>
	@endif

	@if (file_exists(base_path() . '/version'))
		<div style="width:100%; text-align:center;">
			<p class="muted credit"><small>version: {{ file_get_contents(base_path() . '/version') }}</small></p>
		</div>
	@endif

@endsection
