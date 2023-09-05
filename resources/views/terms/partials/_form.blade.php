<!-- /resources/views/terms/partials/_form.blade.php -->

<script src="{{ URL::asset('js/handlebars.js') }}"></script>
<script src="{{ URL::asset('js/typeahead.bundle.js') }}"></script>

<script type="text/javascript">
function myTypeahead() {
	//set url
	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");
	var haunt, repos, sources;
	repos = new Bloodhound({
		datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.value); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		limit: 100,
		/* prefetch: {
			name: 'terms',
			url: myUrl[0] + 'index.php/api/terms',
		} */
		remote: {
			url: url + '/api/terms?search=%QUERY',
			wildcard: '%QUERY'
		}
	});

	//initialize data
	repos.initialize();
	$('input.typeahead').typeahead(null, {
		name: 'repos',
		source: repos.ttAdapter(),
		templates: {
			empty: '<div class="term-box" ><p class="term-collection"></p><p style="margin-left:10px; color:red;" class="term-tername"> No matches</p><p class="term-description"></p></div>',
			suggestion: Handlebars.compile([
				'<div class="dropdown-box" id="@{{id}}">',
				'<p style="color:#f48024;" class="term-collection">@{{collection_name}}</p>',
				'<p class="term-termname">@{{term_name}}</p>',
				'<p class="term-description">@{{term_definition_stripped}}</p>',
				'</div>'
			].join(''))
		}
	});
}
$("document").ready(function(){

	//clear typeahead cache
	localStorage.clear();
	//destroy typeahead
	$('input.typeahead').typeahead('destroy');
	$('input.searcheahead').typeahead('destroy');

	//set clone count to the number of current relations
	var objectsCount = {{ $term->objects->count() }};
	$('body').on('click', '.object-add-more', function(event) {
		//increase objects count
		objectsCount++;
		//temporary disable typeahead on all input dialogs
		$('input.typeahead').typeahead('destroy');
		//clone
		$("div.dropdown-relationships#0").clone().attr('id', objectsCount).appendTo("div#relations-wrapper").find("input[type='text']").val('');
		$("div.dropdown-relationships#" + objectsCount + ' input.hidden-object').attr('name', 'Relations[' + objectsCount + '][object_id]').val('');
		$("div.dropdown-relationships#" + objectsCount + ' input.relationdropdown').attr('name', 'Relations[' + objectsCount + '][relation_name]');
		$("div.dropdown-relationships#0").show();
		//activate typeahead on all input dialogs
		myTypeahead();
		//add delete button
		$('div.dropdown-relationships#' + objectsCount + ' div#last.col-md-1').append("<span><button style=\"margin-top: 10px;\" type=\"button\" class=\"btn btn-danger btn-xs object-remove\">remove</button></span>");
	});

	//function to delete div element when clicking on delete button
	$('body').on('click', '.object-remove', function(event) {
		$(this).closest("div.row").remove();
	});

	//function to delete div element when clicking on delete button
	$('body').on('click', '.property-remove', function(event) {
		$(this).closest("div.row").remove();
	});

	//function when clicking on term, set id
	$('body').on('click', '.dropdown-box', function(event) {
		//get term id
		var object_id = $(this).attr('id');
		console.log(object_id);
		//get id from upper div
		var row_id = $(this).closest("div.dropdown-relationships").attr('id');
		console.log(row_id);
		//set input with id from term
		$('div#' + row_id + '.row.dropdown-relationships input#object_id.hidden-object').val(object_id);
	});

	//initialize typeahead ion initial load
	myTypeahead();

	//function to make dropdown editable
	$(function() {
		$(document).on("click", ".dropdown-menu a" , function() {
			$(this).closest('.dropdown').find('input.relationdropdown').val($(this).attr('data-value'));
		});
	});

	//sync source fields
	$("input#term_name").bind("keyup paste", function() {
		$("input.source_name").val($(this).val());
	});

	//prevent a click on a '#' link from jumping to top of page in jQuery
	$('a[href="#"]').click(function(e) {e.preventDefault(); });

	//add confirmation when deleting term
	$( "#delete" ).click(function() {
		return confirm("Are you sure to delete this term?");
	});

});
</script>

