// jQuery(document).ready(function($) {
// 	var data = {
// 		'action': 'wt_search',
// 		'whatevez': ajax_object.we_valux      // We pass php values differently!
// 	};
// 	// We can also pass the url value separately from ajaxurl for front end AJAX implementations
// 	jQuery.post(ajax_object.ajax_url, data, function(response) {
// 		alert('Got this from the server: ' + response);
// 	});
// });

jQuery( function() {
	var availableTags = [
	"ActionScript",
	"AppleScript",
	"Asp",
	"BASIC",
	"C",
	"C++",
	"Clojure",
	"COBOL",
	"ColdFusion",
	"Erlang",
	"Fortran",
	"Groovy",
	"Haskell",
	"Java",
	"JavaScript",
	"Lisp",
	"Perl",
	"PHP",
	"Python",
	"Ruby",
	"Scala",
	"Scheme"
	];

	jQuery( "#input-text" ).autocomplete( {
		source: function (request, response) { 
			jQuery.post( 
				ajax_object.ajax_url,
				{ 
					'action': 'wt_search',
					'f' : request.term 
				},
				function (data) {
					response(data).slice(0,3);
				}
				);
		},
	} );
} );