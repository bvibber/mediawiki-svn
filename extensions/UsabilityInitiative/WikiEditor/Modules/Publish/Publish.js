/* JavaScript for WikiEditor Publish module */

js2AddOnloadHook( function() {
	// Check preferences for preview
	if ( !wgWikiEditorPreferences || !( wgWikiEditorPreferences.publish && wgWikiEditorPreferences.publish.enable ) ) {
		return true;
	}
	// Add the preview module
	if ( $j.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'publish' );
	}
});
