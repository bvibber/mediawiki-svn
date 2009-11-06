/* JavaScript for WikiEditor Preview module */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'preview' );
});
