/* JavaScript for NavigableTOC extension */

/*
 * This function should be called on the text area to map out the section
 * character positions by scanning for headings, and the resulting data will
 * be stored as $(this).data( 'outline',  { ... } )
 */
jQuery.fn.parseOutline = function() {
	return this.each( function() {
		// Extract headings from wikitext
		var wikitext = '\r\n' + $(this).val() + '\r\n';
		var headings = wikitext.match( /[\r\n][=]+[^\r\n]*[=]+[\r\n]/g );
		var outline = [];
		var offset = 0;
		for ( var h = 0; h < headings.length; h++ ) {
			text = headings[h];
			// Get position of first occurence
			var position = wikitext.indexOf( text, offset );
			// Update offset to avoid stumbling on duplicate headings
			if ( position > offset ) {
				offset = position;
			} else if ( position == -1 ) {
				// Not sure this is possible, or what should happen
				continue;
			}
			// Trim off whitespace
			text = jQuery.trim( text );
			// Detect the starting and ending heading levels
			var startLevel = 0;
			for ( var c = 0; c < text.length; c++ ) {
				if ( text.charAt( c ) == '=' ) {
					startLevel++;
				} else {
					break;
				}
			}
			var endLevel = 0;
			for ( var c = text.length - 1; c >= 0; c-- ) {
				if ( text.charAt( c ) == '=' ) {
					endLevel++;
				} else {
					break;
				}
			}
			// Use the lowest common denominator as the actual level
			var level = Math.min( startLevel, endLevel );
			text = jQuery.trim( text.substr( level, text.length - ( level * 2 ) ) );
			// Add the heading data to the outline
			outline[h] = {
				'text': text,
				'position': position,
				'level': level,
				'index': h
			};
			/*
			console.log(
				'heading:%s @ %i # %i-%i',
				text,
				position,
				startLevel,
				endLevel
			);
			*/
		}
		// Cache outline
		$(this).data( 'outline', outline )
	} );
};
/*
 * Generate structured UL from outline
 */
jQuery.fn.buildOutline = function( target ) {
	return this.each( function() {
		if ( target.size() ) {
			var outline = $(this).data( 'outline' );
			// Normalize levels, adding an nLevel parameter to each node
			var level = 1;
			for ( var i = 0; i < outline.length; i++ ) {
				if ( i > 0 ) {
					if ( outline[i].level > outline[i - 1].level ) {
						level++;
					} else if ( outline[i].level < outline[i - 1].level ) {
						level -= Math.max(
							1, outline[i - 1].level - outline[i].level
						);
					}
				}
				outline[i].nLevel = level;
				/*
				console.log(
					'%s %s',
					( new Array( level + 1 ).join( ':' ) ),
					outline[i].text
				);
				*/
			}
			
			function buildStructure( outline, structure, offset, level ) {
				if ( offset == undefined ) offset = 0;
				if ( level == undefined ) level = 1;
				for ( var i = offset; i < outline.length; i++ ) {
					if ( outline[i].nLevel == level ) {
						buildStructure( outline, outline[i], i + 1, level + 1 );
						if ( structure.sections == undefined ) {
							structure.sections = [ outline[i] ];
						} else {
							structure.sections[structure.sections.length] = outline[i];
						}
					} else if ( outline[i].nLevel < level ) {
						break;
					}
				}
			}
			function buildList( textarea, structure ) {
				var list = $( '<ul><ul>' );
				for ( i in structure ) {
					var item = $( '<li></li>' )
						.append(
							$( '<a></a>' )
								.attr( 'href', '#' )
								.addClass( 'section-' + structure[i].index )
								.data( 'textbox', textarea )
								.data( 'position', structure[i].position )
								.click( function( event ) {
									$(this).data( 'textbox' ).scrollToPosition(
										$(this).data( 'position' ) - 1
									);
									event.preventDefault();
								} )
								.text( structure[i].text )
						);
					if ( structure[i].sections !== undefined ) {
						item.append( buildList( textarea, structure[i].sections ) );
					}
					list.append( item );
				}
				return list;
			}
			var structure = {};
			buildStructure( outline, structure );
			target.html( buildList( $(this), structure.sections ) );
		}
	} );
};
/*
 * Highlight the section the cursor is currently within
 */
jQuery.fn.updateOutline = function( target ) {
	return this.each( function() {
		var outline = $(this).data( 'outline' );
		var position = $(this).bytePos();
		var i = 0;
		while ( i < outline.length && outline[i].position - 1 <= position ) {
			i++;
		}
		i = Math.max( 0, i - 1 );
		target.find( 'a' ).removeClass( 'currentSelection' );
		target.find( 'a.section-' + i ).addClass( 'currentSelection' );
	} );
};
jQuery( document ).ready( function() {
	jQuery( '#wpTextbox1' ).parseOutline();
	jQuery( '#wpTextbox1' )
		.buildOutline( jQuery( '#navigableTOC' ) )
		.updateOutline( jQuery( '#navigableTOC' ) )
		.bind( 'keyup', { 'list': jQuery( '#navigableTOC' ) }, function( event ) {
			jQuery(this).parseOutline();
			jQuery(this).buildOutline( event.data.list );
		} )
		.bind( 'keyup mouseup scrollToPosition', function() {
			jQuery(this).updateOutline( jQuery( '#navigableTOC' ) );
		} );
});
