/**
 * Client side part of the ParagraphEditor.
 */
( function( $ ) { $.inlineEditor.editors.paragraphEditor = {
		
click: function( event ) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	var width = $field.textWidth() - 5;
	if( width < 500 ) width = $field.width();
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.editors.paragraphEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, width, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.paragraphEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'paragraphEditor' );
},

reload: function() {
	// make paragraphs clickable
	$( '.paragraphEditorElement' ).click( $.inlineEditor.editors.paragraphEditor.click );
},

disable: function() {
	// remove the click event from the paragraphs
	$( '.paragraphEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'paragraphEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );