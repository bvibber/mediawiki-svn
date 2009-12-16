/* JavaScript for WikiEditor extension */

js2AddOnloadHook( function() {
	if ( $j.wikiEditor != undefined && $j.wikiEditor.isSupported() || !$j.wikiEditor.isSupportKnown() ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor();
	}
} );
