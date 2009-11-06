/* JavaScript for SideBySidePreview extension */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'preview' );
});
