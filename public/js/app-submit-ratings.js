$( document ).ready(function() {
	//get variables needed for ajax call
	var url = $('meta[name="base_url"]').attr('content');
	var token = $('meta[name="_token"]').attr('content');
	$('.bootstrap-stars').barrating('show', {
		onSelect: function(value, text, event) {
			if (typeof(event) !== 'undefined') {
				// rating was selected by a user
				var term_id = this.$elem.attr('id');
				$.ajax({
					type: "POST",
					url: url + "/api/terms/star",
					data: {
						"term_id": term_id,
						"star_amount": value,
						_token: token
					},
					success: function (json) {
						console.log(json);
					},
					failure: function (errMsg) {
						console.log(errMsg);
					}
				});
			}
		}
	});
});