<style>
	hr.split {
		width: 96%;
		margin-left: 2%;
		color:#ecf0f1;
		height: 1px;
		background-color:#ecf0f1;
	}
	div.relations {
		background-color: #f7f7f9;
		border: 1px solid #e1e1e8;
		margin-bottom: 10px;
	}
	div#last {
		padding-left: 0px;
	}
	button.object-add-more, button.property-add-more {
		margin-top:5px;
		margin-bottom: 10px;
	}
	div.term, p#no-relations, button.property-remove {
		margin-top: 10px;
	}
	div#edit, div#archive {
		float:left;
		margin-top: 10px;
	}
	div#archive {
		margin-left:10px;
	}
	div.row.dropdown-relationships {
		margin-top: 4px;
	}
</style>

<div class="form-horizontal">

	<div class="form-group">
		{!! Form::label('term_name', 'Term:', array('class' => 'col-sm-2 control-label')) !!}
		<div class="col-sm-10">
			@if ($term->id)
				{!! Form::text('term_name', $term->term_name, ['class' => 'form-control', 'autofocus' => 'autofocus', 'autocomplete' => 'off', 'autocorrect' => 'off', 'autocapitalize' => 'off', 'spellcheck' => 'false']) !!}
			@else
				{!! Form::text('term_name', null, ['class' => 'form-control', 'autofocus' => 'autofocus']) !!}
			@endif
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('term_definition', 'Description:', array('class' => 'col-sm-2 control-label')) !!}
		<div class="col-sm-10">
			@if ($term->id)
				{!! Form::textarea('term_definition', $term->term_definition, ['class' => 'form-control', 'rows' => '4']) !!}
			@else
				{!! Form::textarea('term_definition', null, ['class' => 'form-control', 'rows' => '4']) !!}
			@endif
		</div>
	</div>

	<div class="form-group">
		{!! Form::label('status_id', 'Status:', array('class' => 'col-sm-2 control-label')) !!}
		<div class="col-sm-6">
		@if ($term->id)
			{!! Form::select('status_id', $statuses->pluck('status_name', 'id'), $term->status_id, ['id' => 'status_id', 'class' => 'form-control']) !!}
		@else
			{!! Form::select('status_id', $statuses->pluck('status_name', 'id'), 1, ['id' => 'status_id', 'class' => 'form-control']) !!}
		@endif
		</div>
	</div>

	{{-- Changing the owner is only allowed when editing --}}
	<div class="form-group">
		{!! Form::label('owner_id', 'Owner:', array('class' => 'col-sm-2 control-label')) !!}
		<div class="col-sm-6">
			@if ($term->id)
				{!! Form::select('owner_id', $owners->pluck('full_details', 'id'), $term->owner_id, ['id' => 'owner_id', 'class' => 'form-control']) !!}
			@else
				{!! Form::select('owner_id', $owners->pluck('full_details', 'id'), Auth::user()->id, ['id' => 'owner_id', 'class' => 'form-control']) !!}
			@endif
		</div>
	</div>

	<hr class="split" />

	{{-- add hidden fields, these are used to validat if the term is unique given the combination of collection id, term_name, status_id and  --}}
	<input type="hidden" name="collection_id" value="{{ $collection->id }}">
	@if ( $term->id )
		<input type="hidden" name="id" value="{{ $term->id }}">
	@endif
	<input type="hidden" name="archived" value="0">

	<div class="relations col-sm-12">

		<!-- start div relations -->
		<div class="term" id="relations">
			@if ($collection->relations->count())
				<div class="term" id="relations-wrapper">
				<h4>Relations</h4>
				@if ($term->objects->count())
					@foreach($term->objects as $key => $object)
						@if ($object->object)
							<div id="{{ $key+1 }}" class="row dropdown-relationships">
								<input class="hidden-object" type="hidden" id="object_id" name="Relations[{{ $key }}][object_id]" value="{{ $object->object->id }}">
								<div class="col-md-3 col-sm-3">
									<input class="form-control source_name" id="disabledInput" type="text" placeholder="{{ $term->term_name }}" disabled>
								</div>
								<div class="col-md-3 col-sm-3">
									<div class="input-group dropdown relations">
										<input name="Relations[{{ $key }}][relation_name]" type="text" class="form-control relationdropdown dropdown-toggle" value="{{ $object->relation->relation_name }}">
										<ul class="dropdown-menu">
										 @if ($collection->relations->count())
											 @foreach($collection->relations as $relation)
												 <li><a href="#" data-value="{{ strtolower($relation->relation_name) }}">{{ strtolower($relation->relation_name) }}</a></li>
											 @endforeach
										 @endif
									</ul>
									<span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
									</div>
								</div>
								<div class="col-md-5 col-sm-5">
									<input name="Relations[{{ $key }}][object_name]" class="form-control typeahead" value="{{ $object->object->term_name }}" type="text" data-provide="typeahead" autocomplete="off">
								</div>
								<div id="last" class="col-md-1 col-sm-1">
									@if ($key > 0)
										<span><button type="button" class="btn btn-danger btn-xs object-remove">remove</button></span>
									@endif
								</div>
							</div>
						@endif
					@endforeach
				@endif
				<div id="0" class="row dropdown-relationships">
					<input class="hidden-object" type="hidden" id="object_id" name="Relations[9999][object_id]" value="">
					<div class="col-md-3 col-sm-3 col-xs-3">
						<input class="form-control source_name" id="disabledInput" type="text" placeholder="{{ $term->term_name }}" disabled>
					</div>
					<div class="col-md-3 col-sm-3">
						<div class="input-group dropdown relations">
						  <input name="Relations[9999][relation_name]" type="text" class="form-control relationdropdown dropdown-toggle" value="">
						  <ul class="dropdown-menu">
							 @if ($collection->relations->count())
								 @foreach($collection->relations as $relation)
									 <li><a href="#" data-value="{{ strtolower($relation->relation_name) }}">{{ strtolower($relation->relation_name) }}</a></li>
								 @endforeach
							 @endif
						  </ul>
						  <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
						</div>
					</div>
					<div class="col-md-5 col-sm-5 col-xs-5">
						<input name="Relations[9999][object_name]" class="form-control typeahead" type="text" placeholder="Search for terms" data-provide="typeahead" autocomplete="off">
					</div>
					<div id="last" class="col-md-1 col-sm-1 col-xs-2">
					</div>
				</div>


				<!-- end div relations -->
				</div>
					<span><button type="button" class="btn btn-success btn-xs object-add-more">add new relation</button></span>
				</div>
			@else
				<p id="no-relations">No relationship types are found for this model. Click <a href="{{ route('collections.relations.index', $collection) }}">here</a> to create a new relationship type</p>
			@endif

			{{-- TODO: also show the relations from other terms (subjects) to this term --}}

		</div>
	</div>

	<div class="form-group" id="edit">
		{!! Form::submit($submit_text, ['class' => 'btn btn-primary', 'name' => 'edit']) !!}
		<input action="action" onclick="history.go(-1);" class="btn btn-default" style="margin-left:7px;" type="button" value="Cancel" />
	</div>
	{{-- Show archive button --}}
	@if ( $term->id )
		<div class="form-group" id="archive">
			{!! Form::submit('Delete Term', ['id' => 'delete', 'class' => 'btn btn-danger', 'name' => 'archive']) !!}
		</div>
	@endif
	</div>
