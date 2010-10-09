/**
 * Client side part of the MediaEditor.
 */
( function( $ ) { $.inlineEditor.editors.mediaEditor = {

click: function(event) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.mediaEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, 700, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.mediaEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'mediaEditor' );
},

reload: function() {
	// make media clickable
	$( '.mediaEditorElement' ).click( $.inlineEditor.editors.mediaEditor.click );
},

disable: function() {
	// remove the click event from the media
	$( '.mediaEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'mediaEditor' );
}

}; } ) ( jQuery );