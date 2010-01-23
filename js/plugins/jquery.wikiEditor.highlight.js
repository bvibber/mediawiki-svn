/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * Configuration
 */
cfg: {
	'styleVersion': 3
},
/**
 * Internally used event handlers
 */
evt: {
	delayedChange: function( context, event ) {
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
		if ( event.data.scope == 'division' ) {
			$.wikiEditor.modules.highlight.fn.scan( context, "" );
			$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
		}
	},
	ready: function( context, event ) {
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
		function Token( offset, label, tokenStart, match ) {
			this.offset = offset;
			this.label = label;
			this.tokenStart = tokenStart;
			this.match = match;
		}
		// Reset tokens
		var tokenArray = context.modules.highlight.tokenArray = [];
		// We need to look over some text and find interesting areas, then return the positions of those areas as tokens
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
						var tokenStart = match.index + oldOffset + markOffset;
						if ( markAfter ) {
							markOffset += match[0].length;
						}
						tokenArray.push( new Token( match.index + oldOffset + markOffset,
							label, tokenStart, match ) );
						oldOffset += match.index + match[0].length;
						newSubstring = text.substring( oldOffset );
						match = newSubstring.match( regex );
					}
				}
			}
		}
		//sort by offset, or if offset same, sort by start
		tokenArray.sort( function( a, b ) {
			return a.offset - b.offset || a.tokenStart - b.tokenStart;
		} );
		context.fn.trigger( 'scan' );
	},
	/**
	 * Marks up text with HTML
	 * 
	 * @param division
	 * @param tokens
	 */
	// FIXME: What do division and tokens do?
	mark: function( context, division, tokens ) {
		// Reset markers
		var markers = context.modules.highlight.markers = [];
		// Get all markers
		context.fn.trigger( 'mark' );
		markers.sort( function( a, b ) { return a.start - b.start || a.end - b.end; } );
		
		// Traverse the iframe DOM, inserting markers where they're needed.
		var pos = 0;
		var t = context.fn.traverser( context.$content );
		var i = 0; // index for markers[]
		var startNode = null;
		var nextDepth = 0, startDepth = null;
		var lastTextNode = null, lastTextNodeDepth = null;
		while ( i < markers.length && t.node ) {
			if ( t.node.nodeName != '#text' && t.node.nodeName != 'BR' ) {
				// Skip this node
				t.goNext();
				continue;
			}
			var newPos = t.node.nodeName == '#text' ? pos + t.node.nodeValue.length : pos + 1;
			if ( t.node.nodeName == '#text' ) {
				lastTextNode = t.node;
				lastTextNodeDepth = t.depth;
			}
			// We want to isolate each marker, so we may need to split textNodes
			// if a marker starts or end halfway one.
			if ( !startNode && markers[i].start >= pos && markers[i].start < newPos ) {
				// The next marker starts somewhere in this textNode or at this BR
				if ( markers[i].start > pos ) {
					// t.node must be a textnode at this point because
					// start > pos and start < pos+1 can't both be true
					
					// Split off the prefix
					// This leaves the prefix in the current node and puts
					// the rest in a new node, which we immediately advance to
					t.node = t.node.splitText( markers[i].start - pos );
					pos = markers[i].start;
				}
				startNode = t.node;
				startDepth = t.depth;
			}
			// Don't wrap BRs, produces undesirable results
			if ( startNode && startNode.nodeName == 'BR' ) {
				startNode = t.node;
				startDepth = t.depth;
			}
			// TODO: What happens when wrapping a zero-length string?
			if ( startNode && markers[i].end > pos && markers[i].end <= newPos ) {
				// The marker ends somewhere in this textNode or at this BR
				if ( markers[i].end < newPos ) {
					// t.node must be a textnode at this point because
					// end > pos and end < pos+1 can't both be true
					
					// Split off the suffix - This puts the suffix in a new node and leaves the rest in the current
					// node.
					// t.node.nodeValue.length - ( newPos - markers[i].end )
					t.node.splitText( t.node.nodeValue.length - newPos + markers[i].end );
					newPos = markers[i].end;
				}
				
				// Don't wrap leading or trailing BRs, doing that causes weird issues
				var endNode = t.node, endDepth = t.depth;
				if ( endNode.nodeName == 'BR' ) {
					endNode = lastTextNode;
					endDepth = lastTextNodeDepth;
				}
				
				// Now wrap everything between startNode and endNode (may be equal). First find the common ancestor of
				// startNode and endNode. ca1 and ca2 will be children of this common ancestor, such that ca1 is an
				// ancestor of startNode and ca2 of endNode. We also check that startNode and endNode are the leftmost and
				// rightmost leaves in the subtrees rooted at ca1 and ca2 respectively; if this is not the case, we
				// can't cleanly wrap things without misnesting and we silently fail.
				var ca1 = startNode, ca2 = endNode;
				// Correct for startNode and endNode possibly not having the same depth
				if ( startDepth > endDepth ) {
					for ( var j = 0; j < startDepth - endDepth && ca1; j++ ) {
						ca1 = ca1.parentNode.firstChild == ca1 ? ca1.parentNode : null;
					}
				}
				else if ( startDepth < endDepth ) {
					for ( var j = 0; j < endDepth - startDepth && ca2; j++ ) {
						ca2 = ca2.parentNode.lastChild == ca2 ? ca2.parentNode : null;
					}
				}
				// Now that ca1 and ca2 have the same depth, have them walk up the tree simultaneously
				// to find the common ancestor
				while ( ca1 && ca2 && ca1.parentNode != ca2.parentNode ) {
					ca1 = ca1.parentNode.firstChild == ca1 ? ca1.parentNode : null;
					ca2 = ca2.parentNode.lastChild == ca2 ? ca2.parentNode : null;
				}
				if ( ca1 && ca2 ) {
					var anchor = markers[i].getAnchor( ca1, ca2 );
					if ( !anchor ) {
						// We have to store things like .parentNode and .nextSibling because appendChild() changes these
						// properties
						var newNode = ca1.ownerDocument.createElement( 'div' );
						var commonAncestor = ca1.parentNode;
						var nextNode = ca2.nextSibling;
						if ( markers[i].anchor == 'wrap' ) {
							// Append all nodes between ca1 and ca2 (inclusive) to newNode
							var n = ca1;
							while ( n != nextNode ) {
								var ns = n.nextSibling;
								newNode.appendChild( n );
								n = ns;
							}
							
							// Insert newNode in the right place
							if ( nextNode ) {
								commonAncestor.insertBefore( newNode, nextNode );
							} else {
								commonAncestor.appendChild( newNode );
							}
						} else if ( markers[i].anchor == 'before' ) {
							commonAncestor.insertBefore( newNode, ca1 );
						} else if ( markers[i].anchor == 'after' ) {
							if ( nextNode ) {
								commonAncestor.insertBefore( newNode, nextNode );
							} else {
								commonAncestor.appendChild( newNode );
							}
						}
						
						$( newNode ).data( 'marker', markers[i] )
							.addClass( 'wikiEditor-highlight wikiEditor-highlight-tmp' );
						
						// Allow the module adding this marker to manipulate it
						markers[i].afterWrap( newNode, markers[i] );
					} else {
						// Temporarily add a class for bookkeeping purposes
						$( anchor )
							.addClass( 'wikiEditor-highlight-tmp' )
							.data( 'marker', markers[i] );
						markers[i].onSkip( anchor );
					}
				}
				// Clear for next iteration
				startNode = null;
				startDepth = null;
				i++;
			}
			pos = newPos;
			t.goNext();
		}
		
		// Remove markers that were previously inserted but weren't passed to this function
		context.$content.find( 'div.wikiEditor-highlight:not(.wikiEditor-highlight-tmp)' ).each( function() {
			if ( typeof $(this).data( 'marker' ).unwrap == 'function' )
				$(this).data( 'marker' ).unwrap( this );
			if ( $(this).children().size() > 0 ) {
				$(this).replaceWith( $(this).children() );
			} else {
				$(this).replaceWith( $(this).html() );
			}
		});
		// Remove temporary class
		context.$content.find( 'div.wikiEditor-highlight-tmp' ).removeClass( 'wikiEditor-highlight-tmp' );
	}
}

}; })( jQuery );

