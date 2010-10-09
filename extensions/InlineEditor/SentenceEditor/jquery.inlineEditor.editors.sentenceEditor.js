/**
 * Client side part of the SentenceEditor. Defines $.inlineEditor.editors.sentenceEditor,
 * and uses $.elastic and $.textWidth.
 */
( function( $ ) { $.inlineEditor.editors.sentenceEditor = {

click: function(event) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the <span> element and retrieve the corresponding wikitext
	var $span = $(this);
	var wiki = $.inlineEditor.getTextById( $span.attr( 'id' ) );
	
	// calculate width based on the text width witin the page flow
	// this means that this width will never exeed the original width when it's a multiline sentence
	var width = $span.textWidth() - 5;
	if( width < 300 ) width = 300;
	
	$newField = $.inlineEditor.basicEditor.newField( $span, $.inlineEditor.editors.sentenceEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, width, wiki );
},

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.sentenceEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'sentenceEditor' );
},

reload: function() {
	// make sentences clickable
	$( '.sentenceEditorElement' ).click( $.inlineEditor.editors.sentenceEditor.click );
},

disable: function() {
	// remove the click event from the sentences
	$( '.sentenceEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'sentenceEditor' );
}

}; } ) ( jQuery );