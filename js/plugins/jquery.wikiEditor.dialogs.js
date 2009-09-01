/**
 * Extend the RegExp object with an escaping function
 * From http://simonwillison.net/2006/Jan/20/escape/
 */
RegExp.escape = function( s ) { return s.replace(/([.*+?^${}()|\/\\[\]])/g, '\\$1'); };

/**
 * Dialog Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.dialogs = {

/**
 * API accessible functions
 */
api: {
	addDialog: function( context, data ) {
		$.wikiEditor.modules.dialogs.fn.create( context, { 'modules': data } )
	},
	openDialog: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'open' );
		}
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a dialog module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		// Accept additional modules
		if ( 'modules' in config ) {
			for ( module in config.modules ) {
				$.wikiEditor.modules.dialogs.modules[module] = config.modules[module];
			}
		}
		// Build out modules immediately
		for ( module in $.wikiEditor.modules.dialogs.modules ) {
			var module = $.wikiEditor.modules.dialogs.modules[module];
			// Only create the dialog if it doesn't exist yet
			if ( $( '#' + module.id ).size() == 0 ) {
				var configuration = module.dialog;
				// Add some stuff to configuration
				configuration.bgiframe = true;
				configuration.autoOpen = false;
				configuration.modal = true;
				configuration.title = $.wikiEditor.autoMsg( module, 'title' );
				// Transform messages in keys
				// Stupid JS won't let us do stuff like
				// foo = { gM ('bar'): baz }
				for ( msg in configuration.buttons ) {
					configuration.buttons[gM( msg )] = configuration.buttons[msg];
					delete configuration.buttons[msg];
				}
				// Create the dialog <div>
				$( '<div /> ' )
					.attr( 'id', module.id )
					.html( module.html )
					.data( 'context', context )
					.appendTo( $( 'body' ) )
					.each( module.init )
					.dialog( configuration );
			}
		}
	}
},
/**
 * Stock modules
 */
