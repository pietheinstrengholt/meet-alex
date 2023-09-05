function trackClick(link) {
	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");
	var _token = $('meta[name="_token"]').attr('content');
	$.post({
		url: url + '/api/track-click',
		data: {
			'_token': _token,
			'link_target': $(link).attr('href')
		}
	});
}

$( document ).ready(function() {
	$("body").on("click", ".unlink", function() {
		var _token = $('meta[name="_token"]').attr('content');
		var term_id = $(this).attr("id");
		var collection_id = $('input#collection_id').val();
		var url = $('meta[name="base_url"]').attr('content');
		$.ajax({
			type: "POST",
			url: url + "/api/terms/unlink",
			data: {
				"term_id": term_id,
				"collection_id": collection_id,
				_token: _token
			},
			success: function (json) {
				if (json.code == "201") {
					//show message that term has been unlinked
					$('.container.inner').prepend(
					'<div class="alert alert-success alert-dismissible">' +
					'<button type="button" class="close" data-dismiss="alert">' +
					'&times;</button>' + "Term successfully unlinked from collection." + '</div>');

					//remove tr line from table
					$('tr#' + term_id).remove();
				}
			},
			error: function (errMsg) {
				console.log(errMsg);
			}
		});
	});
});

//automatically close bootstrap alerts after a few seconds
$(document).ready (function() {
	$(".alert-dismissible").fadeTo(5000, 500).slideUp(500, function(){
		$(".alert-dismissible").alert('close');
	});
});

function bookmarkCollection(collection) {
	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");
	var _token = $('meta[name="_token"]').attr('content');

	//send json request
	$.ajax({
		type: "POST",
		url: url + "/api/users/bookmark",
		data: {
			'_token': _token,
			'collection_id': $(collection).attr('id')
		},
		async: false,
		success: function (json) {
			if (json == "unbookmarked") {
				$(collection).find('.bookmark').text("Follow");
			}

			if (json == "bookmarked") {
				$(collection).find('.bookmark').text("Unfollow");
			}

			//reload page when button class has refresh
			if ($(collection).find('.bookmark').hasClass( "refresh" )) {
				location.reload();
			}
		},
		failure: function (errMsg) {}
	});
}

function copyToClipboard(link) {
	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");
	var api = url + '/api/collections/' + $(link).attr('id');

	//copy to clipboard function
	if (window.clipboardData && window.clipboardData.setData) {
		// IE specific code path to prevent textarea being shown while dialog is visible.
		return clipboardData.setData("Text", api);

	} else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
		var textarea = document.createElement("textarea");
		textarea.textContent = api;
		textarea.style.position = "fixed";	// Prevent scrolling to bottom of page in MS Edge.
		document.body.appendChild(textarea);
		textarea.select();
		try {
			return document.execCommand("copy");	// Security exception may be thrown by some browsers.
		} catch (ex) {
			console.warn("Copy to clipboard failed.", ex);
			return false;
		} finally {
			document.body.removeChild(textarea);
		}
	}
}

function deleteComment(comment) {
	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");
	var _token = $('meta[name="_token"]').attr('content');
	console.log($(comment).attr('id'));

	//send json request
	$.ajax({
		type: "POST",
		url: url + "/api/deletecomment",
		data: {
			'_token': _token,
			'comment_id': $(comment).attr('id')
		},
		async: false,
		success: function (json) {
			if (json.code == "200") {
				//empty comment
				$("ul.commentList").find('li#' + $(comment).attr('id') + '.comment').remove();
			}
		},
		failure: function (errMsg) {}
	});
}

function myTypeahead() {
	//set url
	var myRegex = /.+?(?=index.php)/;
	var myUrl = $('meta[name="base_url"]').attr('content');
	var _token = $('meta[name="_token"]').attr('content');
	myUrl = myUrl.replace("/index.php", "");

	var haunt, repos, sources;
	repos = new Bloodhound({
		datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.value); },
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		limit: 100,
		/* use the prefetch option to load all terms in cache, we don't want to do this now.. */
		/* prefetch: {
			name: 'terms',
			url: myUrl + 'index.php/api/terms',
		} */
		remote: {
			url: myUrl + '/index.php/api/search?limit=10&_token=' + _token + '&search=%QUERY',
			wildcard: '%QUERY'
		}
	});

	//initialize data
	repos.initialize();

	$('input.typeahead').typeahead(null, {
		name: 'repos',
		source: repos.ttAdapter(),
		templates: {
			empty: '<div class="term-box"><p class="term-collection"></p><p style="margin-left:10px; color:red;" class="term-tername"> No matches</p><p class="term-description"></p></div>',
			suggestion: Handlebars.compile([
				'<div class="term-box" id="{{collection.id}}">',
					'<p style="color:#f48024;" id="{{collection.id}}" class="term-collection collection">{{collection.collection_name}}</p>',
					'<p style="color:#2c3e50;" id="{{id}}" class="term-termname term">{{term_name}}</p>',
					'<p style="color:#f48024;" id="{{id}}" class="term-termname collection">{{collection_name}}</p>',
					'<p style="color:#354b60;" class="term-description term">{{term_definition_stripped}}</p>',
					'<p style="color:#354b60;" class="term-description collection">{{collection_description}}</p>',
				'</div>'
			].join(''))
		}
	});
}

$("document").ready(function(){

	//clear typeahead cache
	localStorage.clear();

	//destroy typeahead on initial load
	$('input.typeahead').typeahead('destroy');

	var url = $('meta[name="base_url"]').attr('content');
	url = url.replace("/index.php", "");

	//function when clicking on term, set id
	$('body').on('click', '.term-box', function(event) {

		//get collection id based on the collection id propertie
		var term_box = $(this).attr('id');

		//if the property is empty the user clicked directly on a collection
		if (term_box === "" || term_box === undefined) {
			//get collection_id
			 var collection_id = $(this).find(".term-termname.collection").attr('id');

			//redirect to term page
	 		window.location.href = url + '/index.php/collections/' + collection_id;
		} else {
			//get term_id
			var term_id = $(this).find(".term-termname.term").attr('id');

			//redirect to term page
			window.location.href = url + '/index.php/collections/' + term_box + '/terms/' + term_id;
		}

	});

	//initialize typeahead ion initial load
	myTypeahead();
});
