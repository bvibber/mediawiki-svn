/* JavaScript for WikiEditor Highlight module */

js2AddOnloadHook( function() {
	// Check preferences for highlight
	if ( !wgWikiEditorPreferences || !( wgWikiEditorPreferences.highlight && wgWikiEditorPreferences.highlight.enable ) ) {
		return true;
	}
	// Add the highlight module
	if ( $j.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'highlight' );
	}
});
