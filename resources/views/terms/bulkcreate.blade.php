<!-- /resources/views/terms/bulkcreate.blade.php -->
@extends('layouts.app')

@section('content')

	<ul class="breadcrumb breadcrumb-section">
		<li><a href="{!! url('/'); !!}">Home</a></li>
		<li><a href="{!! route('collections.index'); !!}">Collections</a></li>
		<li><a href="{{ route('collections.show', $collection->id) }}">{{ $collection->collection_name }}</a></li>
		<li class="active">Create Term</li>
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
			remote: {
				url: url + '/api/terms?collection_id=' + $('input#collection_id').val() + '&search=%QUERY',
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
			$("div.dropdown-relationships#0").clone().attr('id', objectsCount).appendTo("div#relations-wrapper").find("input[type='text']").val("");
			$("div.dropdown-relationships#" + objectsCount + ' input.subject_name').attr('name', 'terms[' + objectsCount + '][subject_name]');
			$("div.dropdown-relationships#" + objectsCount + ' input.object_name').attr('name', 'terms[' + objectsCount + '][object_name]');
			$("div.dropdown-relationships#" + objectsCount + ' input.object_id').attr('name', 'terms[' + objectsCount + '][object_id]');
			$("div.dropdown-relationships#" + objectsCount + ' input.relationdropdown').attr('name', 'terms[' + objectsCount + '][relation_name]');
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

		//function when clicking on term, set id
		$('body').on('click', '.dropdown-box', function(event) {
			//get term id
			var object_id = $(this).attr('id');

			//get id from upper div
			var row_id = $(this).closest("div.dropdown-relationships").attr('id');
			//set input with id from term
			$('div#' + row_id + '.dropdown-relationships input#object_id').val(object_id);
		});

		//initialize typeahead ion initial load
		myTypeahead();

		//function to make dropdown editable
		$(function() {
			$(document).on("click", ".dropdown-menu a" , function() {
				$(this).closest('.dropdown').find('input.relationdropdown').val($(this).attr('data-value'));
			});
		});

		//prevent a click on a '#' link from jumping to top of page in jQuery
		$('a[href="#"]').click(function(e) {e.preventDefault(); });

		$.fn.serializeControls = function() {
			var data = {};

			function buildInputObject(arr, val) {
				if (arr.length < 1)
				return val;
				var objkey = arr[0];
				if (objkey.slice(-1) == "]") {
					objkey = objkey.slice(0,-1);
				}
				var result = {};
				if (arr.length == 1){
					result[objkey] = val;
				} else {
					arr.shift();
					var nestedVal = buildInputObject(arr,val);
					result[objkey] = nestedVal;
				}
				return result;
			}

			$.each(this.serializeArray(), function() {
				var val = this.value;
				var c = this.name.split("[");
				var a = buildInputObject(c, val);
				$.extend(true, data, a);
			});

			return data;
		}


		$('body').on('click', 'button#save', function(event) {
			var data = $('form#bulkcreate').serializeControls();
			console.log(data);
			var token = $('meta[name="_token"]').attr('content');
			var url = $('meta[name="base_url"]').attr('content');
			//send ajax request to backend
			$.ajax({
				type: "POST",
				url: url + "/api/triples",
				data: {
					"collection_id": $('input#collection_id').val(),
					"data": data,
					_token: token
				},
				success: function (json) {
					if (json.data) {
						console.log(json.data);
					}
					console.log(json.message);
					//redirect to term page
					window.location.href = url + '/collections/' + $('input#collection_id').val();
				},
				failure: function (errMsg) {
					console.log(errMsg);
				}
			});
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
			margin-left:25px;
		}

		div.row.dropdown-relationships {
			margin-top: 4px;
		}
	</style>

	<h2>Create multiple terms</h2>

	<h4>Welcome on the bulkcreate page. You can use the form below to create multiple terms at once. Use the 'add new line' button to create an additional item. Also be aware that when you click the 'save terms' button at the bottom of the page, all terms will be saved and you will be redirected to the collection overview page.</h4>

	<div class="form-horizontal">

		{{-- add hidden fields, these are used to validat if the term is unique given the combination of collection id, term_name, status_id and  --}}
		<input type="hidden" id="collection_id" name="collection_id" value="{{ $collection->id }}">

		<div class="relations col-sm-12">

			<!-- start div relations -->
			<form id="bulkcreate">
				<div class="term" id="relations">
					<div class="term" id="relations-wrapper">
					<div class="row">
						<div class="col-md-4 col-sm-4 col-xs-12"><h4>Term</h4></div>
						<div class="col-md-3 col-sm-3 col-xs-12"><h4>Relation</h4></div>
						<div class="col-md-5 col-sm-5 col-xs-12"><h4>Term</h4></div>
					</div>
					<div id="0" class="dropdown-relationships">
						<div class="row">
							<div class="col-md-4 col-sm-4 col-xs-12">
								<input name="terms[0][subject_name]" class="form-control subject_name" id="subject_name" type="text" placeholder="Enter term name">
							</div>
							<div class="col-md-3 col-sm-3 col-xs-12">
								<div class="input-group dropdown relations">
								<input name="terms[0][relation_name]" type="text" id="relation_name" class="form-control relationdropdown dropdown-toggle" value="">
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
							<div class="col-md-5 col-sm-5 col-xs-12">
								<input name="terms[0][object_name]" class="form-control typeahead object_name" id="object_name" type="text" placeholder="Search for terms" data-provide="typeahead" autocomplete="off">
							</div>
							<input type="hidden" class="object_id" name="terms[0][object_id]" id="object_id" value="">
						</div>
						<hr style="margin-top: 0px; margin-bottom: 15px;"/>
					</div>

					<!-- end div relations -->
					</div>
						<span><button type="button" class="btn btn-success btn-xs object-add-more">Add new line</button></span>
					</div>
				</div>
			</form>
		</div>

		<div class="form-group" id="edit">
			<button type="button" class="btn btn-success" id="save">Save terms</button>
			<input action="action" onclick="history.go(-1);" class="btn btn-default" style="margin-left:7px;" type="button" value="Cancel" />
		</div>

	</div>
@endsection
