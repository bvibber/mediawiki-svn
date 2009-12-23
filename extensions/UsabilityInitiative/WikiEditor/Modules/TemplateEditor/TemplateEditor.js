/* JavaScript for WikiEditor Template Editor module */

mw.addOnloadHook( function() {
	// Check preferences for templateEditor
	if ( !wgWikiEditorEnabledModules.templateEditor ) {
		return true;
	}
	// Add the templateEditor module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'templateEditor' );
	}
});
