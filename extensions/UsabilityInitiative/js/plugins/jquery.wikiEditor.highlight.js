/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Internally used event handlers
 */
evt: {
	change: function( context, event ) {
		/*
		 * Triggered on any of the following events, with the intent on detecting if something was added, deleted or
		 * replaced due to user action.
		 *
		 * The following conditions are indicative that one or more divisions need to be re-scanned/marked:
		 * 		Keypress while something is highlighted
		 * 		Cut
		 * 		Paste
		 * 		Drag+drop selected text
		 * The following conditions are indicative that special handlers need to be consulted to properly parse content
		 * 		Keypress with any of the following characters
		 * 			}	Template or Table handler
		 * 			>	Tag handler
		 * 			]	Link handler
		 * The following conditions are indicative that divisions might be being made which would need encapsulation
		 * 		Keypress with any of the following characters
		 * 			=	Heading
		 * 			#	Ordered
		 * 			*	Unordered
		 * 			;	Definition
		 * 			:	Definition
		 */
		if ( event.data.scope == 'do_not_trigger' ) {
			$.wikiEditor.modules.highlight.fn.scan( context, "" );
			$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
		}
	}
},

/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a highlight module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// hook $.wikiEditor.modules.highlight.evt.change to context.evt.change
	},
	divide: function( context ) {
		/*
		 * We need to add some markup to the iframe content to encapsulate divisions
		 */
	},
	isolate: function( context ) {
		/*
		 * A change just occured, and we need to know which sections were affected
		 */
		return []; // array of sections?
	},
	strip: function( context, division ) {
		return $( '<div />' ).html( division.html().replace( /\<br[^\>]*\>/g, "\n" ) ).text();
	},
	tokenArray: [],
	scan: function( context, division ) {
		// We need to look over some text and find interesting areas, then return the
		// positions of those areas as tokens
		function Token( offset, label ) {
			this.offset = offset;
			this.label = label;
		}
		
		this.tokenArray = [];
		var text = context.fn.getContents();
		for ( module in $.wikiEditor.modules ) {
			if ( 'exp' in $.wikiEditor.modules[module] ) {
			   for ( var i = 0; i < $.wikiEditor.modules[module].exp.length; i++ ) {
					var regex = $.wikiEditor.modules[module].exp[i].regex;
					var label = $.wikiEditor.modules[module].exp[i].label;
					var markAfter = false;
					if ( typeof $.wikiEditor.modules[module].exp[i].markAfter != 'undefined' ) {
						markAfter = true;
					}
					match = text.match( regex );
					var oldOffset = 0;
					while ( match != null ) {
						var markOffset = 0;
						if ( markAfter ) {
							markOffset += match[0].length;
						}
						this.tokenArray.push( new Token(
							match.index + oldOffset + markOffset, label ) );
						oldOffset += match.index + match[0].length;
						newSubstring = text.substring( oldOffset );
						match = newSubstring.match( regex );
					}
				}
			}
		}
		
		return this.tokenArray; // array of tokens
	},
	markers: [],
	mark: function( context, division, tokens ) {
		// We need to markup some text based on some tokens
		var rawText = context.fn.getContents();
		
		//get all markers
		for ( module in $.wikiEditor.modules ) {
			if ( 'evt' in $.wikiEditor.modules[module]  && 'mark' in $.wikiEditor.modules[module].evt ) {
				$.wikiEditor.modules[module].evt.mark();
			}
		}
		markedText = "";
		var previousIndex = 0;
	    for(var currentIndex in this.markers){
	    	markedText+= rawText.substring(previousIndex, currentIndex);
	    	for(var i = 0 ; i < this.markers[currentIndex].length; i++){
	    		 markedText += this.markers[currentIndex][i];
	    	}
	    	previousIndex = currentIndex;
	    }
	    if(markedText != ""){
	    	 markedText.replace(/\n/g, '<br\>');
	    	 context.fn.setContents( { contents:markedText } );
	    }
	}//mark
}

}; })( jQuery );