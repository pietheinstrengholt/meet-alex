<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Copy term dialog</h4>
		</div>
		<div class="modal-body">
			<div class="form-horizontal">
				<br><h4>To which Model do you want to copy this term to?</h4>
				<div class="form-group">
					{!! Form::label('collection_id', 'Model:', array('class' => 'col-sm-3 control-label')) !!}
					<div class="col-sm-6">
						{!! Form::select('collection_id', $editableCollections->pluck('collection_name', 'id'), null, ['id' => 'collection_id', 'class' => 'form-control']) !!}
					</div>
				</div>
				<br>
				<div id="relation" style="display:none;">
				<h4>Do you want to use a relation?</h4>
					<div class="form-group">
						<label class="col-sm-3 control-label">Relation type:</label>
						<div class="col-sm-6">
							<select class="form-control" id="relation_id">
								<option>None</option>
							</select>
					  	</div>
				  	</div>
				</div>
				<input type="hidden" id="term_id" name="term_id" value="0">
				<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
				<br><br><br><br><br><br>
			</div>
		</div>
		<div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
		<button type="button" id="link" class="btn btn-warning" data-dismiss="modal">Link Term</button>
		<button type="button" id="clone" class="btn btn-warning" data-dismiss="modal">Copy Term</button>
	  </div>
	</div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>

$( document ).ready(function() {

	//function to retrieve relations for collection
	function fnGetRelations(collection_id) {

		//get url for ajax call
		var url = $('meta[name="base_url"]').attr('content');

		//remove all existing options from select
		$('div#relation select option').each(function() {
			$(this).remove();
		});

		//perform ajax call to get all relations types for a specific collection
		$.ajax({
			type: "GET",
			url: url + "/api/relations/collection/" + collection_id,
			success: function (json) {
				//if json is empty hide relation dialog
				if ( json.length == 0 ) {
					$("div#relation").hide();
				//else show relation dialog and fill options based on json results
				} else {
					$('div#relation select').append('<option>No relation to be used</option>');
					$.each(json, function(key, relation) {
						$('div#relation select').append('<option id="' + relation["id"] + '">' + relation["relation_name"] + '</option>');
					});
					//show relation div when a collection is selected
					$("div#relation").show();
				}
			},
			failure: function (errMsg) {
				console.log(errMsg);
			}
		});
	}

	//set the dropdown the first time
	var collection_id = $( "#collection_id option:selected" ).val();
	fnGetRelations(collection_id);

	//if the clone button is clicked show modal and call function to retrieve relations for first collection
	$("span.clone").click(function() {
		var term_id = this.id;
		$('input#term_id').attr('value',term_id);
	});

	$('#collection_id').change(function(){
		var collection_id = $(this).val();
		fnGetRelations(collection_id);
	});

	$("button#clone").click(function() {
		var term_id = $('#myModal input[name="term_id"]').attr('value');
		var token = $('#myModal input[name="_token"]').attr('value');
		var collection_id = $("#collection_id").val();
		var relation_id = $("#relation_id").children(":selected").attr("id");

		var url = $('meta[name="base_url"]').attr('content');

		$.ajax({
			type: "POST",
			url: url + "/api/terms/clone",
			data: {
				"term_id": term_id,
				"collection_id": collection_id,
				"relation_id": relation_id,
				_token: token
			},
			success: function (json) {
				if (json.code == "201") {
					$('.container.inner').prepend(
						'<div class="alert alert-success alert-dismissible">' +
							'<button type="button" class="close" data-dismiss="alert">' +
							'&times;</button>' + "Term successfully cloned to collection." + '</div>');
				}
			},
			failure: function (errMsg) {
				console.log(errMsg);
			}
		});
	});

	$("button#link").click(function() {
		var term_id = $('#myModal input[name="term_id"]').attr('value');
		var token = $('#myModal input[name="_token"]').attr('value');
		var collection_id = $("select#collection_id").val();
		var url = $('meta[name="base_url"]').attr('content');

		$.ajax({
			type: "POST",
			url: url + "/api/terms/link",
			data: {
				"term_id": term_id,
				"collection_id": collection_id,
				_token: token
			},
			success: function (json) {
				if (json.code == "201") {
					$('.container.inner').prepend(
						'<div class="alert alert-success alert-dismissible">' +
							'<button type="button" class="close" data-dismiss="alert">' +
							'&times;</button>' + "Term successfully linked to collection." + '</div>');
				}
			},
			error: function (errMsg) {
				console.log(errMsg);
				if (json.code == "400") {
					$('.container.inner').prepend(
						'<div class="alert alert-warning alert-dismissible">' +
							'<button type="button" class="close" data-dismiss="alert">' +
							'&times;</button>' + "Cannot link term, since a term with the same name already exists in this collection." + '</div>');
				}
			}
		});
	});
});

</script>
