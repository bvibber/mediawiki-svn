/* JavaScript for WikiEditor Template Editor module */

mw.addMessages({
	"wikieditor-template-editor-preference": "Enable form-based editing of wiki templates"
});

mw.ready( function() {
	// Check preferences for templateEditor
	if ( !wgWikiEditorEnabledModules.templateEditor ) {
		return true;
	}
	// Add the templateEditor module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'templateEditor' );
	}
});

mw.loadDone( 'wikiEditor.config.templateEditor' );