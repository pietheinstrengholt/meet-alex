<!-- /resources/views/collections/show.blade.php -->
@extends('layouts.app')

<style>
	div.search {
		height: 60px;
		margin-left:20%;
	}

	div.full-width {
		width:100%;
	}

	a.btn.dropdown-toggle {
		color:#fff;
		padding: 0px;
		margin-top: -2px;
	}

	.collection_graph_line {
		stroke:grey; stroke-width:1; //stroke-dasharray:1, 1;
	}
	.collection_graph_text {
		font-family:arial; font-size:13px; text-anchor:middle; fill:grey;
	}
</style>

@section('content')

	{{-- Left side of the breadcrumb bar --}}
	<ul class="breadcrumb breadcrumb-section">
	<li><a href="{!! url('/'); !!}">Home</a></li>
	<li class="hidden-xs"><a href="{!! route('collections.index'); !!}">Collections</a></li>
	@if ($collection)
		@if ($collection->parent)
			<li><a href="{{ route('collections.show', $collection->parent) }}">{{ $collection->parent->collection_name }}</a></li>
		@endif
	@endif
	@if ($collection)
		<li><a href="{{ route('collections.show', $collection) }}">{{ $collection->collection_name }}</a></li>
	@else
		<li class="active">All Collections</li>
	@endif
	<li class="active">Terms</li>

	@if (!empty($collection))
		{{-- Right side of the breadcrumb bar --}}
		<li class="right dropdown" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id . '/share'; !!}"><span title="Click to share collection" class="btn btn-info btn-xs">Share</span></a></div></li>
		@can('edit-collection', $collection)
			<li class="right dropdown" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id . '/edit'; !!}"><span title="Click to edit collection" class="btn btn-info btn-xs">Edit Collection</span></a></div></li>
		@endcan
	@endif
	@if (!empty($collection))
		<li class="right dropdown hidden-xs" id="status">
			<div class="button-right" class="dropdown">
				<button class="btn btn-info btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Export
				<span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href="{{ url('collections') . '/' . $collection->id . '/excel'}}">Export to XLS</a></li>
					<li><a href="{{ url('api/collections') . '/' . $collection->id . '?download=yes' }}">Export to .alex file</a></li>
					<li><a onclick="copyToClipboard(this)" href="#" id="{{ $collection->id }}">Export using API (copy to clipboard)</a></li>
				</ul>
			</div>
		</li>
	@endif
	@if (!empty($collection))
		@if (Auth::check())
			<li class="right dropdown hidden-xs" id="status">
				<div class="button-right">
					<div onclick="bookmarkCollection(this)" id="{{ $collection->id }}">
						@if (!(Gate::allows('contribute-to-collection', $collection) || $collection->created_by == Auth::user()->id))
							@if ( Auth::user()->bookmarks->contains($collection))
								<span title="Bookmark" class="bookmark btn btn-info btn-xs">Unfollow</span>
							@else
								<span title="Bookmark" class="bookmark btn btn-info btn-xs">Follow</span>
							@endif
						@endif
					</div>
				</div>
			</li>
		@endif
		@if (isset($modelView))
			<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id; !!}"><span title="Click to visualise the collection" class="btn btn-info btn-xs">List Terms</span></a></div></li>
		@else
			<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id . '?visualShow=yes'; !!}"><span title="Click to visualise the collection" class="btn btn-info btn-xs">Visualise</span></a></div></li>
		@endif
		@can('contribute-to-collection', $collection)
			<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id . '/terms/create'; !!}"><span title="Click to create term" class="btn btn-info btn-xs" class="btn btn-info btn-xs">Create Term</span></a></div></li>
		@else
			@if (Auth::check())
				<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $collection->id . '/collaborate'; !!}"><span title="Click to here to request access to the collection" class="btn btn-info btn-xs">Collaborate</span></a></div></li>
			@endif
		@endcan
		<input type="hidden" id="collection_id" value="{{ $collection->id }}">
	@endif
	</ul>

	@if (isset($modelView))

		@include('visual.header')

		<meta name="collection_id" content="{{ $collection->id }}">
		<meta name="fullscreen" content="yes">

		<div class="row">
			<div id="fullscreen" class="col-md-12 col-xs-12">
				<div class="row" id="sidebar">
					@include('visual.mcontainer')
				</div>
			</div>
		</div>
		@include('visual.footer')
	@else
		<meta name="fullscreen" content="no">
		@if ($collection)
			@if ( $collection->children->count() )

				<h2>Sub collection</h2>
				<h4>Please make a selection of one of the following collections</h4>

				<table class="table section-table dialog table-striped" border="1">

				<tr class="info">
				<td class="header">Collection</td>
				<td class="header">Short description</td>
				<td class="header hidden-xs" style="width: 245px;">Options</td>
				</tr>
				@foreach($collection->children as $child)
					<tr>
					<td><a href="{{ route('collections.show', [$child]) }}">{{ $child->collection_name }}</a></td>
					<td>{!! html_entity_decode(e($child->collection_description)) !!}</td>
					<td class="hidden-xs">
					@can('edit-collection', $collection)
						{!! link_to_route('collections.edit', 'Edit', array($child), array('class' => 'btn btn-info btn-xs')) !!}
						{!! Form::submit('Delete', array('class' => 'btn btn-danger btn-xs', 'style' => 'margin-left:2px;')) !!}
					@endcan
					</td>
					{!! Form::close() !!}
					</tr>
				@endforeach
				</table>
			@endif
		@endif

		@if ($terms->count() > 0)
			{{-- Add search bar --}}
			<div class="search">
				{!! Form::open(array('action' => 'SearchController@search', 'class' => 'navbar-form navbar-left', 'style'=>'width:100%; padding:0px; margin-left:-2px; margin-right:0px;')) !!}
				<input type="hidden" name="_token" value="{!! csrf_token() !!}">
				@if (empty($collection))
					<input type="hidden" name="advanced-search" value="no">
				@else
					<input type="hidden" name="advanced-search" value="yes">
				@endif
				@if (!empty($collection))
					<input type="hidden" name="collections[{{ $collection->id }}]" value="{{ $collection->id }}" checked>
					<input type="hidden" name="types[1]" value="term_names" checked>
					<input type="hidden" name="types[2]" value="term_definitions" checked>
					<input type="hidden" name="types[3]" value="term_properties" checked>
				@endif
					<div class="form-group full-width">
						<div style="width:60%; float: left;">
							<input type="text" name="search" class="form-control" placeholder="Search for content" style="width: 100%;">
						</div>
						<button type="submit" class="btn btn-success" style="margin-left:4px; float: left;">Submit</button>
					</div>
				{!! Form::close() !!}
			</div>
		@endif


		<!-- Collection graph begin ####################################################################################### -->
		<?php
			$relatedcollections = array();
			$svg_canvas_height = 0;
			$svg_canvas_height_dy = 20;
			$svg_min_canvas_height = 120;
			$svg_line_height = 110;
			$svg_line_anchor_dy = 50;
		?>
		@foreach($terms as $key => $term)
			@if (!empty($term->objects) || !empty($term->subjects))
				@foreach($term->objects as $key => $object)
					@if ($object->object && $object->object->collection_id != $collection->id)
							<?php
								$relatedcollections += array($object->object->collection_id => $object->object->collection->short_name);
							?>
					@endif
				@endforeach
				@foreach($term->subjects as $key => $subject)
					@if ($subject->subject && $subject->subject->collection_id != $collection->id)
							<?php
								$relatedcollections += array($subject->subject->collection_id => $subject->subject->collection->short_name);
							?>
					@endif
				@endforeach
			@endif
		@endforeach
		<?php $svg_canvas_height = max($svg_canvas_height_dy + count($relatedcollections) * $svg_line_height, $svg_min_canvas_height); ?>

		<svg width="400" height="{{ $svg_canvas_height }}" viewBox="0 -15 400 {{ $svg_canvas_height }}" version="1.1" xmlns="http://www.w3.org/2000/svg"
		      xmlns:xlink="http://www.w3.org/1999/xlink">
			<?php
				$i = 0;
				foreach ($relatedcollections as $rc_id=>$rc_name ) {
							$j = $i + $svg_line_anchor_dy;
							echo "<line x1='100' y1='50' x2='300' y2='$j' class='collection_graph_line' />";
							echo "<image x='250' y='$i' width='120' height='106.8' xlink:href='../../img/pyramid.svg' />";
							echo "<a onclick='trackClick(this)' xlink:href='$rc_id'>";
							echo "<text x='250' y='$i' dx='55' dy='100' class='collection_graph_text' >$rc_name</text>";
							echo "</a>";
							$i = $i + $svg_line_height;
				}
			?>

			<!-- This collection -->
		  <image x="65" y="0" width="120" height="106.8" xlink:href="../../img/pyramid.svg" />
			<text x="65" y="0" dx="55" dy="100" class='collection_graph_text'>{{ $collection->short_name }}</text>

		</svg>
		<!-- Collection graph end ####################################################################################### -->



		{{-- Pagination with all letters --}}
		@if ( !empty($letters) )
			<div class="text-center">
			<ul class="pagination">
			{{-- Hide ALL if all collections are used --}}
			@if ($collection)
				@if ($collection->term_count < 1000)
					@if (empty($letter))
						<li class="active"><a onclick="trackClick(this)" href="{{ route('collections.show', [$collection->id]) }}">[All]</a></li>
					@else
						<li><a onclick="trackClick(this)" href="{{ route('collections.show', [$collection->id]) }}">[All]</a></li>
					@endif
				@endif
			@endif
			@foreach( $letters as $page )
				@if ($letter == $page)
					@if ($collection)
						<li class="active"><a onclick="trackClick(this)" href="{{ route('collections.show', [$collection->id]) }}?letter={{ $page }}">{{ $page }}</a></li>
					@else
						<li class="active"><a onclick="trackClick(this)" href="{{ url('/terms/') }}?letter={{ $page }}">{{ $page }}</a></li>
					@endif
				@else
					@if ($collection)
						<li><a onclick="trackClick(this)" href="{{ route('collections.show', [$collection->id]) }}?letter={{ $page }}">{{ $page }}</a></li>
					@else
						<li><a onclick="trackClick(this)" href="{{ url('/terms/') }}?letter={{ $page }}">{{ $page }}</a></li>
					@endif
				@endif
			@endforeach
			</ul>
			</div>
		@endif

		{{-- Show table with all terms --}}
		@if ($terms->count() == 0)
			No terms found in the database!<br><br>
		@else
			<table class="table section-table dialog table-striped" border="1">
				<tr class="success">
					<td class="header">Term</td>
					<td class="header">Definition</td>
					@if (empty($collection))
						<td class="header hidden-xs">Collection</td>
					@endif
					<td class="header hidden-xs">Rating</td>
					@if (Gate::allows('contribute-to-collection', $collection) || $editableCollections->count())
						<td class="header hidden-xs" style="width: 200px;">Options</td>
					@endif
				</tr>

				@foreach($terms as $key => $term)
					@if ($collection)
						@if ($collection->id <> $term->collection->id)
							<tr style="background-color:#FEF5E6;" id="{{ $term->id }}">
						@else
							<tr id="{{ $term->id }}">
						@endif
					@else
						<tr id="{{ $term->id }}">
					@endif
						<td>
							{{-- Show a link starting with the most mature term --}}
							<a onclick="trackClick(this)" href="{{ url('collections') . '/' . $term->collection_id . '/terms/' . $term->id }}">{{ $term->term_name }}</a>
						</td>
						<td>
							{{-- Show the most mature term definition --}}
							{{ strip_tags($term->term_definition) }}
						</td>
						@if (empty($collection))
							<td class="hidden-xs">
								@if (!ctype_alpha(substr($term->term_name, 0, 1)))
									<a onclick="trackClick(this)" href="{{ route('collections.show', [$term->collection->id]) }}?letter=[0-9]">{{ $term->collection->collection_name }}</a>
								@else
									<a onclick="trackClick(this)" href="{{ route('collections.show', [$term->collection->id]) }}?letter={{ substr(strtoupper($term->term_name), 0, 1) }}">{{ $term->collection->collection_name }}</a>
								@endif
							</td>
						@endif
						<td class="hidden-xs">
							<div class="br-wrapper br-theme-bootstrap-stars">
								<select id="{{ $term->id }}" class="bootstrap-stars" name="rating" autocomplete="off" style="display: none;">
									<option value=""></option>
									{{-- TODO: also show average rating from other users, but in different color --}}
									@for ($x = 1; $x < 6; $x++)
										{{-- If user is authenticated show personal ratings --}}
										@if (Auth::check())
											@if (isset($stars[$term->id]))
												@if ($stars[$term->id] == $x)
													<option value="{{$x}}" selected>{{$x}}</option>
												@else
													<option value="{{$x}}">{{$x}}</option>
												@endif
											@else
												<option value="{{$x}}">{{$x}}</option>
											@endif
										{{-- show average ratings from all users --}}
										@else
											@if ($term->StarAverage == $x)
												<option value="{{$x}}" selected>{{$x}}</option>
											@else
												<option value="{{$x}}">{{$x}}</option>
											@endif
										@endif
									@endfor
								</select>
							</div>
						</td>
						@if (Gate::allows('contribute-to-collection', $collection) || $editableCollections->count())
							<td class="hidden-xs">
								@if ($collection)
									{{-- Show unlink button --}}
									@if ($collection->id <> $term->collection->id)
										<div class="copy-button-round"><span title="Click to unlink the term from the collection" class="unlink btn btn-warning btn-xs" id="{{ $term->id }}">Unlink</span></div>
									@endif

									{{-- Use the most mature term for the clone option --}}
									@if (($collection->id == $term->collection->id))
										<div class="copy-button-round"><span title="Click to copy term to other Collection" class="clone btn btn-warning btn-xs" id="{{ $term->id }}" data-toggle="modal" data-target="#myModal">Copy</span></div>
									@endif
								@endif
								{{-- Show a button to show the trail --}}
								@if ($term->history)
									<div class="edit-button-round"><a style="text-decoration: none;" href="{{ url('collections') . '/' . $term->collection_id . '/terms/' . $term->id . '/trail' }}"><span title="Show the history and previous versions" class="btn btn-warning btn-xs">History</span></a></div>
								@endif
								@can('contribute-to-collection', $term->collection)
									<div class="edit-button-round"><a style="text-decoration: none;" href="{!! url('collections') . '/' . $term->collection_id . '/terms/' . $term->id . '/edit'; !!}"><span title="Click to edit term" class="btn btn-info btn-xs">Edit term</span></a></div>
								@endcan
							</td>
						@endif
					</tr>
				@endforeach

			</table>
		@endif

		@if ($editableCollections->count())
			@include('modal')
		@endif

		@can('contribute-to-collection', $collection)
			<p class="visible-xs"><a href="{{ route('collections.terms.create', array($collection)) }}">Create Term</a></div></li>
		@endcan
	@endif

@endsection
