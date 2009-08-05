/* JavaScript for NavigableTOC extension */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'toc' );
});
