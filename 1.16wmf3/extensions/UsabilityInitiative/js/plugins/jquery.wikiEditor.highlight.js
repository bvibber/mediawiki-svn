/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * Core Requirements
 */
'req': [ 'iframe' ],
'name': 'highlight',
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
		$.wikiEditor.modules.highlight.currentScope = event.data.scope;
		if ( event.data.scope == 'realchange' ) {
			$.wikiEditor.modules.highlight.fn.scan( context, '' );
			$.wikiEditor.modules.highlight.fn.mark( context, 'realchange', '' );
		}
	},
	ready: function( context, event ) {
		// Highlight stuff for the first time
		$.wikiEditor.modules.highlight.currentScope = 'ready'; // FIXME: Ugly global, kill with fire
		$.wikiEditor.modules.highlight.fn.scan( context, '' );
		$.wikiEditor.modules.highlight.fn.mark( context, '', '' );
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
		context.modules.highlight.markersStr = '';
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
	 * FIXME: Isn't this done by context.fn.htmlToText() already?
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
		for ( module in context.modules ) {
			if ( module in $.wikiEditor.modules && 'exp' in $.wikiEditor.modules[module] ) {
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
	// TODO: Document the scan() and mark() APIs somewhere
	mark: function( context, division, tokens ) {
		// Reset markers
		var markers = [];
		
		// Recycle markers that will be skipped in this run
		if ( context.modules.highlight.markers && division != '' ) {
			for ( var i = 0; i < context.modules.highlight.markers.length; i++ ) {
				if ( context.modules.highlight.markers[i].skipDivision == division ) {
					markers.push( context.modules.highlight.markers[i] );
				}
			}
		}
		context.modules.highlight.markers = markers;
		
		// Get all markers
		context.fn.trigger( 'mark' );
		markers.sort( function( a, b ) { return a.start - b.start || a.end - b.end; } );
		
		// Serialize the markers array to a string and compare it with the one stored in the previous run
		// If they're equal, there's no markers to change
		var markersStr = '';
		for ( var i = 0; i < markers.length; i++ ) {
			markersStr += markers[i].start + ',' + markers[i].end + ',' + markers[i].type + ',';
		}
		if ( context.modules.highlight.markersStr == markersStr ) {
			// No change, bail out
			return;
		}
		context.modules.highlight.markersStr = markersStr;
		
		// Traverse the iframe DOM, inserting markers where they're needed.
		// Store visited markers here so we know which markers should be removed
		var visited = [], v = 0;
		for ( var i = 0; i < markers.length; i++ ) {
			if ( typeof markers[i].skipDivision !== 'undefined' && ( division == markers[i].skipDivision ) ) { 
				continue;
			}
			
			// We want to isolate each marker, so we may need to split textNodes
			// if a marker starts or ends halfway one.
			var start = markers[i].start;
			var s = context.fn.getOffset( start );
			if ( !s ) {
				// This shouldn't happen
				continue;
			}
			var startNode = s.node;
			
			// Don't wrap leading BRs, produces undesirable results
			// FIXME: It's also possible that the offset is a bit high because getOffset() has incremented
			// .length to fake the newline caused by startNode being in a P. In this case, prevent
			// the textnode splitting below from making startNode an empty textnode, IE barfs on that
			while ( startNode.nodeName == 'BR' || s.offset == startNode.nodeValue.length ) {
				start++;
				s = context.fn.getOffset( start );
				startNode = s.node;
			}
			
			// The next marker starts somewhere in this textNode or at this BR
			if ( s.offset > 0 && s.node.nodeName == '#text' ) {
				// Split off the prefix
				// This leaves the prefix in the current node and puts
				// the rest in a new node which is our start node
				var newStartNode = startNode.splitText( s.offset < s.node.nodeValue.length ?
					s.offset : s.node.nodeValue.length - 1
				);
				var oldStartNode = startNode;
				startNode = newStartNode;
				
				// Update offset objects. We don't need purgeOffsets(), simply
				// manipulating the existing offset objects will suffice
				// FIXME: This manipulates context.offsets directly, which is ugly,
				// but the performance improvement vs. purgeOffsets() is worth it
				// This code doesn't set lastTextNode to newStartNode for offset objects
				// with lastTextNode == oldStartNode, but that doesn't really matter
				var subtracted = s.offset;
				var oldLength = s.length;

				var j, o;
				// Update offset objects referring to oldStartNode
				for ( j = start - subtracted; j < start; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = oldStartNode;
						o.length = subtracted;
					}
				}
				// Update offset objects referring to newStartNode
				for ( j = start; j < start - subtracted + oldLength; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = newStartNode;
						o.offset -= subtracted;
						o.length -= subtracted;
						o.lastTextNode = oldStartNode;
					}
				}
			}
			
			var end = markers[i].end;
			// To avoid ending up at the first char of the next node, we grab the offset for end - 1
			// and add one to the offset
			var e = context.fn.getOffset( end - 1 );
			if ( !e ) {
				// This shouldn't happen
				continue;
			}
			var endNode = e.node;
			if ( e.offset + 1 < e.length - 1 && endNode.nodeName == '#text' ) {
				// Split off the suffix. This puts the suffix in a new node and leaves the rest in endNode
				var oldEndNode = endNode;
				var newEndNode = endNode.splitText( e.offset + 1 );
				
				// Update offset objects
				var subtracted = e.offset + 1;
				var oldLength = e.length;

				var j, o;
				// Update offset objects referring to oldEndNode
				for ( j = end - subtracted; j < end; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = oldEndNode;
						o.length = subtracted;
					}
				}
				// We have to insert this one, as it might not exist: we didn't call getOffset( end )
				context.offsets[end] = {
					'node': newEndNode,
					'offset': 0,
					'length': oldLength - subtracted,
					'lastTextNode': oldEndNode
				};
				// Update offset objects referring to newEndNode
				for ( j = end + 1; j < end - subtracted + oldLength; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = newEndNode;
						o.offset -= subtracted;
						o.length -= subtracted;
						o.lastTextNode = oldEndNode;
					}
				}
			}
			
			// Don't wrap trailing BRs, doing that causes weird issues
			if ( endNode.nodeName == 'BR' ) {
				endNode = e.lastTextNode;
			}
			
			// If startNode and endNode have different parents, we need to pull endNode and all textnodes in between
			// into startNode's parent and replace </p><p> with <br>
			if ( startNode.parentNode != endNode.parentNode ) {
				var startP = $( startNode ).closest( 'p' ).get( 0 );
				var t = new context.fn.rawTraverser( startNode, startP, context.$content.get( 0 ), false );
				var afterStart = startNode.nextSibling;
				var lastP = startP;
				var nextT = t.next();
				while ( nextT && t.node != endNode ) {
					t = nextT;
					nextT = t.next();
					// If t.node has a different parent, merge t.node.parentNode with startNode.parentNode
					if ( t.node.parentNode != startNode.parentNode ) {
						var oldParent = t.node.parentNode;
						if ( afterStart ) {
							if ( lastP != t.inP ) {
								// We're entering a new <p>, insert a <br>
								startNode.parentNode.insertBefore(
									startNode.ownerDocument.createElement( 'br' ),
									afterStart
								);
							}
							// A <p> with just a <br> in it is an empty line, so let's not bother with unwrapping it
							if ( !( oldParent.childNodes.length == 1 && oldParent.firstChild.nodeName == 'BR' ) ) {
								// Move all children of oldParent into startNode's parent
								while ( oldParent.firstChild ) {
									startNode.parentNode.insertBefore( oldParent.firstChild, afterStart );
								}
							}
						} else {
							if ( lastP != t.inP ) {
								// We're entering a new <p>, insert a <br>
								startNode.parentNode.appendChild(
									startNode.ownerDocument.createElement( 'br' )
								);
							}
							// A <p> with just a <br> in it is an empty line, so let's not bother with unwrapping it
							if ( !( oldParent.childNodes.length == 1 && oldParent.firstChild.nodeName == 'BR' ) ) {
								// Move all children of oldParent into startNode's parent
								while ( oldParent.firstChild ) {
									startNode.parentNode.appendChild( oldParent.firstChild );
								}
							}
						}
						// Remove oldParent, which is now empty
						oldParent.parentNode.removeChild( oldParent );
					}
					lastP = t.inP;
				}
				// Moving nodes around like this invalidates offset objects
				// TODO: Update offset objects ourselves for performance. Requires rewriting this code block to be
				// offset-based rather than traverser-based
			}
			
			// Now wrap everything between startNode and endNode (may be equal).
			var ca1 = startNode, ca2 = endNode;
			if ( ca1 && ca2 && ca1.parentNode ) {
				var anchor = markers[i].getAnchor( ca1, ca2 );
				if ( !anchor ) {
					var commonAncestor = ca1.parentNode;
					if ( markers[i].anchor == 'wrap') {
						// We have to store things like .parentNode and .nextSibling because
						// appendChild() changes these properties
						
						var newNode = ca1.ownerDocument.createElement( 'span' );
						
						var nextNode = ca2.nextSibling;
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
						
						anchor = newNode;
					} else if ( markers[i].anchor == 'tag' ) {
						anchor = commonAncestor;
					}
					$( anchor ).data( 'marker', markers[i] )
						.addClass( 'wikiEditor-highlight' );
					// Allow the module adding this marker to manipulate it
					markers[i].afterWrap( anchor, markers[i] );

				} else {
					// Update the marker object
					$( anchor ).data( 'marker', markers[i] );
					markers[i].onSkip( anchor );
				}
				visited[v++] = anchor;
			}
		}
		
		// Remove markers that were previously inserted but weren't passed to this function
		// This function works because visited[] contains the visited elements in order and find() and each()
		// preserve order
		var j = 0;
		context.$content.find( '.wikiEditor-highlight' ).each( function() {
			if ( visited[j] == this ) {
				// This marker is legit, leave it in
				j++;
				return true;
			}
			
			// Remove this marker
			var marker = $(this).data( 'marker' );
			if ( marker && typeof marker.skipDivision != 'undefined' && ( division == marker.skipDivision ) ) {
				// Don't remove these either
				return true;
			}
			if ( marker && typeof marker.beforeUnwrap == 'function' )
				marker.beforeUnwrap( this );
			if ( ( marker && marker.anchor == 'tag' ) || $(this).is( 'p' ) ) {
				// Remove all classes
				$(this).removeAttr( 'class' );
			} else {
				// Assume anchor == 'wrap'
				$(this).replaceWith( this.childNodes );
			}
			context.fn.purgeOffsets();
		});
		
	}
}

}; })( jQuery );

