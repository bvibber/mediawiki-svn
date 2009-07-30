/**
 * Plugin for parsing wikitext, building outlines, and keeping them up to date
 */
( function( $ ){ $.fn.extend( {

/**
 * This function should be called on the text area to map out the section
 * character positions by scanning for headings, and the resulting data will
 * be stored as $(this).data( 'outline',  { ... } )
 */
parseOutline: function() {
	return this.each( function() {
		// Extract headings from wikitext
		var wikitext = '\n' + $(this).val() + '\n';
		var headings = wikitext.match( /\n={1,5}.*={1,5}(?=\n)/g );
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
			text = $.trim( text );
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
			text = $.trim( text.substr( level, text.length - ( level * 2 ) ) );
			// Add the heading data to the outline
			outline[h] = {
				'text': text,
				'position': position,
				'level': level,
				'index': h + 1
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
},
/**
 * Generate structured UL from outline
 * 
 * @param target jQuery selection of element of containers to place list in
 */
buildOutline: function( target ) {
	return this.each( function() {
		if ( target.size() ) {
			var outline = $(this).data( 'outline' );
			// Normalize levels, adding an nLevel parameter to each node
			var level = 1;
			var trunc = 0;
			for ( var i = 0; i < outline.length; i++ ) {
				if ( i > 0 ) {
					if ( outline[i].level > outline[i - 1].level ) {
						level++;
					} else if ( outline[i].level < outline[i - 1].level ) {
						if ( trunc <= 1 ) {
							level -= Math.max(
								1, outline[i - 1].level - outline[i].level
							);
						}
					}
					trunc = outline[i].level - outline[i - 1].level;
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
			function buildStructure( outline, offset, level ) {
				if ( offset == undefined ) offset = 0;
				if ( level == undefined ) level = 1;
				var sections = [];
				for ( var i = offset; i < outline.length; i++ ) {
					if ( outline[i].nLevel == level ) {
						var sub = buildStructure( outline, i + 1, level + 1 );
						if ( sub.length ) {
							outline[i].sections = sub;
						}
						sections[sections.length] = outline[i];
					} else if ( outline[i].nLevel < level ) {
						break;
					}
				}
				return sections;
			}
			function buildList( textarea, structure ) {
				var list = $( '<ul></ul>' );
				for ( i in structure ) {
					var item = $( '<li></li>' )
						.append(
							$( '<a></a>' )
								.attr( 'href', '#' )
								.addClass( 'section-' + structure[i].index )
								.data( 'textbox', textarea )
								.data( 'position', structure[i].position )
								.click( function( event ) {
									$(this).data( 'textbox' )
										.scrollToCaretPosition(
												$(this).data( 'position' )
										);
									event.preventDefault();
								} )
								.text( structure[i].text )
						);
					if ( structure[i].sections !== undefined ) {
						item.append(
							buildList( textarea, structure[i].sections )
						);
					}
					list.append( item );
				}
				return list;
			}
			// Adds special level 1 section 0 item
			var structure = buildStructure( outline );
			structure.unshift( {
				'text': wgTitle,
				'level': 1,
				'index': 0,
				'position': 0
			} );
			target.html( buildList( $(this), structure ) );
		}
	} );
},
/**
 * Highlight the section the cursor is currently within
 * 
 * @param target jQuery selection of element of containers with links to update
 */
updateOutline: function( target ) {
	return this.each( function() {
		var outline = $(this).data( 'outline' );
		var position = $(this).getCaretPosition();
		var section = 0;
		if ( position < outline[section].position - 1 ) {
			// Section 0
		} else {
			while (
				section < outline.length &&
				outline[section].position - 1 < position
			) {
				section++;
			}
			section = Math.max( 0, section );
		}
		target.find( 'a' ).removeClass( 'currentSelection' );
		target.find( 'a.section-' + section ).addClass( 'currentSelection' );
	} );
}

} ); } )( jQuery );