modules: {
	'insert-link': {
		titleMsg: 'edittoolbar-tool-link-title',
		id: 'edittoolbar-link-dialog',
		html: '\
			<div id="edittoolbar-link-tabs">\
				<ul>\
					<li><a href="#edittoolbar-link-dialog-tab-int" rel="edittoolbar-tool-link-int"></a></li>\
					<li><a href="#edittoolbar-link-dialog-tab-ext" rel="edittoolbar-tool-link-ext"></a></li>\
				</ul>\
				<div id="edittoolbar-link-dialog-tab-int"><form><fieldset><table><tr>\
					<td><label for="edittoolbar-link-int-target" rel="edittoolbar-tool-link-int-target"></label></td>\
					<td>\
						<input type="text" id="edittoolbar-link-int-target" />\
						<div id="edittoolbar-link-int-target-status" style="display: inline;"></div>\
					</td>\
				</tr><tr>\
					<td><label for="edittoolbar-link-int-text" rel="edittoolbar-tool-link-int-text"></label></td>\
					<td><input type="text" id="edittoolbar-link-int-text" /></td>\
				</table></fieldset></form></div>\
				<div id="edittoolbar-link-dialog-tab-ext"><form><fieldset><table><tr>\
					<td><label for="edittoolbar-link-ext-target" rel="edittoolbar-tool-link-ext-target"></label></td>\
					<td><input type="text" id="edittoolbar-link-ext-target" /></td>\
				</tr><tr>\
					<td><label for="edittoolbar-link-ext-text" rel="edittoolbar-tool-link-ext-text"></label></td>\
					<td><input type="text" id="edittoolbar-link-ext-text" /></td>\
				</table></fieldset></form></div>\
			</div>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
			$( '#edittoolbar-link-tabs' ).tabs();
			
			// Link int-target and int-text fields
			// This means mirroring the contents of int-target in int-text
			// as long as int-text itself hasn't been changed by the user
			$( '#edittoolbar-link-int-target' ).bind( 'keypress paste', function() {
				// $(this).val() is the old value, before the keypress
				if ( $( '#edittoolbar-link-int-text' ).data( 'untouched' ) )
					// Defer this until $(this).val() has been updated
					setTimeout( function() {
						$( '#edittoolbar-link-int-text' ).val( $( '#edittoolbar-link-int-target' ).val() );
					}, 0 );
			});
			$( '#edittoolbar-link-int-text' ).bind( 'keypress paste', function() {
				$(this).data( 'untouched', false );
			});
			$( '#edittoolbar-link-ext-target' ).val( 'http://' );
			
			// Page existence check widget
			var existsImg = $.wikiEditor.imgPath + 'dialogs/' + 'insert-link-exists.png';
			var notexistsImg = $.wikiEditor.imgPath + 'dialogs/' + 'insert-link-notexists.png';
			var invalidImg = $.wikiEditor.imgPath + 'dialogs/' + 'insert-link-invalid.png';
			var loadingImg = $.wikiEditor.imgPath + 'loading.gif';
			var existsMsg = gM( 'edittoolbar-tool-link-int-target-status-exists' );
			var notexistsMsg = gM( 'edittoolbar-tool-link-int-target-status-notexists' );
			var invalidMsg = gM( 'edittoolbar-tool-link-int-target-status-invalid' );
			var loadingMsg = gM( 'edittoolbar-tool-link-int-target-status-loading' );
			$( '#edittoolbar-link-int-target-status' )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-exists',
					'src': existsImg,
					'alt': existsMsg,
					'title': existsMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-notexists',
					'src': notexistsImg,
					'alt': notexistsMsg,
					'title': notexistsMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-invalid',
					'src': invalidImg,
					'alt': invalidMsg,
					'title': invalidMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-loading',
					'src': loadingImg,
					'alt': loadingMsg,
					'title': loadingMsg } ) )
				.data( 'cache', {} )
				.children().hide();
			
			function updateExistence( target ) {
				function updateWidget( status ) {
					$( '#edittoolbar-link-int-target-status' ).children().hide();
					$( '#edittoolbar-link-int-target-status-' + status ).show();
				}
				
				// Abort previous request
				var request = $( '#edittoolbar-link-int-target-status' ).data( 'request' );
				if ( request )
					request.abort();
				
				var target = $( '#edittoolbar-link-int-target' ).val();
				var cache = $( '#edittoolbar-link-int-target-status' ).data( 'cache' );
				if ( cache[target] ) {
					updateWidget( cache[target] );
					return;
				}
				
				if ( target == '' ) {
					// Hide the widget when the textbox is empty
					$( '#edittoolbar-link-int-target-status' ).children().hide();
					return;
				}
				if ( target.indexOf( '|' ) != -1 ) {
					// Title contains | , which means it's invalid
					// but confuses the API. Show invalid and bypass API
					updateWidget( 'invalid' );
					return;
				}
				
				updateWidget( 'loading' );
				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					dataType: 'json',
					data: {
						'action': 'query',
						'indexpageids': '',
						'titles': target,
						'format': 'json'
					},
					success: function( data ) {
						// TODO: What happens if data.query.pageids is undefined?
						var page = data.query.pages[data.query.pageids[0]];
						var status = 'exists';
						if ( typeof page.missing != 'undefined' )
							status = 'notexists';
						else if ( typeof page.invalid != 'undefined' )
							status = 'invalid';
						
						cache[target] = status;
						updateWidget( status );
					}
				});
				// Save request object so it can be aborted if necessary
				$( '#edittoolbar-link-int-target-status' ).data( 'request', request );	
			}
			
			$( '#edittoolbar-link-int-target' ).bind( 'keypress paste', function() {
				// Cancel the running timer if applicable
				if ( typeof $(this).data( 'timerID' ) != 'undefined' )
					clearTimeout( $(this).data( 'timerID' ) );
				
				// Delay fetch for a while
				// FIXME: Make 250 configurable elsewhere
				var timerID = setTimeout( updateExistence, 250 );
				$(this).data( 'timerID', timerID );
			}).change( function() {
				// Cancel the running timer if applicable
				if ( typeof $(this).data( 'timerID' ) != 'undefined' )
					clearTimeout( $(this).data( 'timerID' ) );
				
				// Fetch right now
				updateExistence();
			});
		},
		dialog: {
			width: 550, // FIXME: autoresize width
			buttons: {
				'edittoolbar-tool-link-insert': function() {
					function escapeInternalText( s ) {
						return s.replace( /(]{2,})/g, '<nowiki>$1</nowiki>' );
					}
					function escapeExternalTarget( s ) {
						return s.replace( / /g, '%20' )
							.replace( /]/g, '%5D' );
					}
					function escapeExternalText( s ) {
						return s.replace( /(]+)/g, '<nowiki>$1</nowiki>' );
					}
					var insertText = '';
					var whitespace = [ '', '' ];
					switch ( $( '#edittoolbar-link-tabs' ).tabs( 'option', 'selected' ) ) {
						case 0: // Internal link
							// FIXME: Exactly how fragile is this?
							if ( $( '#edittoolbar-link-int-target-status-invalid' ).is( ':visible' ) ) {
								// Refuse to add links to invalid titles
								alert( gM( 'edittoolbar-tool-link-int-invalid' ) );
								return;
							}
							var target = $( '#edittoolbar-link-int-target' ).val();
							var text = $( '#edittoolbar-link-int-text' ).val();
							whitespace = $( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace' );
							if ( target == text )
								insertText = '[[' + target + ']]';
							else
								insertText = '[[' + target + '|' + escapeInternalText( text ) + ']]';
						break;
						case 1:
							var target = $( '#edittoolbar-link-ext-target' ).val();
							var text = $( '#edittoolbar-link-ext-text' ).val();
							var escTarget = escapeExternalTarget( target );
							var escText = escapeExternalText( text );
							whitespace = $( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace' );
							if ( escTarget == escText )
								insertText = escTarget;
							else if ( text == '' )
								insertText = '[' + escTarget + ']';
							else
								insertText = '[' + escTarget + ' ' + escText + ']';
						break;
					}
					// Preserve whitespace in selection when replacing
					insertText = whitespace[0] + insertText + whitespace[1];
					$.wikiEditor.modules.toolbar.fn.doAction( $(this).data( 'context' ), {
						type: 'replace',
						options: {
							pre: insertText
						}
					}, $(this) );
					$(this).dialog( 'close' );
				},
				'edittoolbar-tool-link-cancel': function() {
					$(this).dialog( 'close' );
				}
			},
			open: function() {
				// Smart pre-fill text fields
				var selection = $(this).data( 'context' ).$textarea.getSelection();
					$( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace', [ '', '' ] );
					$( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace', [ '', '' ] );
				if ( selection != '' ) {
					var inttext, inttarget, exttext, exttarget;
					var matches;
					var tab = -1;
					if ( ( matches = selection.match( /^(\s*)\[\[([^\]\|]+)(\|([^\]\|]*))?\]\](\s*)$/ ) ) ) {
						// [[foo|bar]] or [[foo]]
						inttarget = matches[2];
						inttext = ( matches[4] ? matches[4] : matches[2] );
						tab = 0;
						// Preserve whitespace when replacing
						$( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace', [ matches[1], matches[5] ] );
					} else if ( ( matches = selection.match( /^(\s*)\[([^\] ]+)( ([^\]]+))?\](\s*)$/ ) ) ) {
						// [http://www.example.com foo] or [http://www.example.com]
						exttarget = matches[2];
						exttext = ( matches[4] ? matches[4] : '' );
						tab = 1;
						// Preserve whitespace when replacing
						$( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace', [ matches[1], matches[5] ] );
					} else {
						inttarget = inttext = exttext = selection;
						exttarget = 'http://';
					}
					
					// val() doesn't trigger the change event, so let's do that ourselves
					if ( typeof inttext != 'undefined' )
						$( '#edittoolbar-link-int-text' ).val( inttext ).change();
					if ( typeof inttarget != 'undefined' )
						$( '#edittoolbar-link-int-target' ).val( inttarget ).change();
					if ( typeof exttext != 'undefined' )
						$( '#edittoolbar-link-ext-text' ).val( exttext ).change();
					if ( typeof exttarget != 'undefined' )
						$( '#edittoolbar-link-ext-target' ).val( exttarget ).change();
					if ( tab != -1 )
						$( '#edittoolbar-link-tabs' ).tabs( 'select', tab );
				}
				$( '#edittoolbar-link-int-text' ).data( 'untouched',
					$( '#edittoolbar-link-int-text' ).val() == $( '#edittoolbar-link-int-target' ).val()
				);
			}
		}
	},
	'insert-table': {
		titleMsg: 'edittoolbar-tool-table-title',
		id: 'edittoolbar-table-dialog',
		html: '\
			<form><fieldset><legend rel="edittoolbar-tool-table-dimensions"></legend><table><tr>\
				<td class="label"><label for="edittoolbar-table-dimensions-columns"\
					rel="edittoolbar-tool-table-dimensions-columns"></label></td>\
				<td><input type="text" id="edittoolbar-table-dimensions-columns" size="2" /></td>\
				<td class="label"><label for="edittoolbar-table-dimensions-rows"\
					rel="edittoolbar-tool-table-dimensions-rows"></label></td>\
				<td><input type="text" id="edittoolbar-table-dimensions-rows" size="2" /></td>\
			</tr><tr>\
				<td class="label"><label for="edittoolbar-table-dimensions-headercolumns"\
					rel="edittoolbar-tool-table-dimensions-headercolumns"></label></td>\
				<td><input type="text" id="edittoolbar-table-dimensions-headercolumns" size="2" /></td>\
				<td class="label"><label for="edittoolbar-table-dimensions-headerrows"\
					rel="edittoolbar-tool-table-dimensions-headerrows"></label></td>\
				<td><input type="text" id="edittoolbar-table-dimensions-headerrows" size="2" /></td>\
			</tr></table></fieldset></form>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
		},
		dialog: {
			width: 350, // FIXME: autoresize
			buttons: {
				'edittoolbar-tool-table-insert': function() {
					var rows = parseInt( $( '#edittoolbar-table-dimensions-rows' ).val() );
					var cols = parseInt( $( '#edittoolbar-table-dimensions-columns' ).val() );
					var hrows = parseInt( $( '#edittoolbar-table-dimensions-headerrows' ).val() );
					var hcols = parseInt( $( '#edittoolbar-table-dimensions-headercolumns' ).val() );
					
					var table = "{|\n";
					for ( var r = 0; r < rows; r++ ) {
						table += "|-\n";
						for ( var c = 0; c < cols; c++ ) {
							var isHeader = ( r < hrows || c < hcols );
							var delim = isHeader ? '!' : '|';
							if ( c > 0 )
								delim += delim;
							table += delim + ' ' +
								gM( isHeader ?
									'edittoolbar-tool-table-example-header' :
									'edittoolbar-tool-table-example',
									[ r + 1, c + 1 ] ) + ' ';
						}
						// Replace trailing space by newline
						// table[table.length - 1] is read-only
						table = table.substr( 0, table.length - 1 ) + "\n";
					}
					table += "|}";
					$.wikiEditor.modules.toolbar.fn.doAction(
						$(this).data( 'context' ), {
							type: 'encapsulate',
							options: {
								pre: table,
								ownline: true
							}
						}, $(this) );
					$(this).dialog( 'close' );
				},
				'edittoolbar-tool-table-cancel': function() {
					$(this).dialog( 'close' );
				}
			}
		}
	},
	'search-and-replace': {
		titleMsg: 'edittoolbar-tool-replace-title',
		id: 'edittoolbar-replace-dialog',
		html: '\
			<form><fieldset><table><tr>\
				<td><label for="edittoolbar-replace-search" rel="edittoolbar-tool-replace-search"></label></td>\
				<td><input type="text" id="edittoolbar-replace-search" /></td>\
			</tr><tr>\
				<td><label for="edittoolbar-replace-replace" rel="edittoolbar-tool-replace-replace"></label></td>\
				<td><input type="text" id="edittoolbar-replace-replace" /></td>\
			</tr></table><table><tr>\
				<td><input type="checkbox" id="edittoolbar-replace-case" /></td>\
				<td><label for="edittoolbar-replace-case" rel="edittoolbar-tool-replace-case"></label></td>\
			</tr><tr>\
				<td><input type="checkbox" id="edittoolbar-replace-regex" /></td>\
				<td><label for="edittoolbar-replace-regex" rel="edittoolbar-tool-replace-regex"></label></td>\
			</tr><tr>\
				<td><input type="checkbox" id="edittoolbar-replace-all" /></td>\
				<td><label for="edittoolbar-replace-all" rel="edittoolbar-tool-replace-all"></label></td>\
			</tr></table></fieldset></form>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
		},
		dialog: {
			width: 350, // FIXME: autoresize width
			buttons: {
				'edittoolbar-tool-replace-button': function() {
					var searchStr = $( '#edittoolbar-replace-search' ).val();
					var replaceStr = $( '#edittoolbar-replace-replace' ).val();
					var flags = '';
					if ( !$( '#edittoolbar-replace-case' ).is( ':checked' ) )
						flags += 'i';
					if ( $( '#edittoolbar-replace-all' ).is( ':checked' ) )
						flags += 'g';
					if ( !$( '#edittoolbar-replace-regex' ).is( ':checked' ) )
						searchStr = RegExp.escape( searchStr );
					var regex = new RegExp( searchStr, flags );
					var $textarea = $(this).data( 'context' ).$textarea;
					if ( !$textarea.val().match( regex ) )
						alert( gM( 'edittoolbar-tool-replace-nomatch' ) );
					else
						$textarea.val( $textarea.val().replace( regex, replaceStr ) );
					// TODO: Hook for wikEd
				},
				'edittoolbar-tool-replace-close': function() {
					$(this).dialog( 'close' );
				}
			}
		}
	}
}

}; } ) ( jQuery );