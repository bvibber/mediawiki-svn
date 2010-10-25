/**
 * Client side part of the MediaEditor.
 */
( function( $ ) { $.inlineEditor.editors.mediaEditor = {

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.mediaEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'mediaEditor' );
},

reload: function() {
	// make media clickable
	$( '.mediaEditorElement' ).click( $.inlineEditor.basicEditor.click );
},

disable: function() {
	// remove the click event from the media
	$( '.mediaEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'mediaEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );