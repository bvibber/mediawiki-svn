/**
 * Client side part of the ReferenceEditor. Defines $.inlineEditor.editors.referenceEditor.
 */
( function( $ ) { $.inlineEditor.editors.referenceEditor = {

click: function(event) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.referenceEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, 500, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.referenceEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'referenceEditor' );
},

reload: function() {
	// make references clickable
	$( '.referenceEditorElement' ).click( $.inlineEditor.editors.referenceEditor.click );
},

disable: function() {
	// remove the click event from the references
	$( '.referenceEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'referenceEditor' );
}

}; } ) ( jQuery );