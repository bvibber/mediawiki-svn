/* JavaScript for WikiEditor Toc module */

mw.addMessages({
	"wikieditor-toc-preference" : "Enable navigable table of contents",
	"wikieditor-toc-show" : "Show contents",
	"wikieditor-toc-hide" : "Hide contents"
});

mw.ready( function() {
	// Check preferences for toolbar
	if ( !wgWikiEditorPreferences || !( wgWikiEditorPreferences.toc && wgWikiEditorPreferences.toc.enable ) ) {
		return true;
	}
	// Add the toc module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule',
			{ 'toc' : { 'rtl' : ( $j( 'body' ).is( '.rtl' ) ) } } );
	}
});

mw.loadDone( 'wikiEditor.config.toc' );