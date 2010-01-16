/* JavaScript for WikiEditor Highlight module */

mw.addMessages({
	"wikieditor-highlight-preference" : "Enable syntax highlighting when editing"
});

mw.ready( function() {
	// Check preferences for highlight
	if ( !wgWikiEditorEnabledModules.highlight ) {
		return true;
	}
	// Add the highlight module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'highlight' );
	}
});

mw.loadDone( 'wikiEditor.config.highlight' );