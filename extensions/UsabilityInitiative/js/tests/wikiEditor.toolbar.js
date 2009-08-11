/**
 *
 */
var textareaId = '#wpTextbox1';
var wikiEditorTests = {
	// Should remove Á from latin characters
	'remove_character': {
		'call': 'removeFromToolbar',
		'data': {
	        'section': 'characters',
	        'page': 'latin',
	        'character': 'Á'
	    },
	    'test': 'div[rel=characters].section div[rel=latin].page a[rel=Á]'
    },
    'remove_table_row_0': {
		// Should remove the first non-heading row of format help
		'call': 'removeFromToolbar',
	    'data': {
	        'section': 'help',
	        'page': 'heading',
	        'row': 0
	    },
	    'test': 'div[rel=help].section div[rel=format].page table tr td:eq(0):contains("1st level heading")'
    },
    'remove_table_row_1': {
		// Should remove the second non-heading row of link help
		'call': 'removeFromToolbar',
	    'data': {
	        'section': 'help',
	        'page': 'heading',
	        'row': 1
	    },
	    'test': 'div[rel=help].section div[rel=format].page table tr td:eq(0):contains("3rd level heading")'
	}
};
js2AddOnloadHook( function() {
	$j( '<button>Run wikiEditor Tests!</button>' )
		.appendTo( $j( 'body' ) )
		.css( {
			'position': 'fixed',
			'bottom': 0,
			'right': 0,
			'width': '100%',
			'backgroundColor': '#333333',
			'color': '#DDDDDD',
			'padding': '0.5em',
			'border': 'none'
		} )
		.click( function() {
			var messages = [ 'Running tests for wikiEditor API' ];
			var target = $j( textareaId );
			var ui = target.data( 'context' ).$ui;
			var passes = 0;
			var tests = 0;
			for ( test in wikiEditorTests ) {
				target.wikiEditor(
					wikiEditorTests[test].call,
					wikiEditorTests[test].data
				);
				var pass = ui.find( wikiEditorTests[test].test ).size() == 0;
				messages[messages.length] =
					test + ':' + ( pass ? 'PASS' : 'FAIL' );
				if ( pass ) {
					passes++;
				}
				tests++;
			}
			if ( window.console !== undefined ) {
				for ( message in messages ) {
					console.log( messages[message] );
				}
			}
			$j(this)
				.attr( 'title', messages.join( " | " ) )
				.text( tests + ' / ' + passes + ' were successful' )
				.css( 'backgroundColor', passes < tests ? 'red' : 'green' )
				.attr( 'enabled', 'false' )
				.blur();
		} );
} );