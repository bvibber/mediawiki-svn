/* JavaScript for WikiEditor Publish module */

mw.addMessages({
	"wikieditor-publish-preference" : "Enable step-by-step publishing",
	"wikieditor-publish-button-publish" : "Publish",
	"wikieditor-publish-button-cancel" : "Cancel",
	"wikieditor-publish-dialog-title" : "Publish to {{SITENAME}}",
	"wikieditor-publish-dialog-summary" : "Edit summary (briefly describe the changes you have made):",
	"wikieditor-publish-dialog-minor" : "Minor edit",
	"wikieditor-publish-dialog-watch" : "Watch this page",
	"wikieditor-publish-dialog-publish" : "Publish",
	"wikieditor-publish-dialog-goback" : "Go back"
});
mw.ready( function() {
	// Check preferences for publish
	if ( !wgWikiEditorEnabledModules.publish ) {
		return true;
	}
	// Add the publish module
	if ( $j.fn.wikiEditor ) {
		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'publish' );
	}
});

mw.loadDone( 'wikiEditor.config.publish' );
