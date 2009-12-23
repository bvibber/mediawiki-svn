/* JavaScript for WikiEditor Publish module */

mw.addOnloadHook( function() {
	// Check preferences for publish
	if ( !wgWikiEditorEnabledModules.preview ) {
		return true;
	}
	// Add the publish module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'publish' );
	}
});
