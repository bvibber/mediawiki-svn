/* JavaScript for WikiEditor Toc module */

js2AddOnloadHook( function() {
	$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'toc' );
});
