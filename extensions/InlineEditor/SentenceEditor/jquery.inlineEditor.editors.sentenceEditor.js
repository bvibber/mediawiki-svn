/**
 * Client side part of the SentenceEditor. Defines $.inlineEditor.editors.sentenceEditor.
 */
( function( $ ) { $.inlineEditor.editors.sentenceEditor = {

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.sentenceEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'sentenceEditor' );
},

reload: function() {
	// make sentences clickable
	$( '.sentenceEditorElement' ).click( $.inlineEditor.basicEditor.click );
},

disable: function() {
	// remove the click event from the sentences
	$( '.sentenceEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'sentenceEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );