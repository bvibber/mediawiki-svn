/* JavaScript for WikiEditor Toc module */

js2AddOnloadHook( function() {
	// Check preferences for toolbar
	if ( !wgWikiEditorPreferences || !( wgWikiEditorPreferences.toc && wgWikiEditorPreferences.toc.enable ) ) {
		return true;
	}
	// Add the toc module
	if ( $j.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'toc' );
	}
});
