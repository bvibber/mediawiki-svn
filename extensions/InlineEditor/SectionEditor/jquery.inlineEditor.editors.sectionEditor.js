/**
 * Client side part of the SectionEditor.
 */
( function( $ ) { $.inlineEditor.editors.sectionEditor = {

click: function( event ) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	var width = $field.width();
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.sectionEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, width, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.sectionEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'sectionEditor' );
},

reload: function() {
	// make sections clickable
	$( '.sectionEditorElement' ).click( $.inlineEditor.editors.sectionEditor.click );
},

disable: function() {
	// remove the click event from the sections
	$( '.sectionEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'sectionEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );