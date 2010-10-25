/**
 * Client side part of the ListEditor.
 */
( function( $ ) { $.inlineEditor.editors.listEditor = {

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.listEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'listEditor' );
},

reload: function() {
	// make lists clickable
	$( '.listEditorElement' ).click( $.inlineEditor.basicEditor.click );
},

disable: function() {
	// remove the click event from the lists
	$( '.listEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'listEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );