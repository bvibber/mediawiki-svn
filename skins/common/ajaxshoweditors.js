var sajax_debug_mode = false;
var wgAjaxShowEditors = {} ;

// The loader. Look at bottom for the sajax hook registration
wgAjaxShowEditors.onLoad = function() {
	var elEditors = document.getElementById( 'ajax-se' );
	// wgAjaxShowEditors.refresh();
	elEditors.onclick = function() { wgAjaxShowEditors.refresh(); } ;
}


// Ask for new data & update UI
wgAjaxShowEditors.refresh = function() {

	// Load the editors list element, it will get rewrote
	var elEditorsList = document.getElementById( 'ajax-se-editors' );

	// Do the ajax call to the server
	sajax_do_call( "wfAjaxShowEditors", [ wgArticleId, wgUserName ], elEditorsList );
}


// Register our initialization function.
hookEvent( "load", wgAjaxShowEditors.onLoad);
