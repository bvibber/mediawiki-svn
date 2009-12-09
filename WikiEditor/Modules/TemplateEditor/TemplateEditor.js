/* JavaScript for WikiEditor Template Editor module */

mw.addOnloadHook( function() {
	// Check preferences for highlight
	if ( !wgWikiEditorPreferences || !( wgWikiEditorPreferences.templateEditor && wgWikiEditorPreferences.templateEditor.enable ) ) {
		return true;
	}
	// Add the templateEditor module
	if ( $j.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'templateEditor' );
	}
});
