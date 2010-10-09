/**
 * Client side framework of the InlineEditor. Facilitates publishing, previewing,
 * switching edit modes and undo/redo operations.
 */
( function( $ ) { $.inlineEditor = {
editors: {},

editModes: [],
currentMode: '',

states: [],
currentState: 0,
lastState: 0,

/**
 * Adds the initial state from the current HTML and a wiki string.
 */
addInitialState: function( stateWiki ) {
	$.inlineEditor.states[0] = {
		wiki: stateWiki,
		html: $('#editContent').html()
	};
	$.inlineEditor.currentState = 0;
},

/**
 * Returns wikitext in the current state given an ID.
 */
getTextById: function( id ) {
	return $.inlineEditor.states[$.inlineEditor.currentState].wiki.locations[id].text;
},

/**
 * Previews given a new text for a given field by ID.
 */
previewTextById: function( text, id ) {
	// send out an AJAX request which will be handled by addNewState()
	var data = {
			'originalWiki': $.inlineEditor.states[$.inlineEditor.currentState].wiki,
			'lastEdit': { 'id': id, 'text': text }
	};
	
	var args = [ JSON.stringify( data ), wgPageName ];
	sajax_request_type = 'POST';
	sajax_do_call( 'InlineEditor::ajaxPreview', args, $.inlineEditor.addNewState );
},

/**
 * Adds a new state from an AJAX request.
 */
addNewState: function( request ) {
	// save the state and show it on the screen
	state = JSON.parse( request.responseText );
	$('#editContent').html( state.html );
	$.inlineEditor.currentState += 1;
	$.inlineEditor.states[$.inlineEditor.currentState] = state;
	
	// clear out all states after the current state, because undo/redo would be broken
	var i = $.inlineEditor.currentState + 1;
	while( i <= $.inlineEditor.lastState ) {
		delete $.inlineEditor.states[i];
		i += 1;
	}
	$.inlineEditor.lastState = $.inlineEditor.currentState;
	
	// reload the current editor and update the edit counter
	$.inlineEditor.reload();
	$.inlineEditor.updateEditCounter();
},

/**
 * Reloads the current editor and finish some things in the HTML.
 */
reload: function() {
	// reload the current editor
	$.inlineEditor.editors[$.inlineEditor.currentMode].reload();
	
	// fade out all lastEdit elements
	$('.lastEdit').removeClass('lastEdit', 200);
	
	// make the links in the article unusable
	$('#editContent a').click(function(event) { event.preventDefault(); });
},

/**
 * Moves back one state.
 */
undo: function( event ) {
	event.stopPropagation();
	event.preventDefault();
	
	// check if we can move backward one state and do it
	if( $.inlineEditor.currentState > 0 ) {
		$.inlineEditor.currentState -= 1;
		$('#editContent').html( $.inlineEditor.states[$.inlineEditor.currentState].html );
		$.inlineEditor.reload();
	}
	
	// refresh the edit counter regardless of actually switching, this confirms
	// that the button works, even if there is nothing to switch to
	$.inlineEditor.updateEditCounter();
},

/**
 * Moves forward one state.
 */
redo: function( event ) {
	event.stopPropagation();
	event.preventDefault();
	
	// check if we can move forward one state and do it
	if( $.inlineEditor.currentState < $.inlineEditor.lastState ) {
		$.inlineEditor.currentState += 1;
		$('#editContent').html( $.inlineEditor.states[$.inlineEditor.currentState].html );
		$.inlineEditor.reload();
	}
	
	// refresh the edit counter regardless of actually switching, this confirms
	// that the button works, even if there is nothing to switch to
	$.inlineEditor.updateEditCounter();
},

/**
 * Updates the edit counter and makes it flash.
 */
updateEditCounter: function() {
	// update the value of the edit counter
	var $editCounter = $( '#editCounter' );
	$editCounter.text( '#' + $.inlineEditor.currentState );
	
	// remove everything from the editcounter, and have it fade again
	$editCounter.removeClass( 'changeHighlight' );
	$editCounter.attr( 'style', '' );
	$editCounter.addClass( 'changeHighlight' );
	$editCounter.removeClass( 'changeHighlight', 200 );
},

/**
 * Checks if the mode radio button has changed and selects the new editor and description.
 */
changeMode: function( event ) {
	// set the currently visible descriptions in the foreground to have them
	// fade away nicely
	$( '.editmode .descriptionInner' ).css( 'z-index', 2 );
	$( '.editmode .descriptionInner' ).fadeOut(300);
	
	// check for all options if they are selected
	for( var optionNr in $.inlineEditor.editModes ) {
		if( $.inlineEditor.editModes[optionNr] ) {
			var option = $.inlineEditor.editModes[optionNr];
			
			// if a certain option is selected, show the description
			// and set the edit mode in #content, then exit this function as only
			// one mode can be selected
			if( $( '#radio-' + option ).attr( 'checked' ) ) {
				// get the description, put it behind the currently visible description,
				// and show it instantly
				$description = $( '#description-' + option );
				$description.show();
				$description.css( 'z-index', 1 );
				
				// resize the outer box to match the description height, also take in
				// account the padding of the description box
				$( '.editmode .descriptionOuter' ).animate( {
					height: $description.height() + 15
				}, 600);
				
				// if we've actually switched, disable the previous editor and enable
				// the new one
				if( $.inlineEditor.currentMode != option ) {
					if( $.inlineEditor.editors[$.inlineEditor.currentMode] ) {
						$.inlineEditor.editors[$.inlineEditor.currentMode].disable();
					}
					
					$.inlineEditor.currentMode = option;
					
					if( $.inlineEditor.editors[$.inlineEditor.currentMode] ) {
						$.inlineEditor.editors[$.inlineEditor.currentMode].enable();
					}
				}
				
				// we've found the option to switch to, nothing to be done anymore
				return;
			}
		}
	}
},

/**
 * Publishes the document in its current state.
 */
publish: function( event ) {
	event.stopPropagation();
	event.preventDefault();
	
	// get the wikitext from the state as it's currently on the screen
	var data = { 'originalWiki': $.inlineEditor.states[$.inlineEditor.currentState].wiki };
	var json = JSON.stringify( data );
	
	// set and send the form
	$( '#json' ).val( json );
	$( '#editForm' ).submit();
},

/**
 * Initializes the editor.
 */
init : function() {
	// make the edit mode radiobuttons clickable
	$( '.optionMode' ).change( $.inlineEditor.changeMode );
	
	$( '#publish' ).click( $.inlineEditor.publish );
	$( '#undo' ).click( $.inlineEditor.undo );
	$( '#redo' ).click( $.inlineEditor.redo );
	
	// initially hide the descriptions, else things look messy because of the animation
	$( '.editmode .descriptionInner' ).hide();
	
	// open all our links in a new window except for switching to the full editor
	$( '#siteNotice a[class!=fulleditor]' ).attr( 'target', '_blank' );
	
	// check the current selected edit mode
	$.inlineEditor.changeMode();
	
	// reload the current editor
	$.inlineEditor.reload();
}

}; } ) ( jQuery );