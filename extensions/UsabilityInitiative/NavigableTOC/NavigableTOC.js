/* JavaScript for NavigableTOC extension */

/**
 * Add a plugin that can scroll a textarea to a certain location
 */
(function($){
	$.fn.extend({
		/**
		 * Scroll a textarea to a certain offset
		 * @param pos Byte offset in the contents
		 */
		scrollToPosition: function( pos ) {
			return this.each(function() {
				// The next three functions were copied from Wikia's
				// LinkSuggest extension and modified slightly.
				// https://svn.wikia-code.com/wikia/trunk/extensions/wikia/LinkSuggest/LinkSuggest.js
				function getLineLength(control) {
					var width = control.scrollWidth;
					return Math.floor(width/($.os.name == 'linux' ? 7 : 8));
				}

				function getCaret(control) {
					var caretPos = 0;
					// IE Support
					if($.browser.msie) {
						control.focus();
						var sel = document.selection.createRange();
						var sel2 = sel.duplicate();
						sel2.moveToElementText(control);
						var caretPos = -1;
						while(sel2.inRange(sel)) {
							sel2.moveStart('character');
							caretPos++;
						}
					// Firefox support
					} else if (control.selectionStart || control.selectionStart == '0') {
						caretPos = control.selectionStart;
					}
					return caretPos;
				}

				function getCaretPosition(control) {
					var text = control.value.replace(/\r/g, "");
					var caret = getCaret(control);
					var lineLength = getLineLength(control);

					var row = 0;
					var charInLine = 0;
					var lastSpaceInLine = 0;

					for(i = 0; i < caret; i++) {
						charInLine++;
						if(text.charAt(i) == " ") {
							lastSpaceInLine = charInLine;
						} else if(text.charAt(i) == "\n") {
							lastSpaceInLine = 0;
							charInLine = 0;
							row++;
						}
						if(charInLine > lineLength) {
							if(lastSpaceInLine > 0) {
								charInLine = charInLine - lastSpaceInLine;

								lastSpaceInLine = 0;
								row++;
							}
						}
					}

					var nextSpace = 0;
					for(j = caret; j < caret + lineLength; j++) {
						if(text.charAt(j) == " " || text.charAt(j) == "\n" || caret == text.length) {
							nextSpace = j;
							break;
						}
					}

					if(nextSpace > lineLength && caret <= lineLength) {
						charInLine = caret - lastSpaceInLine;
						row++;
					}


					return ($.os.name == 'mac' ? 13 : ($.os.name == 'linux' ? 15 : 16))*row;
				}

				// Put the cursor at the desired position
				this.focus();
				if ( this.selectionStart || this.selectionStart == '0' ) { // Mozilla
					this.selectionStart = this.selectionEnd = pos;
				} else if ( document.selection && document.selection.createRange ) { // IE/Opera
					var range = document.selection.createRange();
					range.moveToElementText( this );
					range.collapse();
					//range.moveStart( 'character', pos );
					range.move( 'character', pos );
					//alert(range.text);
					range.select();
				}
				$(this).scrollTop( getCaretPosition( this ) );
			});
		}
	});
})(jQuery);

$( document ).ready( function() {
	if ( $.section == '' ) {
		// Full page edit
		// Tell the section links what their offsets are
		for ( i = 0; i < $.sectionOffsets.length; i++ )
			$( '.tocsection-' + ( i + 1 ) ).children( 'a' )
				.data( 'offset', $.sectionOffsets[i] );
	} else if ( $.section != 'new' && $.section != 0 ) {
		// Existing section edit
		// Set adjusted offsets on the usable links
		$.section = parseInt( $.section );
		for ( i = 0; i < $.sectionOffsets.length; i++ )
			$( '.tocsection-' + ( i + $.section ) ).children( 'a' )
				.data( 'offset', $.sectionOffsets[i] -
					$.sectionOffsets[0] );
	}
	// Unlink all section links that didn't get an offset
	$( '.toc:last * li' ).each( function() {
		link = $(this).children( 'a' );
		if ( typeof link.data( 'offset') == 'undefined' &&
				link.is( ':visible' ) ) {
			link.hide();
			$(this).prepend( link.html() );
		}
	});

	$( '.toc:last * li a' ).click( function(e) {
		if( typeof jQuery(this).data( 'offset' ) != 'undefined' )
			jQuery( '#wpTextbox1' ).scrollToPosition( jQuery(this).data( 'offset' ) );
			e.preventDefault();
	});

});
