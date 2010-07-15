/**
 * Plugin that highlights matched word partials in a given element
 * TODO: add a function for restoring the previous text
 * TODO: 
 */
( function( $ ) {

$.highlightText = {
	
	// Split our pattern string at spaces and run our highlight function on the results
	splitAndHighlight: function( node, pat ) {
		var patArray = pat.split(" ");
		for ( var i = 0; i < patArray.length; i++ ) {
			if ( patArray[i].length == 0 ) continue;
			$.highlightText.innerHighlight( node, patArray[i] );
		}
		return node;
	},
	// adapted from Johann Burkard's highlight plugin
	// http://johannburkard.de/blog/programming/javascript/highlight-javascript-text-higlighting-jquery-plugin.html
	// scans a node looking for the pattern and wraps a span around each match 
	innerHighlight: function( node, pat ) {
		var skip = 0;
		if ( node.nodeType == 3 ) {
			// TODO - need to be smarter about the character matching here. 
			// non latin characters can make regex think a new word has begun. 
			var pos = node.data.search( new RegExp( "\\b" + RegExp.escape( pat ), "i" ) );
			if ( pos >= 0 ) {
				var spannode = document.createElement( 'span' );
				spannode.className = 'highlight';
				var middlebit = node.splitText( pos );
				var endbit = middlebit.splitText( pat.length );
				var middleclone = middlebit.cloneNode( true );
				spannode.appendChild( middleclone );
				middlebit.parentNode.replaceChild( spannode, middlebit );
				skip = 1;
			}
		} else if ( node.nodeType == 1 && node.childNodes && !/(script|style)/i.test( node.tagName )
				&& !( node.tagName.toLowerCase() == 'span' && node.classList.contains( 'highlight' ) ) ) {
			for ( var i = 0; i < node.childNodes.length; ++i ) {
				i += $.highlightText.innerHighlight( node.childNodes[i], pat );
			}
		}
		return skip;
	}
};

$.fn.highlightText = function( matchString ) {
	return $( this ).each( function() {
		var $this = $( this );
		$this.data( 'highlightText', { originalText: $this.text() } );
		$.highlightText.splitAndHighlight( this, matchString );
	} );
};

} )( jQuery );

