/* JavaScript for WikiEditorCode extension */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'code' );
});
