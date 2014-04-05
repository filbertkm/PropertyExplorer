( function( $ ) {
  $( '#form_category' ).autocomplete({
    source: function( req, add ) {
		$.getJSON(
			"http://en.wikipedia.org/w/api.php?action=opensearch&search="
				+ req.term + "&namespace=14&format=json&callback=?",
			req,
			function( data ) {
				var trimmed = []

				$.each( data[1], function( key, suggestion ) {
					trimmed.push( suggestion.substring( suggestion.indexOf( ':' ) + 1 ) );
				});

				add( trimmed );
			}
		);
	}
  });
} )( jQuery );
