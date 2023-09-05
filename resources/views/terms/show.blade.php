<!-- /resources/views/terms/show.blade.php -->
@extends('layouts.app')

@section('content')

	<style>
		div.icon-collection {
			background-image: url("{{ URL::asset('img/pyramid.svg') }}");
			background-size: 22px;
			background-repeat: no-repeat;
			background-position:100%;
			width:100px;
			float:right;
			margin: 0em;
		}
			.dropzone {
				border-radius: 5px;
				//border: 1px dashed transparent;
			}
			.dropzone:hover {
				border-radius: 3px;
				//border: 1px dashed grey;
			}
			.move {
				cursor: move;
			}
			.no-drop {
				cursor: no-drop;
			}
	</style>

	<script src="{{ URL::asset('js/dragdrop.js') }}"></script>


	@include('visual.header')

	<!-- Meta data element needed in order to visualise d3js graph -->
	<meta name="term_id" content="{{ $term->id }}">
	@if ($fullscreen)
		<meta name="fullscreen" content="yes">
	@else
		<meta name="fullscreen" content="no">
	@endif

	<ul class="breadcrumb breadcrumb-section">
	 <li><a href="{!! url('/'); !!}">Home</a></li>
	 <li class="hidden-xs"><a href="{!! url('/collections'); !!}">Collections</a></li>
	 <li><a onclick="trackClick(this)" href="{{ route('collections.show', $term->collection) }}">{{ $term->collection->collection_name }}</a></li>
	 <li class="active">{{ $term->term_name }}</li>
	 @if ($editableCollections->contains($term->collection))
	 	<li class="right dropdown" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $term->collection_id . '/terms/' . $term->id . '/edit'; !!}"><span title="Click to edit term" class="label label-info label-as-badge">Edit term</span></a></div></li>
	 @endif
	 @if ($fullscreen)
	 	<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $term->collection_id . '/terms/' . $term->id; !!}"><span title="Click to edit term" class="label label-danger label-as-badge">Show normal view</span></a></div></li>
	 @else
	 	<li class="right dropdown hidden-xs" id="status"><div class="button-right"><a href="{!! url('collections') . '/' . $term->collection_id . '/terms/' . $term->id . '?fullscreen=yes'; !!}"><span title="Click to edit term" class="label label-danger label-as-badge">Show visual fullscreen</span></a></div></li>
	 @endif
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

	@if (!$fullscreen)

		<div class="row">
			<div id="not-fullscreen" class="col-md-7 col-xs-12">

			<h3>{{ $term->term_name }}</h3>
			<blockquote>{!! Format::contentAdjust($term->term_definition, $term->collection_id) !!}</blockquote>

			<!-- Rating -->
			<dl>
				<!--dt>Average rating:</dt-->
				<dd id="term_stars">
					<div class="br-wrapper br-theme-bootstrap-stars">
						<select id="{{ $term->id }}" class="bootstrap-stars" name="rating" autocomplete="off" style="display: none;">
							<option value=""></option>
							{{-- TODO: also show average rating from other users, but in different color --}}
							@for ($x = 1; $x < 6; $x++)
								{{-- show average ratings from all users --}}
								@if ($term->StarAverage == $x)
									<option value="{{$x}}" selected>{{$x}}</option>
								@else
									<option value="{{$x}}">{{$x}}</option>
								@endif
							@endfor
						</select>
					</div>
				</dd>
			</dl>

			<!--<p>Explanatory note</p>-->
			<p>Here we can show explanatory notes once available in the meet-Alex database.</p><br/>


		<!-- Synonyms and Homonyms -->
		@if (!empty($term->synonyms) || !empty($term->homonyms))
		<div class="row">

			<!-- Synonyms -->
					<div class="col-md-6 col-xs-12">
						<div class="panel panel-default">
							<!-- Default panel contents -->
							<div class="panel-heading">
								<h3 class="panel-title"><div class="icon-collection">&nbsp;</div>Synonyms </h3>
							</div>

							<ul class="list-group">
								<?php $i = 0; ?>

									@foreach($term->synonyms as $key => $synonym)
										<?php $i = $i + 1; ?>
										<li class="list-group-item">
												<span class="label label-success" style="float:right;"><a style="color:white; font-size: 14px;" onclick="trackClick(this)" href="{!! url('collections') . '/' . $synonym->collection_id; !!}">{{ $synonym->collection->short_name }}</a></span>
												<span class="label label-default" ><a style="color:white; font-size: 14px;" role="button" data-toggle="collapse" href="#collapseSynonym{{ $i }}" aria-expanded="false" aria-controls="collapseSynonym{{ $i }}">{!! $synonym->term_name !!}</a></span>
												<div class="collapse" id="collapseSynonym{{ $i }}">
													<div class="well-sm">
														<a onclick="trackClick(this)" href="{!! route('collections.terms.show',['collection'=>$synonym->collection,'term'=>$synonym]); !!}">> {{ $synonym->term_definition }}</a>
													</div>
												</div>
										</li>
									@endforeach
									<li class="list-group-item">
										<div class="dropzone" id="PanelSynonym" ondrop="drop(event,this)" ondragover="allowDrop(event)">
										&nbsp;
										</div>
									</li>

							</ul>
						</div>
					</div>

			<!-- Homonyms -->
					<div class="col-md-6 col-xs-12">
						<div class="panel panel-default">
							<!-- Default panel contents -->
							<div class="panel-heading">
								<h3 class="panel-title"><div class="icon-collection">&nbsp;</div> Other term occurrences </h3>
							</div>
							<ul class="list-group">

							<?php $j = 0; ?>

								@foreach($term->homonyms as $key => $homonym)
									<?php $i = $i + 1; ?>
									<li class="list-group-item">
										<div class="move" draggable="true" ondragstart="drag(event)" id="drag{{ $i }}">
											<span class="label label-success" style="float:right;"><a style="color:white; font-size: 14px;" onclick="trackClick(this)" href="{!! url('collections') . '/' . $homonym->collection_id; !!}">{{ $homonym->collection->short_name }}</a></span>
											<span class="label label-default" ><a style="color:white; font-size: 14px;" role="button" data-toggle="collapse" href="#collapseHomonym{{ $i }}" aria-expanded="false" aria-controls="collapseHomonym{{ $i }}">{!! $homonym->term_name !!}</a></span>
											<div class="collapse" id="collapseHomonym{{ $i }}">
												<div class="well-sm">
													<a onclick="trackClick(this)" href="{!! route('collections.terms.show',['collection'=>$homonym->collection,'term'=>$homonym]); !!}">> {{ $homonym->term_definition }}</a>
												</div>
											</div>
										</div>
									</li>
								@endforeach
								<li class="list-group-item">
									<div class="dropzone" id="PanelHomononym" ondrop="drop(event,this)" ondragover="allowDrop(event)">
									&nbsp;
									</div>
								</li>

							</ul>
						</div>
					</div>

		</div>
		@endif



			<!-- Relations to other terms (objects and subjects) -->
			@if (!empty($term->objects) || !empty($term->subjects))
				<div class="panel panel-default">
					<!-- Default panel contents -->
					<div class="panel-heading">
						<h3 class="panel-title"><div class="icon-collection">&nbsp;</div>Relations to other terms </h3>
					</div>
					<ul class="list-group">
					@if (!empty($term->objects))
						@foreach($term->objects as $key => $object)
							@if ($object->object)
								<li class="list-group-item">
									<strong>{{ $term->term_name }}</strong>
									{{ strtolower($object->relation->relation_name) }}
									<a onclick="trackClick(this)" href="{!! route('collections.terms.show',['collection'=>$object->object->collection,'term'=>$object->object]); !!}">{{ $object->object->term_name }}</a>
									<span class="label label-success" style="float:right;"><a style="color:white; font-size: 14px;" onclick="trackClick(this)" href="{!! url('collections') . '/' . $object->object->collection_id; !!}">{{ $object->object->collection->short_name }}</a></span>
								</li>
							@endif
						@endforeach
					@endif
					@if (!empty($term->subjects) || true)
						@foreach($term->subjects as $key => $subject)
							@if ($subject->$subject || true)
								<li class="list-group-item">
									<a onclick="trackClick(this)" href="{!! route('collections.terms.show',['collection'=>$subject->subject->collection,'term'=>$subject->subject]); !!}">{{ $subject->subject->term_name }}</a>
									{{ strtolower($subject->relation->relation_name) }}
									<strong>{{ $term->term_name }}</strong>
									<span class="label label-success" style="float:right;"><a style="color:white; font-size: 14px;" onclick="trackClick(this)" href="{!! url('collections') . '/' . $subject->subject->collection_id; !!}">{{ $subject->subject->collection->short_name }}</a></span>
								</li>
							@endif
						@endforeach
					@endif
					</ul>
				</div>
			@endif
			</div>

			<div id="not-fullscreen" class="col-md-5 col-xs-12">
				{{-- show the visual --}}
				<div class="row" id="sidebar">
					@include('visual.mcontainer')
				</div>
			</div>

		<!-- end of row -->
		</div>

	@endif

	@if ($fullscreen)
	<div class="row">
		<div id="fullscreen" class="col-md-12 col-xs-12">
			{{-- show the visual --}}
			<div class="row" id="sidebar">
				@include('visual.mcontainer')
			</div>
		</div>
	</div>

	@endif


	@if (!$fullscreen)
		@if (Auth::check())
			<br/>&nbsp;

			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Comments</h3>
						</div>
					 	<div class="panel-body">
							<ul class="commentList">
							@foreach( $comments as $key => $comment )
								@if (!empty($comment->reviewer))
									<li class="comment" id="{{ $comment->id }}">
										<div class="commenterImage">
											<img src="{{ $comment->reviewer->gravatar }}" />
										</div>
										<div class="commentText">
											<p class="">{{ $comment->comment }}</p> <span class="date sub-text">{{ date_format( $comment->created_at,"d M Y H:i:s") }} by {{ $comment->reviewer->full_details }}</span>
												@if ($editableCollections->contains($term->collection))
													<small><a onclick="deleteComment(this)" id="{{ $comment->id }}">(delete comment)</a></small>
												@endif
										</div>
									</li>
								@endif
							@endforeach
							</ul>
							{!! Form::open(['action' => ['CommentController@create', $collection->id, $term->id], 'class' => 'form-inline']) !!}
								<input type="hidden" name="term_id" value="{{ $term->id }}">
								<div class="form-group">
									<textarea class="form-control" rows="2" cols="70" type="text" name="comment" placeholder="Your comments"></textarea>
								</div>
								<div class="form-group">
									<button class="btn btn-default">Add</button>
								</div>
							{!! Form::close() !!}
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 col-xs-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">About this term</h3>
						</div>
						<div class="panel-body">
							<!-- Last modified -->
							<dl class="dl-horizontal">
								<dt class="label label-info" style="font-size: 14px;">Last edited on</dt>
								<dd id="term_updated">{{ date_format($term->updated_at,"d  M  Y") }}</dd>
							</dl>
							<!-- Term owner -->
							<dl class="dl-horizontal">
								<dt class="label label-info" style="font-size: 14px;">Term owner</dt>
								<dd id="term_owner">{{ $term->owner->display_name }}</dd>
							</dl>

							<!-- Term status -->
							<dl class="dl-horizontal">
								<dt class="label label-info" style="font-size: 14px;">Term status</dt>
								<dd id="term_status">{{ $term->status_name }}</dd>
							</dl>

							<!-- More details -->
							<a class="label label-default label-as-badge" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								More information
							</a>

							<div class="collapse" id="collapseExample">
								<div class="well">
									<p>Please contact the term owner for more information on the term, its description and the relations to other terms.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif
	@endif

	@include('visual.footer')

@endsection
