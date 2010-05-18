/**
 * This plugin provides functionallity to expand a text box on focus to double it's current width
 *
 * Usage:
 *
 * Set options:
 *		$('#textbox').expandableField( { option1: value1, option2: value2 } );
 *		$('#textbox').expandableField( option, value );
 * Get option:
 *		value = $('#textbox').expandableField( option );
 * Initialize:
 *		$('#textbox').expandableField();
 *
 * Options:
 *
 */
( function( $ ) {

$.expandableField = {
	/**
	 * Cancel any delayed updateSuggestions() call and inform the user so
	 * they can cancel their result fetching if they use AJAX or something
	 */
	expandField: function( e, context ) {
		context.data.$field
		.css( { 'display' : 'inline-block' } )
		.animate( { 'width': context.data.expandedWidth } );
	},
	/**
	 * Restore the text the user originally typed in the textbox, before it was overwritten by highlight(). This
	 * restores the value the currently displayed suggestions are based on, rather than the value just before
	 * highlight() overwrote it; the former is arguably slightly more sensible.
	 */
	condenseField: function( e, context ) {
		context.data.$field
			.css( { 'display' : 'inline-block' } )
			.animate( { 'width': context.data.condensedWidth, 'display': 'inline'} );
	},
	/**
	 * Sets the value of a property, and updates the widget accordingly
	 * @param {String} property Name of property
	 * @param {Mixed} value Value to set property with
	 */
	configure: function( context, property, value ) {
		// Validate creation using fallback values
		switch( property ) {
			default:
				context.config[property] = value;
				break;
		}
	}

};
$.fn.expandableField = function() {
	
	// Multi-context fields
	var returnValue = null;
	var args = arguments;
	
	$( this ).each( function() {

		/* Construction / Loading */
		
		var context = $( this ).data( 'expandableField-context' );
		if ( context == null ) {
			context = {
				config: {
				}
			};
		}
		
		/* API */
		
		// Handle various calling styles
		if ( args.length > 0 ) {
			if ( typeof args[0] == 'object' ) {
				// Apply set of properties
				for ( var key in args[0] ) {
					$.suggestions.configure( context, key, args[0][key] );
				}
			} else if ( typeof args[0] == 'string' ) {
				if ( args.length > 1 ) {
					// Set property values
					$.suggestions.configure( context, args[0], args[1] );
				} else if ( returnValue == null ) {
					// Get property values, but don't give access to internal data - returns only the first
					returnValue = ( args[0] in context.config ? undefined : context.config[args[0]] );
				}
			}
		}
		
		/* Initialization */
		
		if ( typeof context.data == 'undefined' ) {
			context.data = {
				// The width of the field in it's condensed state
				'condensedWidth': $( this ).width(),
				// The width of the field in it's expanded state
				'expandedWidth': $( this ).width() * 2,
				// Reference to the field
				'$field': $( this )
			};
			
			$( this )
				.addClass( 'expandableField-condensed' )
				.focus( function( e ) {
					$.expandableField.expandField( e, context );
				} )
				.blur( function( e ) {
					$.expandableField.condenseField( e, context );
				} );
		}
		// Store the context for next time
		$( this ).data( 'expandableField-context', context );
	} );
	return returnValue !== null ? returnValue : $(this);
};

} )( jQuery );
