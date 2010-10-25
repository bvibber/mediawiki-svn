/**
 * Provides a basic editor with preview and cancel functionality.
 */
( function( $ ) { $.inlineEditor.basicEditor = {

/**
 * Creates a new field which stores the original field inside.
 * The original click event is required to bind with the original field.
 */
newField: function( $field, originalClickEvent ) {
	// store the original field in a hidden field
	var $orig = $( '<' + $field.get(0).nodeName + '/>' );
	$orig.html( $field.html() );
	$orig.attr( 'id', $field.attr( 'id' ) );
	$orig.addClass( $field.attr( 'class' ) );
	$orig.addClass( 'orig' );
	$orig.click( originalClickEvent );
	
	// create a new field and add the original text
	var $newField = $( '<' + $field.get(0).nodeName + '/>' );
	$newField.addClass( $field.attr('class' ) );
	$newField.removeClass( 'notEditing' );
	$newField.addClass( 'editing' );
	$newField.append( $orig );
	
	// add the new field after the current one, and remove the current one
	// editing the current field is buggy in Webkit browsers
	$field.after( $newField );
	$field.remove();
	
	return $newField;
},

/**
 * Get a good width for the edit bar based on the original field.
 */
fieldWidth: function( $field, minWidth ) {
	// calculate width based on the text width witin the page flow
	// this means that this width will never exeed the original width when it's a multiline sentence
	var width = $field.textWidth() - 5;
	if( minWidth === undefined ) minWidth = 300;
	if( width < minWidth ) width = minWidth;
	return width;
},

/**
 * Adds an edit bar to the field with preview and cancel functionality.
 */
addEditBar: function( $newSpan, width, wiki ) {
	// build the input field
	var $input = $( '<textarea style="width: ' + (width-60-60-10) + 'px;"></textarea>' );
	$input.text( wiki );
	
	// build preview and cancel buttons and add click events
	var $preview = $( '<input type="button" value="Preview" style="width: 60px; margin-left: ' + (3) + 'px;" class="preview"/>' );
	var $cancel = $( '<input type="button" value="Cancel" style="width: 60px; margin-left: ' + (60+3+3) + 'px;" class="cancel"/>' );
	$preview.click( $.inlineEditor.basicEditor.preview );
	$cancel.click( $.inlineEditor.basicEditor.cancel );
	
	// build the edit bar from the input field and buttons
	var $editBar = $( '<span class="editbar" style="width: ' + width + 'px"></span>' );
	$editBar.append( $input );
	$editBar.append( $preview );
	$editBar.append( $cancel );
	
	// append the edit bar to the new span
	$newSpan.append( $editBar );
	
	// automatically resize the textarea using the Elastic plugin
	$input.elastic();
	
	// focus on the input so you can start typing immediately
	$input.focus();
	
	return $editBar;
},

/**
 * Default click handler for simple editors. Recommended to override.
 */
click: function( event ) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the element and retrieve the corresponding wikitext
	var $field = $(this);
	var wiki = $.inlineEditor.getTextById( $field.attr( 'id' ) );
	var width = $.inlineEditor.basicEditor.fieldWidth( $field );
	
	$newField = $.inlineEditor.basicEditor.newField( $field, $.inlineEditor.basicEditor.click );
	$.inlineEditor.basicEditor.addEditBar( $newField, width, wiki );
},

/**
 * Cancels the current edit operation.
 */
cancel: function( event ) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the outer span, two parents above the buttons
	var $span = $(this).parent().parent();
	
	// find the span with the original value
	var $orig = $span.children('.orig');
	
	// convert the span to it's original state
	$orig.removeClass( 'orig' );
	
	// place the original span after the current span and remove the current span
	// editing the current span is buggy in Webkit browsers
	$span.after( $orig );
	$span.remove();
	
	// highlight the text orange and have it fade to blue again
	// this is a visual indicator to where the element is now
	$orig.addClass( 'lastEdit' );
	$orig.removeClass( 'lastEdit', 800 );
},

/**
 * Previews the current edit operation.
 */
preview: function( event ) {
	// prevent clicks from reaching other elements
	event.stopPropagation();
	event.preventDefault();
	
	// find the span with class 'editbar', one parent above the buttons
	var $editbar = $(this).parent();
	
	// the element is one level above the editbar
	var $element = $editbar.parent(); 
	
	// add a visual indicator to show the preview is loading 
	$element.addClass( 'saving' );
	var $overlay = $( '<div class="overlay"><div class="alpha"></div><img class="spinner" src="' + wgScriptPath + '/extensions/InlineEditor/ajax-loader.gif"/></div>' );
	
	// if it's an inline element, put it *inside* the editbar, else outside
	if( $element.hasClass( 'inline' ) ) {
		$editbar.append( $overlay );
	}
	else {
		$editbar.after( $overlay );
	}
	
	// get the edited text and the id to save it to
	text = $editbar.children( 'textarea' ).val();
	id   = $element.children( '.orig' ).attr( 'id' );
	
	// let the inlineEditor framework handle the preview
	$.inlineEditor.previewTextById( text, id );
},

/**
 * Cancel all basic editors. Recommended to call when switching edit modes.
 */
cancelAll: function() {
	$('.editing').find('.cancel').click();
}

}; } ) ( jQuery );


