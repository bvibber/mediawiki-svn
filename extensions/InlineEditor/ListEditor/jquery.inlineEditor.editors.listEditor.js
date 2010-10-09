/**
 * Client side part of the ListEditor.
 */
( function( $ ) { $.inlineEditor.editors.listEditor = {

click: function(event) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.listEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, 500, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.listEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'listEditor' );
},

reload: function() {
	// make lists clickable
	$( '.listEditorElement' ).click( $.inlineEditor.editors.listEditor.click );
},

disable: function() {
	// remove the click event from the lists
	$( '.listEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'listEditor' );
}

}; } ) ( jQuery );