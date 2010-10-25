/**
 * Client side part of the TemplateEditor.
 */
( function( $ ) { $.inlineEditor.editors.templateEditor = {

enable: function() {
	// do what we also do when reloading the page
	$.inlineEditor.editors.templateEditor.reload();
	
	// add the identifying class to #editContent
	$( '#editContent' ).addClass( 'templateEditor' );
},

reload: function() {
	// make templates clickable
	$( '.templateEditorElement' ).click( $.inlineEditor.basicEditor.click );
},

disable: function() {
	// remove the click event from the templates
	$( '.templateEditorElement' ).unbind( 'click' );
	
	// remove the identifying class from #editContent
	$( '#editContent' ).removeClass( 'templateEditor' );
	
	// cancel all open editors
	$.inlineEditor.basicEditor.cancelAll();
}

}; } ) ( jQuery );