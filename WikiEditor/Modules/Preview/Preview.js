/* JavaScript for WikiEditor Preview module */

mw.addMessages({
	"wikieditor-preview-preference" : "Enable side-by-side preview",
	"wikieditor-preview-tab" : "Preview",
	"wikieditor-preview-changes-tab" : "Changes",
	"wikieditor-preview-loading" :  "Loading..."
});
mw.ready( function() {
	// Check preferences for preview
	if ( !wgWikiEditorEnabledModules.preview ) {
		return true;
	}
	// Add the preview module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'preview' );
	}
});

mw.loadDone( 'wikiEditor.config.preview' );