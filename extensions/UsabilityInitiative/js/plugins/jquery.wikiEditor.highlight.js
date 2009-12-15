/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

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
		if ( event.data.scope == 'keydown' ) {
			$.wikiEditor.modules.highlight.fn.scan( context, "" );
			$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
		}
	},
	ready: function( context, event ) {
		// Add our CSS to the iframe
		context.$content.parent().find( 'head' ).append( $j( '<link />' ).attr( {
			'rel': 'stylesheet',
			'type': 'text/css',
			'href': wgScriptPath + '/extensions/UsabilityInitiative/css/wikiEditor.highlight.css',
		} ) );
		
		// Highlight stuff for the first time
		$.wikiEditor.modules.highlight.fn.scan( context, "" );
		$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a highlight module within a wikiEditor
	 * 
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// hook $.wikiEditor.modules.highlight.evt.change to context.evt.change
	},
	/**
	 * Divides text into divisions
	 */
	divide: function( context ) {
		/*
		 * We need to add some markup to the iframe content to encapsulate divisions
		 */
	},
	/**
	 * Isolates division which was affected by most recent change
	 */
	isolate: function( context ) {
		/*
		 * A change just occured, and we need to know which sections were affected
		 */
		return []; // array of sections?
	},
	/**
	 * Strips division of HTML
	 * 
	 * @param division
	 */
	strip: function( context, division ) {
		return $( '<div />' ).html( division.html().replace( /\<br[^\>]*\>/g, "\n" ) ).text();
	},
	/**
	 * Scans text division for tokens
	 * 
	 * @param division
	 */
	scan: function( context, division ) {
		/**
		 * Builds a Token object
		 * 
		 * @param offset
		 * @param label
		 */
		function Token( offset, label ) {
			this.offset = offset;
			this.label = label;
		}
		// We need to look over some text and find interesting areas, then return the positions of those areas as tokens
		context.modules.highlight.tokenArray = [];
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
						context.modules.highlight.tokenArray.push(
							new Token( match.index + oldOffset + markOffset, label )
						);
						oldOffset += match.index + match[0].length;
						newSubstring = text.substring( oldOffset );
						match = newSubstring.match( regex );
					}
				}
			}
		}
		
		context.modules.highlight.tokenArray.sort( function( a, b ) { return a.offset - b.offset; } );
		return context.modules.highlight.tokenArray; // array of tokens
	},
	/**
	 * Marks up text with HTML
	 * 
	 * @param division
	 * @param tokens
	 */
	// FIXME: What do division and tokens do?
	mark: function( context, division, tokens ) {
		// Get all markers
		context.modules.highlight.markers = [];
		for ( module in $.wikiEditor.modules ) {
			if ( 'evt' in $.wikiEditor.modules[module]  && 'mark' in $.wikiEditor.modules[module].evt ) {
				$.wikiEditor.modules[module].evt.mark( context ); // FIXME: event?
			}
		}
		var markers = context.modules.highlight.markers;
		markers.sort( function( a, b ) { return a.start - b.start || a.end - b.end; } );
		
		// Traverse the iframe DOM, inserting markers where they're needed
		// The loop traverses all leaf nodes in the DOM, and uses DOM methods
		// rather than jQuery because it has to work with text nodes and for performance
		var pos = 0;
		var node = context.$content.get( 0 );
		var next = null;
		var i = 0; // index for markers[]
		var startNode = null;
		var depth = 0, nextDepth = 0, startDepth = null;
		
		// Find the leftmost leaf node in the tree
		while ( node.firstChild ) {
			node = node.firstChild;
			depth++;
		}
		while ( i < markers.length && node ) {
			// Find the next leaf node
			var p = node;
			nextDepth = depth;
			while ( p && !p.nextSibling ) {
				p = p.parentNode;
				nextDepth--;
			}
			p = p ? p.nextSibling : null;
			while ( p && p.firstChild ) {
				p = p.firstChild;
				nextDepth++;
			}
			next = p;
			
			if ( node.nodeName != '#text' ) {
				if ( node.nodeName == 'BR' )
					pos++;
				// Skip this node
				node = next;
				depth = nextDepth;
				continue;
			}
			var newPos = pos + node.nodeValue.length;
			
			// We want to isolate each marker, so we may need to split textNodes
			// if a marker starts or end halfway one.
			if ( !startNode && markers[i].start >= pos && markers[i].start < newPos ) {
				// The next marker starts somewhere in this textNode
				if ( markers[i].start > pos ) {
					// Split off the prefix
					// This leaves the prefix in the current node and puts
					// the rest in a new node, which we immediately advance to
					node = node.splitText( markers[i].start - pos );
					pos = markers[i].start;
				}
				startNode = node;
				startDepth = depth;
			}
			
			// TODO: What happens when wrapping a zero-length string?
			// TODO: Detect that something's already been wrapped and leave it alone
			if ( startNode && markers[i].end > pos && markers[i].end <= newPos ) {
				// The marker ends somewhere in this textNode
				if ( markers[i].end < newPos ) {
					// Split off the suffix
					// This puts the suffix in a new node and leaves the rest
					// in the current node. We have to make sure the split-off
					// node will be visited correctly
					
					// node.nodeValue.length - ( newPos - markers[i].end )
					next = node.splitText( node.nodeValue.length - newPos + markers[i].end );
					newPos = markers[i].end;
				}
				
				// Now wrap everything between startNode and node (may be equal).
				// First find the common ancestor of startNode and node.
				// ca1 and ca2 will be children of this common ancestor, such that
				// ca1 is an ancestor of startNode and ca2 of node.
				// We also check that startNode and node are the leftmost and rightmost
				// leaves in the subtrees rooted at ca1 and ca2 respectively; if this is
				// not the case, we can't cleanly wrap things without misnesting and we
				// silently fail.
				var ca1 = startNode, ca2 = node;
				// Correct for startNode and node possibly not having the same depth
				if ( startDepth > depth ) {
					for ( var j = 0; j < startDepth - depth && ca1; j++ ) {
						ca1 = ca1.parentNode;
					}
				}
				else if ( startDepth < depth ) {
					for ( var j = 0; j < depth - startDepth && ca2; j++ ) {
						ca2 = ca2.parentNode;
					}
				}
				while ( ca1 && ca2 && ca1.parentNode != ca2.parentNode ) {
					if ( ca1.parentNode.firstChild != ca1 || ca2.parentNode.lastChild != ca2 ) {
						// startNode and node are not the leftmost and rightmost leaves
						ca1 = ca2 = null;
					} else {
						ca1 = ca1.parentNode;
						ca2 = ca2.parentNode;
					}
				}
				
				if ( ca1 && ca2 ) {
					// We have to store things like .parentNode and .nextSibling
					// because appendChild() changes these properties
					var newNode = markers[i].wrapElement;
					if ( typeof newNode == 'function' )
						newNode = newNode();
					if ( newNode.jquery )
						newNode = newNode.get( 0 );
					var commonAncestor = ca1.parentNode;
					var nextNode = ca2.nextSibling;
					
					// Append all nodes between ca1 and ca2 (inclusive)
					// to newNode
					var n = ca1;
					while ( n != nextNode ) {
						var ns = n.nextSibling;
						newNode.appendChild( n );
						n = ns;
					}
					
					// Insert newNode in the right place
					if ( nextNode )
						commonAncestor.insertBefore( newNode, nextNode );
					else
						commonAncestor.appendChild( newNode );
				}
				startNode = null; // Clear for next iteration
				startDepth = null;
				i++;
			}
			
			pos = newPos;
			node = next;
			depth = nextDepth;
		}
	}
}

}; })( jQuery );

