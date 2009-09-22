(function( $ ) {
$.fn.extend( {
	/**
	 * Bind a callback to an event in a delayed fashion.
	 * In detail, this means that the callback will be called a certain
	 * time after the event fires, but the timer is reset every time
	 * the event fires.
	 * @param event Name of the event (string)
	 * @param callback Function to call
	 * @param timeout Number of milliseconds to wait
	 */
	delayedBind: function( event, callback, timeout ) {
		return this.each( function() {
			var that = this;
			$(this).bind( event, function() {
				var timerID = $(this).data( '_delayedBindTimerID-' + event );
				var args = arguments;
				// Cancel the running timer
				if ( typeof timerID != 'undefined' )
					clearTimeout( timerID );
				timerID = setTimeout( function() {
						callback.apply( that, args );
					}, timeout );
				$(this).data( '_delayedBindTimerID-' + event, timerID );
			} );
		} );
	},
	
	/**
	 * Cancel the timers for delayed events on the selected elements.
	 */
	delayedBindCancel: function( event ) {
		return this.each( function() {
			var timerID = $(this).data( '_delayedBindTimerID-' + event );
			if ( typeof timerID != 'undefined' )
				clearTimeout( timerID );
		} );
	}
} );
} )( jQuery );
