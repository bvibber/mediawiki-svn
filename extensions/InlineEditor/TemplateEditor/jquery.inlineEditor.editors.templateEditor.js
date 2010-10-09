/**
 * Client side part of the TemplateEditor.
 */
( function( $ ) { $.inlineEditor.editors.templateEditor = {

click: function(event) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.templateEditor.click );
	$newField.removeClass( 'templateEditorElementNotEditing' );
	$.inlineEditor.basicEditor.addEditBar( $newField, 700, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.templateEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'templateEditor' );
},

reload: function() {
	// make templates clickable
	$( '.templateEditorElement' ).click( $.inlineEditor.editors.templateEditor.click );
},

disable: function() {
	// remove the click event from the templates
	$( '.templateEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'templateEditor' );
}

}; } ) ( jQuery );