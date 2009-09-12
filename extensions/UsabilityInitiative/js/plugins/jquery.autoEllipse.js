/**
 * Plugin that automatically truncates the plain text contents of an element and adds an ellipsis 
 */
( function( $ ) {

$.fn.autoEllipse = function() {
	$(this).each( function() {
		var text = $(this).text();
		var $text = $( '<span />' ).text( text ).css( 'whiteSpace', 'nowrap' );
		$(this).empty().append( $text );
		if ( $text.outerWidth() > $(this).outerWidth() ) {
			var i = text.length;
			while ( $text.outerWidth() > $(this).outerWidth() && i > 0 ) {
				$text.text( text.substr( 0, i ) + '...' );
				i--;
			}
		}
	} );
};

} )( jQuery );