/* JavaScript for WikiEditor Preview module */

mw.addOnloadHook( function() {
	// Check preferences for preview
	if ( !wgWikiEditorEnabledModules.preview ) {
		return true;
	}
	// Add the preview module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'preview' );
	}
});
