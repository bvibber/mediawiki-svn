/* JavaScript for WikiEditor Highlight module */

mw.addOnloadHook( function() {
	// Check preferences for highlight
	if ( !wgWikiEditorEnabledModules.highlight ) {
		return true;
	}
	// Add the highlight module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'highlight' );
	}
});
