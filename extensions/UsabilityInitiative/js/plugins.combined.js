/*
 * jQuery Asynchronous Plugin 1.0
 *
 * Copyright (c) 2008 Vincent Robert (genezys.net)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 *
 */
(function($){

// opts.delay : (default 10) delay between async call in ms
// opts.bulk : (default 500) delay during which the loop can continue synchronously without yielding the CPU
// opts.test : (default true) function to test in the while test part
// opts.loop : (default empty) function to call in the while loop part
// opts.end : (default empty) function to call at the end of the while loop
$.whileAsync = function(opts)
{
	var delay = Math.abs(opts.delay) || 10,
		bulk = isNaN(opts.bulk) ? 500 : Math.abs(opts.bulk),
		test = opts.test || function(){ return true; },
		loop = opts.loop || function(){},
		end  = opts.end  || function(){};
	
	(function(){

		var t = false, 
			begin = new Date();
			
		while( t = test() )
		{
			loop();
			if( bulk === 0 || (new Date() - begin) > bulk )
			{
				break;
			}
		}
		if( t ) 
		{
			setTimeout(arguments.callee, delay);
		}
		else
		{
			end();
		}
		
	})();
}

// opts.delay : (default 10) delay between async call in ms
// opts.bulk : (default 500) delay during which the loop can continue synchronously without yielding the CPU
// opts.loop : (default empty) function to call in the each loop part, signature: function(index, value) this = value
// opts.end : (default empty) function to call at the end of the each loop
$.eachAsync = function(array, opts)
{
	var i = 0, 
		l = array.length, 
		loop = opts.loop || function(){};
	
	$.whileAsync(
		$.extend(opts, {
			test: function(){ return i < l; },
			loop: function()
			{ 
				var val = array[i];
				return loop.call(val, i++, val);
			}
		})
	);
}

$.fn.eachAsync = function(opts)
{
	$.eachAsync(this, opts);
	return this;
}

})(jQuery);

/**
 * Plugin that automatically truncates the plain text contents of an element and adds an ellipsis 
 */
( function( $ ) {

$.fn.autoEllipse = function( options ) {
	$(this).each( function() {
		options = $.extend( {
			'position': 'center',
			'tooltip': false
		}, options );
		var text = $(this).text();
		var $text = $( '<span />' ).text( text ).css( 'whiteSpace', 'nowrap' );
		$(this).empty().append( $text );
		if ( $text.width() > $(this).width() ) {
			switch ( options.position ) {
				case 'right':
					// Use binary search-like technique for efficiency
					var l = 0, r = text.length;
					do {
						var m = Math.ceil( ( l + r ) / 2 );
						$text.text( text.substr( 0, m ) + '...' );
						if ( $text.width() > $(this).width() ) {
							// Text is too long
							r = m - 1;
						} else {
							l = m;
						}
					} while ( l < r );
					$text.text( text.substr( 0, l ) + '...' );
					break;
				case 'center':
					// TODO: Use binary search like for 'right'
					var i = [Math.round( text.length / 2 ), Math.round( text.length / 2 )];
					var side = 1; // Begin with making the end shorter
					while ( $text.outerWidth() > ( $(this).width() ) && i[0] > 0 ) {
						$text.text( text.substr( 0, i[0] ) + '...' + text.substr( i[1] ) );
						// Alternate between trimming the end and begining
						if ( side == 0 ) {
							// Make the begining shorter
							i[0]--;
							side = 1;
						} else {
							// Make the end shorter
							i[1]++;
							side = 0;
						}
					}
					break;
				case 'left':
					// TODO: Use binary search like for 'right'
					var r = 0;
					while ( $text.outerWidth() > $(this).width() && r < text.length ) {
						$text.text( '...' + text.substr( r ) );
						r++;
					}
					break;
			}
			if ( options.tooltip )
				$text.attr( 'title', text );
		}
	} );
};

} )( jQuery );/*

jQuery Browser Plugin
	* Version 2.3
	* 2008-09-17 19:27:05
	* URL: http://jquery.thewikies.com/browser
	* Description: jQuery Browser Plugin extends browser detection capabilities and can assign browser selectors to CSS classes.
	* Author: Nate Cavanaugh, Minhchau Dang, & Jonathan Neal
	* Copyright: Copyright (c) 2008 Jonathan Neal under dual MIT/GPL license.
	* JSLint: This javascript file passes JSLint verification.
*//*jslint
		bitwise: true,
		browser: true,
		eqeqeq: true,
		forin: true,
		nomen: true,
		plusplus: true,
		undef: true,
		white: true
*//*global
		jQuery
*/

(function ($) {
	$.browserTest = function (a, z) {
		var u = 'unknown', x = 'X', m = function (r, h) {
			for (var i = 0; i < h.length; i = i + 1) {
				r = r.replace(h[i][0], h[i][1]);
			}

			return r;
		}, c = function (i, a, b, c) {
			var r = {
				name: m((a.exec(i) || [u, u])[1], b)
			};

			r[r.name] = true;

			r.version = (c.exec(i) || [x, x, x, x])[3];

			if (r.name.match(/safari/) && r.version > 400) {
				r.version = '2.0';
			}

			if (r.name === 'presto') {
				r.version = ($.browser.version > 9.27) ? 'futhark' : 'linear_b';
			}
			r.versionNumber = parseFloat(r.version, 10) || 0;
			r.versionX = (r.version !== x) ? (r.version + '').substr(0, 1) : x;
			r.className = r.name + r.versionX;

			return r;
		};

		a = (a.match(/Opera|Navigator|Minefield|KHTML|Chrome/) ? m(a, [
			[/(Firefox|MSIE|KHTML,\slike\sGecko|Konqueror)/, ''],
			['Chrome Safari', 'Chrome'],
			['KHTML', 'Konqueror'],
			['Minefield', 'Firefox'],
			['Navigator', 'Netscape']
		]) : a).toLowerCase();

		$.browser = $.extend((!z) ? $.browser : {}, c(a, /(camino|chrome|firefox|netscape|konqueror|lynx|msie|opera|safari)/, [], /(camino|chrome|firefox|netscape|netscape6|opera|version|konqueror|lynx|msie|safari)(\/|\s)([a-z0-9\.\+]*?)(\;|dev|rel|\s|$)/));

		$.layout = c(a, /(gecko|konqueror|msie|opera|webkit)/, [
			['konqueror', 'khtml'],
			['msie', 'trident'],
			['opera', 'presto']
		], /(applewebkit|rv|konqueror|msie)(\:|\/|\s)([a-z0-9\.]*?)(\;|\)|\s)/);

		$.os = {
			name: (/(win|mac|linux|sunos|solaris|iphone)/.exec(navigator.platform.toLowerCase()) || [u])[0].replace('sunos', 'solaris')
		};

		if (!z) {
			$('html').addClass([$.os.name, $.browser.name, $.browser.className, $.layout.name, $.layout.className].join(' '));
		}
	};

	$.browserTest(navigator.userAgent);
})(jQuery);

( function( $ ) {

$.fn.collapsibleTabs = function( $$options ) {
	// return if the function is called on an empty jquery object
	if( !this.length ) return this; 
	//merge options into the defaults
	var $settings = $.extend( {}, $.collapsibleTabs.defaults, $$options );

	this.each( function() {
		var $this = $( this );
		// add the element to our array of collapsible managers
		$.collapsibleTabs.instances = ( $.collapsibleTabs.instances.length == 0 ? 
			$this : $.collapsibleTabs.instances.add( $this ) );
		// attach the settings to the elements
		$this.data( 'collapsibleTabsSettings', $settings );
		// attach data to our collapsible elements
		$this.children( $settings.collapsible ).each( function() {
			var $collapsible = $( this );
			$collapsible.data( 'collapsibleTabsSettings', {
				'expandedContainer': $settings.expandedContainer,
				'collapsedContainer': $settings.collapsedContainer,
				'expandedWidth': $collapsible.width(),
				'prevElement': $collapsible.prev()
			} );
		} );
	} );
	
	// if we haven't already bound our resize hanlder, bind it now
	if( !$.collapsibleTabs.boundEvent ) {
		$( window )
			.delayedBind( '500', 'resize', function( ) { $.collapsibleTabs.handleResize(); } );
	}
	// call our resize handler to setup the page
	$.collapsibleTabs.handleResize();
	return this;
};

$.collapsibleTabs = {
	instances: [],
	boundEvent: null,
	defaults: {
		expandedContainer: '#p-views ul',
		collapsedContainer: '#p-cactions ul',
		collapsible: 'li.collapsible',
		shifting: false,
		expandCondition: function( eleWidth ) {
			return ( $( '#left-navigation' ).position().left + $( '#left-navigation' ).width() ) 
				< ( $( '#right-navigation' ).position().left - eleWidth );
		},
		collapseCondition: function() {
			return ( $( '#left-navigation' ).position().left + $( '#left-navigation' ).width() )
				> $( '#right-navigation' ).position().left;
		}
	},
	handleResize: function( e ){
		$.collapsibleTabs.instances.each( function() {
			var $this = $( this ), data = $this.data( 'collapsibleTabsSettings' );
			if( data.shifting ) return;

			// if the two navigations are colliding 
			if( $this.children( data.collapsible ).length > 0 && data.collapseCondition() ) {
				
				$this.trigger( "beforeTabCollapse" );
				// move the element to the dropdown menu
				$.collapsibleTabs.moveToCollapsed( $this.children( data.collapsible + ':last' ) );
			}

			// if there are still moveable items in the dropdown menu, 
			// and there is sufficient space to place them in the tab container
			if( $( data.collapsedContainer + ' ' + data.collapsible ).length > 0
					&& data.expandCondition( $( data.collapsedContainer ).children(
							data.collapsible+":first" ).data( 'collapsibleTabsSettings' ).expandedWidth ) ) {
				//move the element from the dropdown to the tab
				$this.trigger( "beforeTabExpand" );
				$.collapsibleTabs
					.moveToExpanded( data.collapsedContainer + " " + data.collapsible + ':first' );
			}
		});
	},
	moveToCollapsed: function( ele ) {
		var $moving = $( ele );
		var data = $moving.data( 'collapsibleTabsSettings' );
		$( data.expandedContainer ).data( 'collapsibleTabsSettings' ).shifting = true;
		$moving
			.remove()
			.prependTo( data.collapsedContainer )
			.data( 'collapsibleTabsSettings', data );
		$( data.expandedContainer ).data( 'collapsibleTabsSettings' ).shifting = false;
		$.collapsibleTabs.handleResize();
	},
	moveToExpanded: function( ele ) {
		var $moving = $( ele );
		var data = $moving.data( 'collapsibleTabsSettings' );
		$( data.expandedContainer ).data( 'collapsibleTabsSettings' ).shifting = true;
		// remove this element from where it's at and put it in the dropdown menu
		$moving.remove().insertAfter( data.prevElement ).data( 'collapsibleTabsSettings', data );
		$( data.expandedContainer ).data( 'collapsibleTabsSettings' ).shifting = false;
		$.collapsibleTabs.handleResize();
	}
};

} )( jQuery );/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

(function( $ ) {
/**
 * Function that escapes spaces in event names. This is needed because
 * "_delayedBind-foo bar-1000" refers to two events
 */
function encodeEvent( event ) {
	return event.replace( /-/g, '--' ).replace( / /g, '-' );
}

$.fn.extend( {
	/**
	 * Bind a callback to an event in a delayed fashion.
	 * In detail, this means that the callback will be called a certain
	 * time after the event fires, but the timer is reset every time
	 * the event fires.
	 * @param timeout Number of milliseconds to wait
	 * @param event Name of the event (string)
	 * @param data Data to pass to the event handler (optional)
	 * @param callback Function to call
	 */
	delayedBind: function( timeout, event, data, callback ) {
		var encEvent = encodeEvent( event );
		return this.each( function() {
			var that = this;
			// Bind the top half
			// Do this only once for every (event, timeout) pair
			if (  !( $(this).data( '_delayedBindBound-' + encEvent + '-' + timeout ) ) ) {
				$(this).data( '_delayedBindBound-' + encEvent + '-' + timeout, true );
				$(this).bind( event, function() {
					var timerID = $(this).data( '_delayedBindTimerID-' + encEvent + '-' + timeout );
					// Cancel the running timer
					if ( typeof timerID != 'undefined' )
						clearTimeout( timerID );
					timerID = setTimeout( function() {
						$(that).trigger( '_delayedBind-' + encEvent + '-' + timeout );
					}, timeout );
					$(this).data( '_delayedBindTimerID-' + encEvent + '-' + timeout, timerID );
				} );
			}
			
			// Bottom half
			$(this).bind( '_delayedBind-' + encEvent + '-' + timeout, data, callback );
		} );
	},
	
	/**
	 * Cancel the timers for delayed events on the selected elements.
	 */
	delayedBindCancel: function( timeout, event ) {
		var encEvent = encodeEvent( event );
		return this.each( function() {
			var timerID = $(this).data( '_delayedBindTimerID-' + encEvent + '-' + timeout );
			if ( typeof timerID != 'undefined' )
				clearTimeout( timerID );
		} );
	},
	
	/**
	 * Unbind an event bound with delayedBind()
	 */
	delayedBindUnbind: function( timeout, event, callback ) {
		var encEvent = encodeEvent( event );
		return this.each( function() {
			$(this).unbind( '_delayedBind-' + encEvent + '-' + timeout, callback );
		} );
	}
} );
} )( jQuery );
/**
 * Plugin that fills a <select> with namespaces
 */

(function ($) {
$.fn.namespaceSelector = function( defaultNS ) {
	if ( typeof defaultNS == 'undefined' )
		defaultNS = 0;
	return this.each( function() {
		for ( var id in wgFormattedNamespaces ) {
			var opt = $( '<option />' )
				.attr( 'value', id )
				.text( wgFormattedNamespaces[id] );
			if ( id == defaultNS )
				opt.attr( 'selected', 'selected' );
			opt.appendTo( $(this) );
		}
	});
};})(jQuery);

/**
 * This plugin provides a generic way to add suggestions to a text box.
 * 
 * Usage:
 * 
 * Set options:
 *		$('#textbox').suggestions( { option1: value1, option2: value2 } );
 *		$('#textbox').suggestions( option, value );
 * Get option:
 *		value = $('#textbox').suggestions( option );
 * Initialize:
 *		$('#textbox').suggestions();
 * 
 * Options:
 * 
 * fetch(query): Callback that should fetch suggestions and set the suggestions property. Executed in the context of the
 * 		textbox
 * 		Type: Function
 * cancel: Callback function to call when any pending asynchronous suggestions fetches should be canceled.
 * 		Executed in the context of the textbox
 *		Type: Function
 * special: Set of callbacks for rendering and selecting
 *		Type: Object of Functions 'render' and 'select'
 * result: Set of callbacks for rendering and selecting
 *		Type: Object of Functions 'render' and 'select'
 * $region: jQuery selection of element to place the suggestions below and match width of
 * 		Type: jQuery Object, Default: $(this)
 * suggestions: Suggestions to display
 * 		Type: Array of strings
 * maxRows: Maximum number of suggestions to display at one time
 * 		Type: Number, Range: 1 - 100, Default: 7
 * delay: Number of ms to wait for the user to stop typing
 * 		Type: Number, Range: 0 - 1200, Default: 120
 */
( function( $ ) {

$.suggestions = {
	/**
	 * Cancel any delayed updateSuggestions() call and inform the user so
	 * they can cancel their result fetching if they use AJAX or something 
	 */
	cancel: function( context ) {
		if ( context.data.timerID != null ) {
			clearTimeout( context.data.timerID );
		}
		if ( typeof context.config.cancel == 'function' ) {
			context.config.cancel.call( context.data.$textbox );
		}
	},
	/**
	 * Restore the text the user originally typed in the textbox, before it was overwritten by highlight(). This
	 * restores the value the currently displayed suggestions are based on, rather than the value just before
	 * highlight() overwrote it; the former is arguably slightly more sensible.
	 */
	restore: function( context ) {
		context.data.$textbox.val( context.data.prevText );
	},
	/**
	 * Ask the user-specified callback for new suggestions. Any previous delayed call to this function still pending
	 * will be canceled.  If the value in the textbox hasn't changed since the last time suggestions were fetched, this
	 * function does nothing.
	 * @param {Boolean} delayed Whether or not to delay this by the currently configured amount of time
	 */
	update: function( context, delayed ) {
		// Only fetch if the value in the textbox changed
		function maybeFetch() {
			if ( context.data.$textbox.val() !== context.data.prevText ) {
				context.data.prevText = context.data.$textbox.val();
				if ( typeof context.config.fetch == 'function' ) {
					context.config.fetch.call( context.data.$textbox, context.data.$textbox.val() );
				}
			}
		}
		// Cancel previous call
		if ( context.data.timerID != null ) {
			clearTimeout( context.data.timerID );
		}
		if ( delayed ) {
			// Start a new asynchronous call
			context.data.timerID = setTimeout( maybeFetch, context.config.delay );
		} else {
			maybeFetch();
		}
		$.suggestions.special( context );
	},
	special: function( context ) {
		// Allow custom rendering - but otherwise don't do any rendering
		if ( typeof context.config.special.render == 'function' ) {
			// Wait for the browser to update the value
			setTimeout( function() {
				// Render special
				$special = context.data.$container.find( '.suggestions-special' );
				context.config.special.render.call( $special, context.data.$textbox.val() );
			}, 1 );
		}
	},
	/**
	 * Sets the value of a property, and updates the widget accordingly
	 * @param {String} property Name of property
	 * @param {Mixed} value Value to set property with
	 */
	configure: function( context, property, value ) {
		// Validate creation using fallback values
		switch( property ) {
			case 'fetch':
			case 'cancel':
			case 'special':
			case 'result':
			case '$region':
				context.config[property] = value;
				break;
			case 'suggestions':
				context.config[property] = value;
				// Update suggestions
				if ( typeof context.data !== 'undefined'  ) {
					if ( typeof context.config.suggestions == 'undefined' ||
							context.config.suggestions.length == 0 ) {
						// Hide the div when no suggestion exist
						context.data.$container.hide();
					} else {
						// Rebuild the suggestions list
						context.data.$container.show();
						// Update the size and position of the list
						context.data.$container.css( {
							'top': context.config.$region.offset().top + context.config.$region.outerHeight(),
							'bottom': 'auto',
							'width': context.config.$region.outerWidth(),
							'height': 'auto',
							'left': context.config.$region.offset().left,
							'right': 'auto'
						} );
						var $results = context.data.$container.children( '.suggestions-results' );
						$results.empty();
						for ( var i = 0; i < context.config.suggestions.length; i++ ) {
							$result = $( '<div />' )
								.addClass( 'suggestions-result' )
								.attr( 'rel', i )
								.data( 'text', context.config.suggestions[i] )
								.appendTo( $results );
							// Allow custom rendering
							if ( typeof context.config.result.render == 'function' ) {
								context.config.result.render.call( $result, context.config.suggestions[i] );
							} else {
								$result.text( context.config.suggestions[i] ).autoEllipse();
							}
						}
					}
				}
				break;
			case 'maxRows':
				context.config[property] = Math.max( 1, Math.min( 100, value ) );
				break;
			case 'delay':
				context.config[property] = Math.max( 0, Math.min( 1200, value ) );
				break;
			case 'submitOnClick':
				context.config[property] = value ? true : false;
				break;
		}
	},
	/**
	 * Highlight a result in the results table
	 * @param result <tr> to highlight: jQuery object, or 'prev' or 'next'
	 * @param updateTextbox If true, put the suggestion in the textbox
	 */
	highlight: function( context, result, updateTextbox ) {
		var selected = context.data.$container.find( '.suggestions-result-current' );
		if ( !result.get || selected.get( 0 ) != result.get( 0 ) ) {
			if ( result == 'prev' ) {
				result = selected.prev();
			} else if ( result == 'next' ) {
				if ( selected.size() == 0 )
					// No item selected, go to the first one
					result = context.data.$container.find( '.suggestions-results div:first' );
				else {
					result = selected.next();
					if ( result.size() == 0 )
						// We were at the last item, stay there
						result = selected;
				}
			}
			selected.removeClass( 'suggestions-result-current' );
			result.addClass( 'suggestions-result-current' );
		}
		if ( updateTextbox ) {
			if ( result.size() == 0 ) {
				$.suggestions.restore( context );
			} else {
				context.data.$textbox.val( result.data( 'text' ) );
				
				// .val() doesn't call any event handlers, so
				// let the world know what happened
				context.data.$textbox.change();
			}
		}
		$.suggestions.special( context );
	},
	/**
	 * Respond to keypress event
	 * @param {Integer} key Code of key pressed
	 */
	keypress: function( e, context, key ) {
		var wasVisible = context.data.$container.is( ':visible' );
		var preventDefault = false;
		switch ( key ) {
			// Arrow down
			case 40:
				if ( wasVisible ) {
					$.suggestions.highlight( context, 'next', true );
				} else {
					$.suggestions.update( context, false );
				}
				context.data.$textbox.trigger( 'change' );
				preventDefault = true;
				break;
			// Arrow up
			case 38:
				if ( wasVisible ) {
					$.suggestions.highlight( context, 'prev', true );
				}
				context.data.$textbox.trigger( 'change' );
				preventDefault = wasVisible;
				break;
			// Escape
			case 27:
				context.data.$container.hide();
				$.suggestions.restore( context );
				$.suggestions.cancel( context );
				context.data.$textbox.trigger( 'change' );
				preventDefault = wasVisible;
				break;
			// Enter
			case 13:
				context.data.$container.hide();
				preventDefault = wasVisible;
				if ( typeof context.config.result.select == 'function' ) {
					context.config.result.select.call(
						context.data.$container.find( '.suggestions-result-current' ),
						context.data.$textbox
					);
				}
				break;
			default:
				$.suggestions.update( context, true );
				break;
		}
		if ( preventDefault ) {
			e.preventDefault();
			e.stopImmediatePropagation();
		}
	}
};
$.fn.suggestions = function() {
	
	// Multi-context fields
	var returnValue = null;
	var args = arguments;
	
	$(this).each( function() {

		/* Construction / Loading */
		
		var context = $(this).data( 'suggestions-context' );
		if ( typeof context == 'undefined' ) {
			context = {
				config: {
				    'fetch' : function() {},
					'cancel': function() {},
					'special': {},
					'result': {},
					'$region': $(this),
					'suggestions': [],
					'maxRows': 7,
					'delay': 120,
					'submitOnClick': false
				}
			};
		}
		
		/* API */
		
		// Handle various calling styles
		if ( args.length > 0 ) {
			if ( typeof args[0] == 'object' ) {
				// Apply set of properties
				for ( key in args[0] ) {
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
				// ID of running timer
				'timerID': null,
				// Text in textbox when suggestions were last fetched
				'prevText': null,
				// Number of results visible without scrolling
				'visibleResults': 0,
				// Suggestion the last mousedown event occured on
				'mouseDownOn': $( [] ),
				'$textbox': $(this)
			};
			context.data.$container = $( '<div />' )
				.css( {
					'top': Math.round( context.data.$textbox.offset().top + context.data.$textbox.outerHeight() ),
					'left': Math.round( context.data.$textbox.offset().left ),
					'width': context.data.$textbox.outerWidth(),
					'display': 'none'
				} )
				.mouseover( function( e ) {
					$.suggestions.highlight( context, $( e.target ).closest( '.suggestions-results div' ), false );
				} )
				.addClass( 'suggestions' )
				.append(
					$( '<div />' ).addClass( 'suggestions-results' )
						// Can't use click() because the container div is hidden when the textbox loses focus. Instead,
						// listen for a mousedown followed by a mouseup on the same div
						.mousedown( function( e ) {
							context.data.mouseDownOn = $( e.target ).closest( '.suggestions-results div' );
						} )
						.mouseup( function( e ) {
							var $result = $( e.target ).closest( '.suggestions-results div' );
							var $other = context.data.mouseDownOn;
							context.data.mouseDownOn = $( [] );
							if ( $result.get( 0 ) != $other.get( 0 ) ) {
								return;
							}
							$.suggestions.highlight( context, $result, true );
							context.data.$container.hide();
							if ( typeof context.config.result.select == 'function' ) {
								context.config.result.select.call( $result, context.data.$textbox );
							}
							context.data.$textbox.focus();
						} )
				)
				.append(
					$( '<div />' ).addClass( 'suggestions-special' )
						// Can't use click() because the container div is hidden when the textbox loses focus. Instead,
						// listen for a mousedown followed by a mouseup on the same div
						.mousedown( function( e ) {
							context.data.mouseDownOn = $( e.target ).closest( '.suggestions-special' );
						} )
						.mouseup( function( e ) {
							var $special = $( e.target ).closest( '.suggestions-special' );
							var $other = context.data.mouseDownOn;
							context.data.mouseDownOn = $( [] );
							if ( $special.get( 0 ) != $other.get( 0 ) ) {
								return;
							}
							context.data.$container.hide();
							if ( typeof context.config.special.select == 'function' ) {
								context.config.special.select.call( $special, context.data.$textbox );
							}
							context.data.$textbox.focus();
						} )
				)
				.appendTo( $( 'body' ) );
			$(this)
				// Stop browser autocomplete from interfering
				.attr( 'autocomplete', 'off')
				.keydown( function( e ) {
					// Store key pressed to handle later
					context.data.keypressed = ( e.keyCode == undefined ) ? e.which : e.keyCode;
					context.data.keypressedCount = 0;
					
					switch ( context.data.keypressed ) {
						// This preventDefault logic is duplicated from
						// $.suggestions.keypress(), which sucks
						case 40:
							e.preventDefault();
							e.stopImmediatePropagation();
							break;
						case 38:
						case 27:
						case 13:
							if ( context.data.$container.is( ':visible' ) ) {
								e.preventDefault();
								e.stopImmediatePropagation();
							}
					}
				} )
				.keypress( function( e ) {
					context.data.keypressedCount++;
					$.suggestions.keypress( e, context, context.data.keypressed );
				} )
				.keyup( function( e ) {
					// Some browsers won't throw keypress() for arrow keys. If we got a keydown and a keyup without a
					// keypress in between, solve it
					if ( context.data.keypressedCount == 0 ) {
						$.suggestions.keypress( e, context, context.data.keypressed );
					}
				} )
				.blur( function() {
					// When losing focus because of a mousedown
					// on a suggestion, don't hide the suggestions 
					if ( context.data.mouseDownOn.size() > 0 ) {
						return;
					}
					context.data.$container.hide();
					$.suggestions.cancel( context );
				} );
		}
		// Store the context for next time
		$(this).data( 'suggestions-context', context );
	} );
	return returnValue !== null ? returnValue : $(this);
};

} )( jQuery );
/**
 * These plugins provide extra functionality for interaction with textareas.
 */
( function( $ ) {
$.fn.textSelection = function( command, options ) {
var fn = {
/**
 * Get the contents of the textarea
 */
getContents: function() {
	return this.val();
},

setContents: function( options ) {
	return this.val( options.contents );
},

/**
 * Get the currently selected text in this textarea. Will focus the textarea
 * in some browsers (IE/Opera)
 */
getSelection: function() {
	var e = this.get( 0 );
	var retval = '';
	if ( $(e).is( ':hidden' ) ) {
		// Do nothing
	} else if ( document.selection && document.selection.createRange ) {
		e.focus();
		var range = document.selection.createRange();
		retval = range.text;
	} else if ( e.selectionStart || e.selectionStart == '0' ) {
		retval = e.value.substring( e.selectionStart, e.selectionEnd );
	}
	return retval;
},
/**
 * Ported from skins/common/edit.js by Trevor Parscal
 * (c) 2009 Wikimedia Foundation (GPLv2) - http://www.wikimedia.org
 * 
 * Inserts text at the begining and end of a text selection, optionally
 * inserting text at the caret when selection is empty.
 */
encapsulateSelection: function( options ) {
	return this.each( function() {
		/**
		 * Check if the selected text is the same as the insert text
		 */ 
		function checkSelectedText() {
			if ( !selText ) {
				selText = options.peri;
				isSample = true;
			} else if ( options.replace ) {
				selText = options.peri;
			} else if ( selText.charAt( selText.length - 1 ) == ' ' ) {
				// Exclude ending space char
				selText = selText.substring(0, selText.length - 1);
				options.post += ' ';
			}
		}
		var selText = $(this).textSelection( 'getSelection' );
		var isSample = false;
		if ( this.style.display == 'none' ) {
			// Do nothing
		} else if ( this.selectionStart || this.selectionStart == '0' ) {
			// Mozilla/Opera
			$(this).focus();
			var startPos = this.selectionStart;
			var endPos = this.selectionEnd;
			checkSelectedText();
			if ( options.ownline ) {
				if ( startPos != 0 && this.value.charAt( startPos - 1 ) != "\n" ) {
					options.pre = "\n" + options.pre;
				}
				if ( this.value.charAt( endPos ) != "\n" ) {
					options.post += "\n";
				}
			}
			this.value = this.value.substring( 0, startPos ) + options.pre + selText + options.post +
				this.value.substring( endPos, this.value.length );
			if ( window.opera ) {
				options.pre = options.pre.replace( /\r?\n/g, "\r\n" );
				selText = selText.replace( /\r?\n/g, "\r\n" );
				options.post = options.post.replace( /\r?\n/g, "\r\n" );
			}
			if ( isSample ) {
				this.selectionStart = startPos + options.pre.length;
				this.selectionEnd = startPos + options.pre.length + selText.length;
			} else {
				this.selectionStart = startPos + options.pre.length + selText.length +
					options.post.length;
				this.selectionEnd = this.selectionStart;
			}
		} else if ( document.selection && document.selection.createRange ) {
			// IE
			$(this).focus();
			var range = document.selection.createRange();
			if ( options.ownline && range.moveStart ) {
				var range2 = document.selection.createRange();
				range2.collapse();
				range2.moveStart( 'character', -1 );
				// FIXME: Which check is correct?
				if ( range2.text != "\r" && range2.text != "\n" && range2.text != "" ) {
					options.pre = "\n" + options.pre;
				}
				var range3 = document.selection.createRange();
				range3.collapse( false );
				range3.moveEnd( 'character', 1 );
				if ( range3.text != "\r" && range3.text != "\n" && range3.text != "" ) {
					options.post += "\n";
				}
			}
			checkSelectedText();
			range.text = options.pre + selText + options.post;
			if ( isSample && range.moveStart ) {
				range.moveStart( 'character', - options.post.length - selText.length );
				range.moveEnd( 'character', - options.post.length );
			}
			range.select();
		}
		// Scroll the textarea to the inserted text
		$(this).textSelection( 'scrollToCaretPosition' );
		$(this).trigger( 'encapsulateSelection', [ options.pre, options.peri, options.post, options.ownline,
			options.replace ] );
	});
},
/**
 * Ported from Wikia's LinkSuggest extension
 * https://svn.wikia-code.com/wikia/trunk/extensions/wikia/LinkSuggest
 * Some code copied from
 * http://www.dedestruct.com/2008/03/22/howto-cross-browser-cursor-position-in-textareas/
 *
 * Get the position (in resolution of bytes not nessecarily characters)
 * in a textarea 
 */
 getCaretPosition: function( options ) {
	function getCaret( e ) {
		var caretPos = 0, endPos = 0;
		if ( $.browser.msie ) {
			// IE Support
			var postFinished = false;
			var periFinished = false;
			var postFinished = false;
			var preText, rawPreText, periText;
			var rawPeriText, postText, rawPostText;
			// Create range containing text in the selection
			var periRange = document.selection.createRange().duplicate();
			// Create range containing text before the selection
			var preRange = document.body.createTextRange();
			// Select all the text
			preRange.moveToElementText(e);
			// Move the end where we need it
			preRange.setEndPoint("EndToStart", periRange);
			// Create range containing text after the selection
			var postRange = document.body.createTextRange();
			// Select all the text
			postRange.moveToElementText(e);
			// Move the start where we need it
			postRange.setEndPoint("StartToEnd", periRange);
			// Load the text values we need to compare
			preText = rawPreText = preRange.text;
			periText = rawPeriText = periRange.text;
			postText = rawPostText = postRange.text;
			/*
			 * Check each range for trimmed newlines by shrinking the range by 1
			 * character and seeing if the text property has changed. If it has
			 * not changed then we know that IE has trimmed a \r\n from the end.
			 */
			do {
				if ( !postFinished ) {
					if ( preRange.compareEndPoints( "StartToEnd", preRange ) == 0 ) {
						postFinished = true;
					} else {
						preRange.moveEnd( "character", -1 )
						if ( preRange.text == preText ) {
							rawPreText += "\r\n";
						} else {
							postFinished = true;
						}
					}
				}
				if ( !periFinished ) {
					if ( periRange.compareEndPoints( "StartToEnd", periRange ) == 0 ) {
						periFinished = true;
					} else {
						periRange.moveEnd( "character", -1 )
						if ( periRange.text == periText ) {
							rawPeriText += "\r\n";
						} else {
							periFinished = true;
						}
					}
				}
				if ( !postFinished ) {
					if ( postRange.compareEndPoints("StartToEnd", postRange) == 0 ) {
						postFinished = true;
					} else {
						postRange.moveEnd( "character", -1 )
						if ( postRange.text == postText ) {
							rawPostText += "\r\n";
						} else {
							postFinished = true;
						}
					}
				}
			} while ( ( !postFinished || !periFinished || !postFinished ) );
			caretPos = rawPreText.replace( /\r\n/g, "\n" ).length;
			endPos = caretPos + rawPeriText.replace( /\r\n/g, "\n" ).length;
		} else if ( e.selectionStart || e.selectionStart == '0' ) {
			// Firefox support
			caretPos = e.selectionStart;
			endPos = e.selectionEnd;
		}
		return options.startAndEnd ? [ caretPos, endPos ] : caretPos;
	}
	return getCaret( this.get( 0 ) );
},
setSelection: function( options ) {
	return this.each( function() {
		if ( $(this).is( ':hidden' ) ) {
			// Do nothing
		} else if ( this.selectionStart || this.selectionStart == '0' ) {
			// Opera 9.0 doesn't allow setting selectionStart past
			// selectionEnd; any attempts to do that will be ignored
			// Make sure to set them in the right order
			if ( options.start > this.selectionEnd ) {
				this.selectionEnd = options.end;
				this.selectionStart = options.start;
			} else {
				this.selectionStart = options.start;
				this.selectionEnd = options.end;
			}
		} else if ( document.body.createTextRange ) {
			var selection = document.body.createTextRange();
			selection.moveToElementText( this );
			var length = selection.text.length;
			selection.moveStart( 'character', options.start );
			selection.moveEnd( 'character', -length + options.end );
			selection.select();
		}
	});
},
/**
 * Ported from Wikia's LinkSuggest extension
 * https://svn.wikia-code.com/wikia/trunk/extensions/wikia/LinkSuggest
 * 
 * Scroll a textarea to the current cursor position. You can set the cursor
 * position with setSelection()
 * @param force boolean Whether to force a scroll even if the caret position
 *  is already visible. Defaults to false
 */
scrollToCaretPosition: function( options ) {
	function getLineLength( e ) {
		return Math.floor( e.scrollWidth / ( $.os.name == 'linux' ? 7 : 8 ) );
	}
	function getCaretScrollPosition( e ) {
		// FIXME: This functions sucks and is off by a few lines most
		// of the time. It should be replaced by something decent.
		var text = e.value.replace( /\r/g, "" );
		var caret = $( e ).textSelection( 'getCaretPosition' );
		var lineLength = getLineLength( e );
		var row = 0;
		var charInLine = 0;
		var lastSpaceInLine = 0;
		for ( i = 0; i < caret; i++ ) {
			charInLine++;
			if ( text.charAt( i ) == " " ) {
				lastSpaceInLine = charInLine;
			} else if ( text.charAt( i ) == "\n" ) {
				lastSpaceInLine = 0;
				charInLine = 0;
				row++;
			}
			if ( charInLine > lineLength ) {
				if ( lastSpaceInLine > 0 ) {
					charInLine = charInLine - lastSpaceInLine;
					lastSpaceInLine = 0;
					row++;
				}
			}
		}
		var nextSpace = 0;
		for ( j = caret; j < caret + lineLength; j++ ) {
			if (
				text.charAt( j ) == " " ||
				text.charAt( j ) == "\n" ||
				caret == text.length
			) {
				nextSpace = j;
				break;
			}
		}
		if ( nextSpace > lineLength && caret <= lineLength ) {
			charInLine = caret - lastSpaceInLine;
			row++;
		}
		return ( $.os.name == 'mac' ? 13 : ( $.os.name == 'linux' ? 15 : 16 ) ) * row;
	}
	return this.each(function() {
		if ( $(this).is( ':hidden' ) ) {
			// Do nothing
		} else if ( this.selectionStart || this.selectionStart == '0' ) {
			// Mozilla
			var scroll = getCaretScrollPosition( this );
			if ( options.force || scroll < $(this).scrollTop() ||
					scroll > $(this).scrollTop() + $(this).height() )
				$(this).scrollTop( scroll );
		} else if ( document.selection && document.selection.createRange ) {
			// IE / Opera
			/*
			 * IE automatically scrolls the selected text to the
			 * bottom of the textarea at range.select() time, except
			 * if it was already in view and the cursor position
			 * wasn't changed, in which case it does nothing. To
			 * cover that case, we'll force it to act by moving one
			 * character back and forth.
			 */
			var range = document.selection.createRange();
			var pos = $(this).textSelection( 'getCaretPosition' );
			var oldScrollTop = this.scrollTop;
			range.moveToElementText( this );
			range.collapse();
			range.move( 'character', pos + 1);
			range.select();
			if ( this.scrollTop != oldScrollTop )
				this.scrollTop += range.offsetTop;
			else if ( options.force ) {
				range.move( 'character', -1 );
				range.select();
			}
		}
		$(this).trigger( 'scrollToPosition' );
	} );
}
};
	// Apply defaults
	switch ( command ) {
		//case 'getContents': // no params
		//case 'setContents': // no params with defaults
		//case 'getSelection': // no params
		case 'encapsulateSelection':
			options = $.extend( {
				'pre': '', // Text to insert before the cursor/selection
				'peri': '', // Text to insert between pre and post and select afterwards
				'post': '', // Text to insert after the cursor/selection
				'ownline': false, // Put the inserted text on a line of its own
				'replace': false // If there is a selection, replace it with peri instead of leaving it alone
			}, options );
			break;
		case 'getCaretPosition':
			options = $.extend( {
				'startAndEnd': false, // Return [start, end] instead of just start
			}, options );
			// FIXME: We may not need character position-based functions if we insert markers in the right places
			break;
		case 'setSelection':
			options = $.extend( {
				'start': undefined, // Position to start selection at
				'end': undefined // Position to end selection at. Defaults to start
			}, options );
			if ( options.end === undefined )
				options.end = options.start;
			// FIXME: We may not need character position-based functions if we insert markers in the right places
			break;
		case 'scrollToCaretPosition':
			options = $.extend( {
				'force': false // Force a scroll even if the caret position is already visible
			}, options );
			break;
	}
	var context = $(this).data( 'wikiEditor-context' );
	var hasIframe = context !== undefined && context.$iframe !== undefined;
	// iframe functions have not been implemented yet, this is a temp hack
	//var hasIframe = false;
	return ( hasIframe ? context.fn : fn )[command].call( this, options );
};

} )( jQuery );/**
 * This plugin provides a way to build a user interface around a textarea. You
 * can build the UI from a confguration..
 * 	$j( 'div#edittoolbar' ).wikiEditor(
 * 		{ 'modules': { 'toolbar': { ... config ... } } }
 * 	);
 * ...and add modules after it's already been initialized...
 * 	$j( 'textarea#wpTextbox1' ).wikiEditor(
 * 		'addModule', 'toc', { ... config ... }
 *	);
 * ...using the API, which is still be finished.
 */
( function( $ ) {

$.wikiEditor = {
	'modules': {},
	'instances': [],
	/**
	 * For each browser name, an array of conditions that must be met are supplied in [operaton, value] form where
	 * operation is a string containing a JavaScript compatible binary operator and value is either a number to be
	 * compared with $.browser.versionNumber or a string to be compared with $.browser.version
	 */
	'browsers': {
		'ltr': {
			'msie': [['>=', 7]],
			'firefox': [
				['>=', 2],
				['!=', '2.0'],
				['!=', '2.0.0.1'],
				['!=', '2.0.0.2'],
				['!=', '2.0.0.3'],
				['!=', '2.0.0.4']
			],
			'opera': [['>=', 9.6]],
			'safari': [['>=', 3.1]]
		},
		'rtl': {
			'msie': [['>=', 8]],
			'firefox': [
				['>=', 2],
				['!=', '2.0'],
				['!=', '2.0.0.1'],
				['!=', '2.0.0.2'],
				['!=', '2.0.0.3'],
				['!=', '2.0.0.4']
			],
			'opera': [['>=', 9.6]],
			'safari': [['>=', 3.1]]
		}
	},
	/**
	 * Path to images - this is a bit messy, and it would need to change if
	 * this code (and images) gets moved into the core - or anywhere for
	 * that matter...
	 */
	'imgPath' : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/',
	'isSupportKnown': function() {
		return $.browser.name in $.wikiEditor.browsers[$( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'];
	},
	'isSupported': function() {
		if ( !$.wikiEditor.isSupportKnown ) {
			// Assume good faith :)
			return true;
		}
		var browser = $.wikiEditor.browsers[$( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'][$.browser.name];
		for ( condition in browser ) {
			var op = browser[condition][0];
			var val = browser[condition][1];
			if ( typeof val == 'string' ) {
				if ( !( eval( '$.browser.version' + op + '"' + val + '"' ) ) ) {
					return false;
				}
			} else if ( typeof val == 'number' ) {
				if ( !( eval( '$.browser.versionNumber' + op + val ) ) ) {
					return false;
				}
			}
		}
		return true;
	},
	// Wraps gM from js2, but allows raw text to supercede
	'autoMsg': function( object, property ) {
		// Accept array of possible properties, of which the first one found will be used
		if ( typeof property == 'object' ) {
			for ( i in property ) {
				if ( property[i] in object || property[i] + 'Msg' in object ) {
					property = property[i];
					break;
				}
			}
		}
		if ( property in object ) {
			return object[property];
		} else if ( property + 'Msg' in object ) {
			return gM( object[property + 'Msg'] );
		} else {
			return '';
		}
	},
	'fixOperaBrokenness': function( s ) {
		/*
		// This function works around Opera's
		// broken newline handling in textareas.
		// .val() has \n while selection functions
		// treat newlines as \r\n
		
		if ( typeof $.isOperaBroken == 'undefined' && $.wikiEditor.instances.length > 0 ) {
			// Create a textarea inside a div
			// with zero area, to hide it properly
			var div = $( '<div />' )
				.height( 0 )
				.width( 0 )
				.insertBefore( $.wikiEditor.instances[0] );
			var textarea = $( '<textarea></textarea>' )
				.height( 0 )
				.appendTo( div )
				.val( "foo\r\nbar" );
			// Try to search&replace bar --> BAR
			var index = textarea.val().indexOf( 'bar' );
			textarea.select();
			textarea.setSelection( index, index + 3 );
			textarea.encapsulateSelection( '', 'BAR', '', false, true );
			if ( textarea.val().substr( -4 ) != 'BARr' )
				$.isOperaBroken = false;
			else
				$.isOperaBroken = true;
			div.remove();
		}
		if ( $.isOperaBroken )
			s = s.replace( /\n/g, "\r\n" );
		*/
		return s;
	}
};

$.fn.wikiEditor = function() {

// Skip any further work on browsers that are unsupported
if ( $j.wikiEditor.isSupportKnown() && !$j.wikiEditor.isSupported() ) {
	return $(this);
}

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'wikiEditor-context' );

// This only gets run on the first call
if ( typeof context == 'undefined' ) {
	
	var instance = $.wikiEditor.instances.length;
	context = { '$textarea': $(this), 'views': {}, 'modules': {}, 'data': {}, 'instance': instance };
	$.wikiEditor.instances[instance] = $(this);
	
	/* Externally Accessible API */
	
	context.api = {
		/**
		 * Accepts either a string of the name of a module to add without any
		 * additional configuration parameters, or an object with members keyed with
		 * module names and valued with configuration objects
		 */
		'addModule': function( context, data ) {
			// A safe way of calling an API function on a module
			function callModuleApi( module, call, data ) {
				if (
					module in $.wikiEditor.modules &&
					'fn' in $.wikiEditor.modules[module] &&
					call in $.wikiEditor.modules[module].fn
				) {
					$.wikiEditor.modules[module].fn[call]( context, data );
				}
			}
			if ( typeof data == 'string' ) {
				callModuleApi( data, 'create', {} );
			} else if ( typeof data == 'object' ) {
				for ( module in data ) {
					if ( typeof module == 'string' ) {
						callModuleApi( module, 'create', data[module] );
					}
				}
			}
		}
	};
	// Allow modules to extend the API
	for ( module in $.wikiEditor.modules ) {
		if ( 'api' in $.wikiEditor.modules[module] ) {
			for ( call in $.wikiEditor.modules[module].api ) {
				// Modules may not overwrite existing API functions - first come,
				// first serve
				if ( !( call in context.api ) ) {
					context.api[call] = $.wikiEditor.modules[module].api[call];
				}
			}
		}
	}
	/* Event Handlers */
	context.evt = {
		'change': function( event ) {
			// BTW: context is in event.data.context
			
			switch ( event.type ) {
				case 'keypress':
					if ( /* something interesting was deleted */ false ) {
						//console.log( 'MAJOR CHANGE' );
					} else {
						//console.log( 'MINOR CHANGE' );
					}
					break;
				case 'mousedown': // FIXME: mouseup?
					if ( /* text was dragged and dropped */ false ) {
						//console.log( 'MAJOR CHANGE' );
					} else {
						//console.log( 'MINOR CHANGE' );
					}
					break;
				default:
					//console.log( 'MAJOR CHANGE' );
					break;
			}
		
		}
	};
	
	/* Internal Functions */
	
	context.fn = {
		'addButton': function( options ) {
			// Ensure that buttons and tabs are visible
			context.$controls.show();
		},
		'addView': function( options ) {
			// Adds a tab
			function addTab( options ) {
				// Ensure that buttons and tabs are visible
				context.$controls.show();
				// Return the newly appended tab
				return $( '<div></div>' )
					.attr( 'rel', 'wikiEditor-ui-view-' + options.name )
					.addClass( context.view == options.name ? 'current' : null )
					.append( $( '<a></a>' )
						.attr( 'href', '#' )
						.click( function( event ) {
							context.$ui.find( '.wikiEditor-ui-view' ).hide();
							context.$ui.find( '.' + $(this).parent().attr( 'rel' ) ).show();
							context.$tabs.find( 'div' ).removeClass( 'current' );
							$(this).parent().addClass( 'current' );
							$(this).blur();
							if ( 'init' in options && typeof options.init == 'function' ) {
								options.init( context );
							}
							event.preventDefault();
						} )
						.text( $.wikiEditor.autoMsg( options, 'title' ) )
					)
					.appendTo( context.$tabs );
			}
			// Automatically add the previously not-needed wikitext tab
			if ( !context.$tabs.children().size() ) {
				addTab( { 'name': 'wikitext', 'titleMsg': 'wikieditor-wikitext-tab' } );
			}
			// Add the tab for the view we were actually asked to add
			addTab( options );
			// Return newly appended view
			return $( '<div></div>' )
				.addClass( 'wikiEditor-ui-view wikiEditor-ui-view-' + options.name )
				.hide()
				.appendTo( context.$ui );
		},
		/**
		 * Set up the magic iframe
		 */
		'setup': function() {
			// Setup the iframe with a basic document
			context.$iframe[0].contentWindow.document.open();
			context.$iframe[0].contentWindow.document.write(
				'<html><head><title>wikiEditor</title><script>var context = window.parent.jQuery.wikiEditor.instances[' + context.instance + '].data( "wikiEditor-context" ); window.parent.jQuery( document ).bind( "keydown keypress keyup mousedown mouseup cut paste", { "context": context }, context.evt.change );</script></head><body style="margin:0;padding:0;width:100%;height:100%;white-space:pre-wrap;font-family:monospace"></body></html>'
			);
			context.$iframe[0].contentWindow.document.close();
			// Turn the document's design mode on
			context.$iframe[0].contentWindow.document.designMode = 'on';
			// Get a reference to the content area of the iframe 
			context.$content = $( context.$iframe[0].contentWindow.document.body );
			if ( $( 'body' ).is( '.rtl' ) ) {
				context.$content.addClass( 'rtl' ).attr( 'dir', 'rtl' );
			}
			
			/* Magic IFRAME Activation */
			
			// Activate the iframe, encoding the content of the textarea and copying it to the content of the iframe
			context.$textarea.attr( 'disabled', true );
			// We need to properly escape any HTML entities like &amp;, &lt; and &gt; so they end up as visible
			// characters rather than actual HTML tags in the code editor container.
			context.$content.text( context.$textarea.val() );
			context.$textarea.hide();
			context.$iframe.show();
		},
		/**
		 * Checks whether the magic iframe is properly set up
		 */
		'isSetup': function() {
			return context.$content != undefined && context.$content[0].innerHTML != undefined;
		},
		/**
		 * Gets the complete contents of the iframe
		 */
		'getContents': function() {
			// We use .html() instead of .text() so HTML entities are handled right
			// Setting the HTML of the textarea doesn't work on all browsers, use a dummy <div> instead
			return $( '<div />' )
				.html( context.$content.html().replace( /\<br\>/g, "\n" ) )
				.text();
		},
		'setContents': function( options ) {
			context.$content.text( options.contents );
			return context.$textarea;
		},
		/**
		 * Gets the currently selected text in the content
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 */
		'getSelection': function() {
			var retval;
			if ( context.$iframe[0].contentWindow.getSelection ) {
				retval = context.$iframe[0].contentWindow.getSelection();
			} else if ( context.$iframe[0].contentWindow.selection ) { // should come last; Opera!
				retval = context.$iframe[0].contentWindow.selection.createRange();
			}
			if ( retval.text ) {
				retval = retval.text;
			} else if ( retval.toString ) {
				retval = retval.toString();
			}
			return retval;
		},
		/**
		 * Inserts text at the begining and end of a text selection, optionally inserting text at the caret when
		 * selection is empty.
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 */
		'encapsulateSelection': function( options ) {
			// TODO: IE
			// TODO: respect options.ownline
			var selText = $(this).textSelection( 'getSelection' );
			var selectAfter = false;
			var pre = options.pre, post = options.post;
			if ( !selText ) {
				selText = options.peri;
				selectAfter = true;
			} else if ( options.replace ) {
				selText = options.peri;
			} else if ( selText.charAt( selText.length - 1 ) == ' ' ) {
				// Exclude ending space char
				// FIXME: Why?
				selText = selText.substring( 0, selText.length - 1 );
				post += ' ';
			}
			var range = context.$iframe[0].contentWindow.getSelection().getRangeAt( 0 );
			if ( options.ownline ) {
				// TODO: This'll probably break with syntax highlighting
				if ( range.startOffset != 0 )
					pre  = "\n" + options.pre;
				// TODO: Will this still work with syntax highlighting?
				if ( range.endContainer == range.commonAncestorContainer )
					post += "\n";
			}
			var insertText = pre + selText + post;
			var insertLines = insertText.split( "\n" );
			range.extractContents();
			// Insert the contents one line at a time
			// insertNode() inserts at the beginning, so this has
			// to happen in reverse order
			var lastNode;
			for ( var i = insertLines.length - 1; i >= 0; i-- ) {
				range.insertNode( document.createTextNode( insertLines[i] ) );
				if ( i > 0 ) {
					lastNode = range.insertNode( document.createElement( 'br' ) );
				}
			}
			if ( lastNode ) {
				context.fn.scrollToTop( lastNode );
			}
			// Trigger the encapsulateSelection event (this might need to get named something else/done differently)
			context.$content.trigger( 'encapsulateSelection', [ pre, peri, post, ownline, replace ] );
			return context.$textarea;
		},
		/**
		 * Gets the position (in resolution of bytes not nessecarily characters) in a textarea
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 */
		'getCaretPosition': function( options ) {
			// FIXME: Character-based functions aren't useful for the magic iframe
			// ...
			//reurn character position
		},
		/**
		 * Sets the selection of the content
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 * 
		 * @param start Character offset of selection start
		 * @param end Character offset of selection end
		 */
		'setSelection': function( options ) {
			// FIXME: Character-based functions aren't useful for the magic iframe
			// ...
		},
		/**
		 * Scroll a textarea to the current cursor position. You can set the cursor position with setSelection()
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 */
		'scrollToCaretPosition': function( options ) {
			// ...
			//context.$textarea.trigger( 'scrollToPosition' );
		},
		/**
		 * Scroll an element to the top of the iframe
		 * DO NOT CALL THESE DIRECTLY, use .textSelection( 'functionname', options ) instead
		 * 
		 * @param $element jQuery object containing an element in the iframe
		 * @param force If true, scroll the element even if it's already visible
		 */
		'scrollToTop': function( $element, force ) {
			var body = context.$content.closest( 'body' );
			var y = $element.offset().top - context.$content.offset().top;
			if ( force || y < body.scrollTop() || y > body.scrollTop() + body.height() )
				body.scrollTop( y );
			$element.trigger( 'scrollToTop' );
		}
	};
	
	/* Base UI Construction */
	
	// Encapsulate the textarea with some containers for layout
	context.$textarea
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-view wikiEditor-ui-view-wikitext' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-left' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
	
	context.$ui = context.$textarea.parent().parent().parent().parent().parent();
	context.$wikitext = context.$textarea.parent().parent().parent().parent();
	// Add in tab and button containers
	context.$wikitext
		.before(
			$( '<div></div>' ).addClass( 'wikiEditor-ui-controls' )
				.append( $( '<div></div>' ).addClass( 'wikiEditor-ui-tabs' ) )
				.append( $( '<div></div>' ).addClass( 'wikiEditor-ui-buttons' ) )
		)
		.before( $( '<div style="clear:both;"></div>' ) );
	context.$controls = context.$ui.find( '.wikiEditor-ui-buttons' ).hide();
	context.$buttons = context.$ui.find( '.wikiEditor-ui-buttons' );
	context.$tabs = context.$ui.find( '.wikiEditor-ui-tabs' );
	// Clear all floating after the UI
	context.$ui.after( $( '<div style="clear:both;"></div>' ) );
	// Attach a right container
	context.$wikitext.append( $( '<div></div>' ).addClass( 'wikiEditor-ui-right' ) );
	// Attach a top container to the left pane
	context.$wikitext.find( '.wikiEditor-ui-left' ).prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ) );
	// Setup the intial view
	context.view = 'wikitext';
	
	/* Magic IFRAME Construction */
	
	// Create an iframe in place of the text area
	context.$iframe = $( '<iframe></iframe>' )
		.attr( 'frameborder', 0 )
		.css( {
			'backgroundColor': 'white',
			'width': '100%',
			'height': context.$textarea.height(),
			'display': 'none',
			'overflow-y': 'scroll',
			'overflow-x': 'hidden',
		})
		.insertAfter( context.$textarea );
	
	/*
	 * For whatever strange reason, this code needs to be within a timeout or it doesn't work - it seems to be that
	 * the DOM manipulation to add the iframe happens asynchronously and this code that depends on it actually being
	 * finished doesn't function on the right reference.
	 * FIXME: The fact that this calls a function that's defined below is ugly
	 */
	setTimeout( function() { context.fn.setup(); }, 1 );
	
	// Attach a submit handler to the form so that when the form is submitted the content of the iframe gets decoded and
	// copied over to the textarea
	context.$textarea.closest( 'form' ).submit( function() {
		context.$textarea.attr( 'disabled', false );
		context.$textarea.val( context.$textarea.textSelection( 'getContents' ) );
	} );
}

// If there was a configuration passed, it's assumed to be for the addModule API call
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	// If the iframe construction isn't ready yet, defer the call
	if ( context.fn.isSetup() )
		context.api.addModule( context, arguments[0] );
	else {
		var args = arguments;
		setTimeout( function() {
			context.api.addModule( context, args[0] );
 		}, 2 );
	}
} else {
	// Since javascript gives arguments as an object, we need to convert them so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			// If the iframe construction isn't ready yet, defer the call
			if ( context.fn.isSetup() )
				context.api[call]( context, arguments[0] == undefined ? {} : arguments[0] );
			else {
				var args = arguments;
				setTimeout( function() {
					context.api[call]( context, args[0] == undefined ? {} : args[0] );
				}, 2 );
			}
		}
	}
}

// Store the context for next time, and support chaining
return $(this).data( 'wikiEditor-context', context );

}; } )( jQuery );
/**
 * Extend the RegExp object with an escaping function
 * From http://simonwillison.net/2006/Jan/20/escape/
 */
RegExp.escape = function( s ) { return s.replace(/([.*+?^${}()|\/\\[\]])/g, '\\$1'); };

/**
 * Dialog Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.dialogs = {

/**
 * API accessible functions
 */
api: {
	addDialog: function( context, data ) {
		$.wikiEditor.modules.dialogs.fn.create( context, data )
	},
	openDialog: function( context, module ) {
		if ( module in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[module].id ).dialog( 'open' );
		}
	},
	closeDialog: function( context, data ) {
		if ( module in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[module].id ).dialog( 'close' );
		}
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a dialog module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		// Add modules
		for ( module in config ) {
			$.wikiEditor.modules.dialogs.modules[module] = config[module];
		}
		// Build out modules immediately
		mvJsLoader.doLoad( ['$j.ui', '$j.ui.dialog', '$j.ui.draggable', '$j.ui.resizable' ], function() {
			for ( module in $.wikiEditor.modules.dialogs.modules ) {
				var module = $.wikiEditor.modules.dialogs.modules[module];
				// Only create the dialog if it doesn't exist yet
				if ( $( '#' + module.id ).size() == 0 ) {
					var configuration = module.dialog;
					// Add some stuff to configuration
					configuration.bgiframe = true;
					configuration.autoOpen = false;
					configuration.modal = true;
					configuration.title = $.wikiEditor.autoMsg( module, 'title' );
					// Transform messages in keys
					// Stupid JS won't let us do stuff like
					// foo = { gM ('bar'): baz }
					configuration.newButtons = {};
					for ( msg in configuration.buttons )
						configuration.newButtons[gM( msg )] = configuration.buttons[msg];
					configuration.buttons = configuration.newButtons;
					// Create the dialog <div>
					var dialogDiv = $( '<div /> ' )
						.attr( 'id', module.id )
						.html( module.html )
						.data( 'context', context )
						.appendTo( $( 'body' ) )
						.each( module.init )
						.dialog( configuration );
					if ( !( 'resizeme' in module ) || module.resizeme )
						dialogDiv
							.bind( 'dialogopen', $.wikiEditor.modules.dialogs.fn.resize )
							.find( '.ui-tabs' ).bind( 'tabsshow', function() {
								$(this).closest( '.ui-dialog-content' ).each(
									$.wikiEditor.modules.dialogs.fn.resize );
							});
					
					// Add tabindexes to dialog form elements
					// Find the highest tabindex in use
					var maxTI = 0;
					$j( '[tabindex]' ).each( function() {
						var ti = parseInt( $j(this).attr( 'tabindex' ) );
						if ( ti > maxTI )
							maxTI = ti;
					});
					
					var tabIndex = maxTI + 1;
					$j( '.ui-dialog input, .ui-dialog button' )
						.not( '[tabindex]' )
						.each( function() {
							$j(this).attr( 'tabindex', tabIndex++ );
						});
				}
			}
		});
	},
	
	/**
	 * Resize a dialog so its contents fit
	 *
	 * Usage: dialog.each( resize ); or dialog.bind( 'blah', resize );
	 * NOTE: This function assumes $j.ui.dialog has already been loaded
	 */
	resize: function() {
		var wrapper = $(this).closest( '.ui-dialog' );
		var oldWidth = wrapper.width();
		// Make sure elements don't wrapped so we get an accurate idea
		// of whether they really fit. Also temporarily show hidden
		// elements.
		
		// Work around jQuery bug where <div style="display:inline;" />
		// inside a dialog is both :visible and :hidden 
		var oldHidden = $(this).find( '*' ).not( ':visible' );
		
		// Save the style attributes of the hidden elements to restore
		// them later. Calling hide() after show() messes up for
		// elements hidden with a class
		oldHidden.each( function() {
			$(this).data( 'oldstyle', $(this).attr( 'style' ) );
		});
		oldHidden.show();
		var oldWS = $(this).css( 'white-space' );
		$(this).css( 'white-space', 'nowrap' );
		
		if ( wrapper.width() <= $(this).get(0).scrollWidth ) {
			var thisWidth = $(this).data( 'thisWidth' ) ? $(this).data( 'thisWidth' ) : 0;
			thisWidth = Math.max( $(this).get(0).scrollWidth, thisWidth );
			$(this).width( thisWidth );
			$(this).data( 'thisWidth', thisWidth );
			
			var wrapperWidth = $(this).data( 'wrapperWidth' ) ? $(this).data( 'wrapperWidth' ) : 0;
			wrapperWidth = Math.max( wrapper.get(0).scrollWidth, wrapperWidth );
			wrapper.width( wrapperWidth );
			$(this).data( 'wrapperWidth', wrapperWidth );
			
			$(this).dialog( { 'width': wrapper.width() } );
			wrapper.css( 'left',
				parseInt( wrapper.css( 'left' ) ) -
				( wrapper.width() - oldWidth ) / 2 );
		}
		
		$(this).css( 'white-space', oldWS );
		oldHidden.each( function() {
			$(this).attr( 'style', $(this).data( 'oldstyle' ) );
		});
		
	}
},
modules: {},
quickDialog: function( body, settings ) {
	$( '<div />' )
		.text( body )
		.appendTo( $( 'body' ) )
		.dialog( $.extend( {
			bgiframe: true,
			modal: true
		}, settings ) )
		.dialog( 'open' );
}

}; } ) ( jQuery );
/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Internally used event handlers
 */
evt: {
	change: function( event ) {
		/*
		 * Triggered on any of the following events, with the intent on detecting if something was added, deleted or
		 * replaced due to user action.
		 * 
		 * The following conditions are indicative that one or more divisions need to be re-scanned/marked:
		 * 		Keypress while something is highlighted
		 * 		Cut
		 * 		Paste
		 * 		Drag+drop selected text
		 * The following conditions are indicative that special handlers need to be consulted to properly parse content
		 * 		Keypress with any of the following characters
		 * 			}	Template or Table handler
		 * 			>	Tag handler
		 * 			]	Link handler
		 * The following conditions are indicative that divisions might be being made which would need encapsulation
		 * 		Keypress with any of the following characters
		 * 			=	Heading
		 * 			#	Ordered
		 * 			*	Unordered
		 * 			;	Definition
		 * 			:	Definition
		 */
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a highlight module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// hook $.wikiEditor.modules.highlight.evt.change to context.evt.change
	},
	divide: function( context ) {
		/*
		 * We need to add some markup to the iframe content to encapsulate divisions
		 */
	},
	isolate: function( context ) {
		/*
		 * A change just occured, and we need to know which sections were affected
		 */
		return []; // array of sections?
	},
	strip: function( context, division ) {
		return $( '<div />' ).html( division.html().replace( /\<br[^\>]*\>/g, "\n" ) ).text();
	},
	scan: function( context, division ) {
		/*
		 * We need to look over some text and find interesting areas, then return the positions of those areas as tokens
		 */
		return []; // array of tokens?
	},
	mark: function( context, division, tokens ) {
		/*
		 * We need to markup some text based on some tokens
		 */
	}
}

}; })( jQuery );/* Preview module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.preview = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a preview module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		if ( 'preview' in context.modules ) {
			return;
		}
		context.modules.preview = {
			'previousText': null,
		};
		context.$preview = context.fn.addView( {
			'name': 'preview',
			'titleMsg': 'wikieditor-preview-tab',
			'init': function( context ) {
				// Gets the latest copy of the wikitext
				var wikitext = context.fn.getContents();
				// Aborts when nothing has changed since the last preview
				if ( context.modules.preview.previousText == wikitext ) {
					return;
				}
				context.$preview.find( '.wikiEditor-preview-contents' ).empty();
				context.$preview.find( '.wikiEditor-preview-loading' ).show();
				$.post(
					wgScriptPath + '/api.php',
					{
						'action': 'parse',
						'title': wgPageName,
						'text': wikitext,
						'prop': 'text',
						'pst': '',
						'format': 'json'
					},
					function( data ) {
						if (
							data.parse == undefined ||
							data.parse.text == undefined ||
							data.parse.text['*'] == undefined
						) {
							return;
						}
						context.modules.preview.previousText = wikitext;
						context.$preview.find( '.wikiEditor-preview-loading' ).hide();
						context.$preview.find( '.wikiEditor-preview-contents' ).html( data.parse.text['*'] );
					},
					'json'
				);
			}
		} );
		var loadingMsg = gM( 'wikieditor-preview-loading' );
		context.$preview
			.append( $( '<div />' )
				.addClass( 'wikiEditor-preview-loading' )
				.append( $( '<img />' )
					.addClass( 'wikiEditor-preview-spinner' )
					.attr( {
						'src': $.wikiEditor.imgPath + 'dialogs/loading.gif',
						'valign': 'absmiddle',
						'alt': loadingMsg,
						'title': loadingMsg
					} )
				)
				.append(
					$( '<span></span>' ).text( loadingMsg )
				)
			)
			.append( $( '<div />' )
				.addClass( 'wikiEditor-preview-contents' )
			);
	}
}

}; } )( jQuery );/* Publish module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.publish = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a publish module within a wikiEditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		/*
		 * This code was moved here from the old version of the preview module - it needs more work to run on it's own,
		 * and many of the names of classes and messages need to be updated
		 * 
		mvJsLoader.doLoad( [ '$j.ui' ], function() {
			// Build the dialog behind the Publish button
			var dialogID = 'wikiEditor-' + context.instance + '-savedialog';
			$.wikiEditor.modules.dialogs.fn.create(
				context,
				{
					previewsave: {
						id: dialogID,
						titleMsg: 'wikieditor-preview-savedialog-title',
						html: '\
							<div class="wikiEditor-savedialog-copywarn"></div>\
							<div class="wikiEditor-savedialog-editoptions">\
								<form>\
									<label for="wikiEditor-' + context.instance + '-savedialog-summary"\
										rel="wikieditor-preview-savedialog-summary"></label>\
									<br />\
									<input type="text" id="wikiEditor-' + context.instance + '-savedialog-summary"\
										style="width: 100%;" />\
									<br />\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-savedialog-minor" />\
									<label for="wikiEditor-' + context.instance + '-savedialog-minor"\
										rel="wikieditor-preview-savedialog-minor"></label>\
									<br />\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-savedialog-watch" />\
									<label for="wikiEditor-' + context.instance + '-savedialog-watch"\
										rel="wikieditor-preview-savedialog-watch"></label>\
								</form>\
							</div>',
						init: function() {
							$(this).find( '[rel]' ).each( function() {
								$(this).text( gM( $(this).attr( 'rel' ) ) );
							});
							$(this).find( '.wikiEditor-savedialog-copywarn' )
								.html( $( '#editpage-copywarn' ).html() );
							
							if ( $( '#wpMinoredit' ).size() == 0 )
								$( '#wikiEditor-' + context.instance + '-savedialog-minor' ).hide();
							else if ( $( '#wpMinoredit' ).is( ':checked' ) )
								$( '#wikiEditor-' + context.instance + '-savedialog-minor' )
									.attr( 'checked', 'checked' );
							if ( $( '#wpWatchthis' ).size() == 0 )
								$( '#wikiEditor-' + context.instance + '-savedialog-watch' ).hide();
							else if ( $( '#wpWatchthis' ).is( ':checked' ) )
								$( '#wikiEditor-' + context.instance + '-savedialog-watch' )
									.attr( 'checked', 'checked' );
							
							$(this).find( 'form' ).submit( function( e ) {
								$(this).closest( '.ui-dialog' ).find( 'button:first' ).click();
								e.preventDefault();
							});
						},
						dialog: {
							buttons: {
								'wikieditor-preview-savedialog-publish': function() {
									var minorChecked = $( '#wikiEditor-' + context.instance +
										'-savedialog-minor' ).is( ':checked' ) ?
											'checked' : '';
									var watchChecked = $( '#wikiEditor-' + context.instance +
										'-savedialog-watch' ).is( ':checked' ) ?
											'checked' : '';
									$( '#wpMinoredit' ).attr( 'checked', minorChecked );
									$( '#wpWatchthis' ).attr( 'checked', watchChecked );
									$( '#wpSummary' ).val( $j( '#wikiEditor-' + context.instance +
										'-savedialog-summary' ).val() );
									$( '#editform' ).submit();
								},
								'wikieditor-preview-savedialog-goback': function() {
									$(this).dialog( 'close' );
								}
							},
							open: function() {
								$( '#wikiEditor-' + context.instance + '-savedialog-summary' ).focus();
							},
							width: 500
						},
						resizeme: false
					}
				}
			);
			// Paranoia: initialize context.modules before running
			// tabs() and binding event handlers
			context.modules.preview = {
				'editTab': editTab,
				'previewTab': previewTab,
				'tabList': tabList,
				'saveDialog': $( '#' + dialogID ),
				'prevText': null
			};
			tabList
				.append( $( '<ul />' )
					.append( $( '<li />' )
						.append( $( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-edit' )
							.text( gM( 'wikieditor-preview-tab-edit' ) )
						)
					)
					.append( $( '<li />' )
						.append( $( '<a />' )
							.attr( 'href', '#wikiEditor-' + context.instance + '-tab-preview' )
							.text( gM( 'wikieditor-preview-tab-preview' ) )
						)
					)
					.append( $( '<button />' )
						.text( gM( 'wikieditor-preview-button-publish' ) )
						.click( function() {
							context.modules.preview.saveDialog.dialog( 'open' );
							return false;
						})
					)
					// These have to go in reverse because they're floated right
					.append( $( '<button />' )
						.text( gM( 'wikieditor-preview-button-cancel' ) )
					)
				)
				.append( editTab )
				.append( previewTab );
			
			tabList
				.bind( 'tabsshow', function() {
					if ( context.modules.preview.previewTab.is( ':visible' ) )
						$.wikiEditor.modules.preview.fn.showPreview( context );
				})
				.tabs();
			
			// Remove the ui-widget class from the tabs div,
			// causes NTOC mispositioning
			// FIXME: Find out which CSS rule is causing this
			// and override it
			// FIXME: Don't use jQuery UI tabs, implement our own tabs
			tabList.closest( '.ui-tabs' ).removeClass( 'ui-widget' );
		} );
		*/
	}
}

}; } )( jQuery );/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Default width of table of contents
 */
defaultWidth: '166px',
/**
 * Minimum width to allow resizing to before collapsing the table of contents
 * Only used if resizing and collapsing is enabled
 */
minimumWidth: '70px',
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a table of contents module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		if ( '$toc' in context.modules ) {
			return;
		}
		
		var height = context.$ui.find( '.wikiEditor-ui-left' ).height();
		context.modules.$toc = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.data( 'context', context );
		context.$ui.find( '.wikiEditor-ui-right' )
			.css( 'width', $.wikiEditor.modules.toc.defaultWidth )
			.append( context.modules.$toc );
		context.modules.$toc.height(
			context.$ui.find( '.wikiEditor-ui-left' ).height()
		);
		context.$ui.find( '.wikiEditor-ui-left' )
			.css( 'marginRight', "-" + $.wikiEditor.modules.toc.defaultWidth )
			.children()
			.css( 'marginRight', $.wikiEditor.modules.toc.defaultWidth );
		
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context, config );
		context.$content.parent()
			.delayedBind( 250, 'mouseup scrollToTop keyup change',
				function() {
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.build( context );
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			)
			.blur( function( event ) {
				var context = event.data.context;
				context.$textarea.delayedBindCancel( 250, 'mouseup scrollToTop keyup change' );
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
		
	},
	
	unhighlight: function( context ) {
		context.modules.$toc.find( 'div' ).removeClass( 'current' );
	},
	/**
	 * Highlight the section the cursor is currently within
	 * 
	 * @param {Object} context
	 */
	update: function( context ) {
		$.wikiEditor.modules.toc.fn.unhighlight( context );
		var position = context.$textarea.textSelection( 'getCaretPosition' );
		var section = 0;
		if ( context.data.outline.length > 0 ) {
			// If the caret is before the first heading, you must be in section
			// 0, and there is no need to look any farther - otherwise check
			// that the caret is before each section, and when it's not, we now
			// know what section it is in
			if ( !( position < context.data.outline[0].position - 1 ) ) {
				while (
					section < context.data.outline.length && context.data.outline[section].position - 1 < position
				) {
					section++;
				}
				section = Math.max( 0, section );
			}
			var sectionLink = context.modules.$toc.find( 'div.section-' + section );
			sectionLink.addClass( 'current' );
			
			// Scroll the highlighted link into view if necessary
			var relTop = sectionLink.offset().top - context.modules.$toc.offset().top;
			var scrollTop = context.modules.$toc.scrollTop();
			var divHeight = context.modules.$toc.height();
			var sectionHeight = sectionLink.height();
			if ( relTop < 0 )
				// Scroll up
				context.modules.$toc.scrollTop( scrollTop + relTop );
			else if ( relTop + sectionHeight > divHeight )
				// Scroll down
				context.modules.$toc.scrollTop( scrollTop + relTop + sectionHeight - divHeight );
		}
	},
	
	/**
	 * Collapse the contents module
	 * 
	 * @param {Object} event Event object with context as data
	 */
	collapse: function( event ) {
		var $this = $( this ), context = $this.data( 'context' ),
			pT = $this.parent().position().top - 1;
		$this.parent()
			.css( 'position', 'absolute' )
			.css( { 'left': 'auto', 'right': 0, 'top': pT } )
			.fadeOut( 'fast', function() { 
				$( this ).hide()
				.css( 'width', '1px' );
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).fadeIn( 'fast' );
			 } )
			.prev()
			.animate( { 'marginRight': '-1px' }, 'fast', function() { $( this ).css( 'marginRight', 0 ); } )
			.children()
			.animate( { 'marginRight': '1px' }, 'fast',  function() { $( this ).css( 'marginRight', 0 ); } );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width', 0 );
		return false;
	},
	
	/**
	 * Expand the contents module
	 * 
	 * @param {Object} event Event object with context as data
	 */
	expand: function( event) {
		var $this = $( this ), 
			context = $this.data( 'context' ), 
			openWidth = context.modules.$toc.data( 'openWidth' );
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		$this.parent()
			.show()
			.animate( { 'width' : openWidth }, 'fast', function() { 
				context.$content.trigger( 'mouseup' );
				$( this ).css( { 'position': 'relative', 'right': 'auto', 'top': 'auto' } );
			 } )
			.prev()
			.animate( { 'marginRight': ( parseFloat( openWidth ) * -1 ) }, 'fast' )
			.children()
			.animate( { 'marginRight': openWidth }, 'fast' );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width', 
			context.modules.$toc.data( 'openWidth' ) );
		return false;
	},
	/**
	 * Builds table of contents
	 * 
	 * @param {Object} context
	 */
	build: function( context ) {
		/**
		 * Builds a structured outline from flat outline
		 * 
		 * @param {Object} outline Array of objects with level fields
		 */
		function buildStructure( outline, offset, level ) {
			if ( offset == undefined ) offset = 0;
			if ( level == undefined ) level = 1;
			var sections = [];
			for ( var i = offset; i < outline.length; i++ ) {
				if ( outline[i].nLevel == level ) {
					var sub = buildStructure( outline, i + 1, level + 1 );
					if ( sub.length ) {
						outline[i].sections = sub;
					}
					sections[sections.length] = outline[i];
				} else if ( outline[i].nLevel < level ) {
					break;
				}
			}
			return sections;
		}
		/**
		 * Bulds unordered list HTML object from structured outline
		 * 
		 * @param {Object} structure Structured outline
		 */
		function buildList( structure ) {
			var list = $( '<ul />' );
			for ( i in structure ) {
				var div = $( '<div />' )
					.addClass( 'section-' + structure[i].index )
					.data( 'wrapper', structure[i].wrapper )
					.mousedown( function( event ) {
						context.fn.scrollToTop( $(this).data( 'wrapper' ) );
						if ( typeof $.trackAction != 'undefined' )
							$.trackAction( 'ntoc.heading' );
						event.preventDefault();
					} )
					.text( structure[i].text );
				if ( structure[i].text == '' )
					div.html( '&nbsp;' );
				var item = $( '<li />' ).append( div );
				if ( structure[i].sections !== undefined ) {
					item.append( buildList( structure[i].sections ) );
				}
				list.append( item );
			}
			return list;
		}
		/**
		 * Builds controls for collapsing and expanding the TOC
		 * 
		 */
		function buildCollapseControls( ) {
			var $collapseControl = $( '<div />' ), $expandControl = $( '<div />' );
			$collapseControl
				.addClass( 'tab' )
				.addClass( 'tab-toc' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() { 
					context.modules.$toc.trigger( 'collapse.wikiEditor-toc' ); return false; 
				} )
				.find( 'a' )
				.text( gM( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() { 
					context.modules.$toc.trigger( 'expand.wikiEditor-toc' ); return false; 
				} )
				.hide()
				.find( 'a' )
				.text( gM( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.$toc );
			context.$ui.find( '.wikiEditor-ui-left .wikiEditor-ui-top' ).append( $expandControl );
		}
		/**
		 * Initializes resizing controls on the TOC and sets the width of 
		 * the TOC based on it's previous state
		 * 
		 */
		function buildResizeControls( ) {
			context.$ui
				.data( 'resizableDone', true )
				.find( '.wikiEditor-ui-right' )
				.data( 'wikiEditor-ui-left', context.$ui.find( '.wikiEditor-ui-left' ) )
				.resizable( { handles: 'w,e', preventPositionLeftChange: true, minWidth: 50,
					start: function( e, ui ) {
						var $this = $( this );
						// Toss a transparent cover over our iframe
						$( '<div />' )
							.addClass( 'wikiEditor-ui-resize-mask' )
							.css( { 'position': 'absolute', 'z-index': 2, 'left': 0, 'top': 0, 'bottom': 0, 'right': 0 } )
							.appendTo( context.$ui.find( '.wikiEditor-ui-left' ) );
						$this.resizable( 'option', 'maxWidth', $this.parent().width() - 450 );
					},
					resize: function( e, ui ) {
						// for some odd reason, ui.size.width seems a step ahead of what the *actual* width of 
						// the resizable is
						$( this ).css( { 'width': ui.size.width, 'top': 'auto', 'height': 'auto' } )
							.data( 'wikiEditor-ui-left' ).css( 'marginRight', ( -1 * ui.size.width ) )
							.children().css( 'marginRight', ui.size.width );
					},
					stop: function ( e, ui ) {
						context.$ui.find( '.wikiEditor-ui-resize-mask' ).remove();
						context.$content.trigger( 'mouseup' );
						if( ui.size.width < parseFloat( $.wikiEditor.modules.toc.minimumWidth ) ) {
							context.modules.$toc.trigger( 'collapse' ); 
						} else {
							context.modules.$toc.data( 'openWidth', ui.size.width );
							$.cookie( 'wikiEditor-' + context.instance + '-toc-width', ui.size.width );
						}
					}
				});
			// Convert our east resize handle into a secondary west resize handle
			context.$ui.find( '.ui-resizable-e' )
				.removeClass( 'ui-resizable-e' )
				.addClass( 'ui-resizable-w' )
				.addClass( 'wikiEditor-ui-toc-resize-grip' );
			// Bind collapse and expand event handlers to the TOC
			context.modules.$toc
				.bind( 'collapse.wikiEditor-toc', $.wikiEditor.modules.toc.fn.collapse )
				.bind( 'expand.wikiEditor-toc', $.wikiEditor.modules.toc.fn.expand  );
			context.modules.$toc.data( 'openWidth', $.wikiEditor.modules.toc.defaultWidth );
			// If the toc-width cookie is set, reset the widths based upon that
			if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) == 0 ) {
				context.modules.$toc.trigger( 'collapse.wikiEditor-toc', { data: context } );
			} else if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) > 0 ) {
				var initialWidth = $.cookie( 'wikiEditor-' + context.instance + '-toc-width' );
				if( initialWidth < parseFloat( $.wikiEditor.modules.toc.minimumWidth ) )
					initialWidth = parseFloat( $.wikiEditor.modules.toc.minimumWidth ) + 1;
				context.modules.$toc.data( 'openWidth', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-right' )
					.css( 'width', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-left' )
					.css( 'marginRight', ( parseFloat( initialWidth ) * -1 ) + 'px' )
					.children()
					.css( 'marginRight', initialWidth + 'px' );
			}
		}
		
		// Build outline from wikitext
		var outline = [];
		
		// Traverse all text nodes in context.$content
		var h = 0;
		context.$content.contents().add( context.$content.find( '.wikiEditor-toc-header' ) ).each( function() {
			if ( this.nodeName != '#text' && !$(this).is( '.wikiEditor-toc-header' ) )
				return;
			var text = this.nodeValue;
			if ( $(this).is( '.wikiEditor-toc-header' ) )
				text = $(this).html();
			
			var match = text.match( /^(={1,6})(.*?)\1\s*$/ );
			if ( !match ) {
				if ( $(this).is( '.wikiEditor-toc-header' ) )
					// Header has become invalid
					// Remove the class but keep the <div> intact
					// to prevent issues with Firefox
					$(this).removeClass( 'wikiEditor-toc-header' );
				return;
			}
			// Wrap the header in a <div>, unless it's already wrapped
			var div;
			if ( $(this).is( '.wikiEditor-toc-header' ) )
				div = $(this);
			else {
				div = $j( '<div />' )
					.text( text )
					.css( 'display', 'inline' )
					.addClass( 'wikiEditor-toc-header' );
				$(this).replaceWith( div );
			}
			outline[h] = { 'text': match[2], 'wrapper': div, 'level': match[1].length, 'index': h + 1 };
			h++;
		});
		// Normalize heading levels for list creation
		// This is based on Linker::generateTOC(), so it should behave like the
		// TOC on rendered articles does - which is considdered to be correct
		// at this point in time.
		var lastLevel = 0;
		var nLevel = 0;
		for ( var i = 0; i < outline.length; i++ ) {
			if ( outline[i].level > lastLevel ) {
				nLevel++;
			}
			else if ( outline[i].level < lastLevel ) {
				nLevel -= Math.max( 1, lastLevel - outline[i].level );
			}
			if ( nLevel <= 0 ) {
				nLevel = 1;
			}
			outline[i].nLevel = nLevel;
			lastLevel = outline[i].level;
		}
		// Recursively build the structure and add special item for
		// section 0, if needed
		var structure = buildStructure( outline );
		if ( $( 'input[name=wpSection]' ).val() == '' ) {
			structure.unshift( { 'text': wgPageName.replace(/_/g, ' '), 'level': 1, 'index': 0,
				'wrapper': context.$content } );
		}
		context.modules.$toc.html( buildList( structure ) );
		
		if ( wgNavigableTOCResizable && !context.$ui.data( 'resizableDone' ) ) {
			buildResizeControls();
			buildCollapseControls();
		}
		context.modules.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

};

/*
 * Extending resizable to allow west resizing without altering the left position attribute
 */
$.ui.plugin.add( "resizable", "preventPositionLeftChange", {
	resize: function( event, ui ) {
		$( this ).data( "resizable" ).position.left = 0;
	}
} );
 
} ) ( jQuery );
/**
 * Toolbar module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.toolbar = {

/**
 * API accessible functions
 */
api : {
	addToToolbar : function( context, data ) {
		for ( type in data ) {
			switch ( type ) {
				case 'sections':
					var $sections = context.modules.$toolbar
					.find( 'div.sections' );
					var $tabs = context.modules.$toolbar
					.find( 'div.tabs' );
					for ( section in data[type] ) {
						if ( section == 'main' ) {
							// Section
							context.modules.$toolbar
							.prepend(
								$.wikiEditor.modules.toolbar.fn.buildSection(
									context, section, data[type][section]
								)
							);
							continue;
						}
						// Section
						$sections.append(
							$.wikiEditor.modules.toolbar.fn.buildSection( context, section, data[type][section] )
						);
						// Tab
						$tabs.append(
							$.wikiEditor.modules.toolbar.fn.buildTab( context, section, data[type][section] )
						);
						// Update visibility of section
						$section = $sections.find( '.section:visible' );
						if ( $section.size() ) {
							$sections.animate( { 'height': $section.outerHeight() }, 'fast' );
						}
					}
					break;
				case 'groups':
					if ( ! ( 'section' in data ) ) {
						continue;
					}
					var $section = context.modules.$toolbar
					.find( 'div[rel=' + data.section + '].section' );
					for ( group in data[type] ) {
						// Group
						$section
						.append( $.wikiEditor.modules.toolbar.fn.buildGroup( context, group, data[type][group] ) );
					}
					break;
				case 'tools':
					if ( ! ( 'section' in data && 'group' in data ) ) {
						continue;
					}
					var $group = context.modules.$toolbar
					.find( 'div[rel=' + data.section + '].section ' + 'div[rel=' + data.group + '].group' );
					for ( tool in data[type] ) {
						// Tool
						$group.append( $.wikiEditor.modules.toolbar.fn.buildTool( context, tool,data[type][tool] ) );
					}
					break;
				case 'pages':
					if ( ! ( 'section' in data ) ) {
						continue;
					}
					var $pages = context.modules.$toolbar
					.find( 'div[rel=' + data.section + '].section .pages' );
					var $index = context.modules.$toolbar
					.find( 'div[rel=' + data.section + '].section .index' );
					for ( page in data[type] ) {
						// Page
						$pages.append( $.wikiEditor.modules.toolbar.fn.buildPage( context, page, data[type][page] ) );
						// Index
						$index.append(
							$.wikiEditor.modules.toolbar.fn.buildBookmark( context, page, data[type][page] )
						);
					}
					$.wikiEditor.modules.toolbar.fn.updateBookletSelection( context, page, $pages, $index );
					break;
				case 'rows':
					if ( ! ( 'section' in data && 'page' in data ) ) {
						continue;
					}
					var $table = context.modules.$toolbar.find(
						'div[rel=' + data.section + '].section ' + 'div[rel=' + data.page + '].page table'
					);
					for ( row in data[type] ) {
						// Row
						$table.append( $.wikiEditor.modules.toolbar.fn.buildRow( context, data[type][row] ) );
					}
					break;
				case 'characters':
					if ( ! ( 'section' in data && 'page' in data ) ) {
						continue;
					}
					$characters = context.modules.$toolbar.find(
						'div[rel=' + data.section + '].section ' + 'div[rel=' + data.page + '].page div'
					);
					var actions = $characters.data( 'actions' );
					for ( character in data[type] ) {
						// Character
						$characters
						.append(
							$( $.wikiEditor.modules.toolbar.fn.buildCharacter( data[type][character], actions ) )
								.click( function() {
									$.wikiEditor.modules.toolbar.fn.doAction( $(this).parent().data( 'context' ),
									$(this).parent().data( 'actions' )[$(this).attr( 'rel' )] );
									return false;
								} )
						);
					}
					break;
				default: break;
			}
		}
	},
	removeFromToolbar : function( context, data ) {
		js_log("f:removeFromToolbar");
		if ( typeof data.section == 'string' ) {
			// Section
			var tab = 'div.tabs span[rel=' + data.section + '].tab';
			var target = 'div[rel=' + data.section + '].section';
			if ( typeof data.group == 'string' ) {
				// Toolbar group
				target += ' div[rel=' + data.group + '].group';
				if ( typeof data.tool == 'string' ) {
					// Tool
					target += ' div[rel=' + data.tool + '].tool';
				}
			} else if ( typeof data.page == 'string' ) {
				// Booklet page
				var index = target + ' div.index div[rel=' + data.page + ']';
				target += ' div.pages div[rel=' + data.page + '].page';
				if ( typeof data.character == 'string' ) {
					// Character
					target += ' a[rel=' + data.character + ']';
				} else if ( typeof data.row == 'number' ) {
					// Table row
					target += ' table tr:not(:has(th)):eq(' + data.row + ')';
				} else {
					// Just a page, remove the index too!
					context.modules.$toolbar.find( index ).remove();
					$.wikiEditor.modules.toolbar.fn.updateBookletSelection(
						context,
						null,
						context.modules.$toolbar.find( target ),
						context.modules.$toolbar.find( index )
					);
				}
			} else {
				// Just a section, remove the tab too!
				context.modules.$toolbar.find( tab ).remove();
			}
			js_log('target is: ' + target);
			context.modules.$toolbar.find( target ).remove();
		}
	}
},
/**
 * Internally used functions
 */
fn : {
	/**
	 * Creates a toolbar module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create : function( context, config ) {
		if ( '$toolbar' in context.modules ) {
			return;
		}
		context.modules.$toolbar = $( '<div />' )
			.addClass( 'wikiEditor-ui-toolbar' )
			.attr( 'id', 'wikiEditor-ui-toolbar' );
		$.wikiEditor.modules.toolbar.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-top' ).append( context.modules.$toolbar );
	},
	/**
	 * Performs an operation based on parameters
	 * 
	 * @param {Object} context
	 * @param {Object} action
	 * @param {Object} source
	 */
	doAction : function( context, action, source ) {
		// Verify that this has been called from a source that's within the toolbar
		// 'trackAction' defined in click tracking
		if ($.trackAction != undefined && source.closest( '.wikiEditor-ui-toolbar' ).size() ) {
			// Build a unique id for this action by tracking the parent rel attributes up to the toolbar level
			var rels = [];
			var step = source;
			var i = 0;
			while ( !step.hasClass( 'wikiEditor-ui-toolbar' ) ) {
				if ( i > 25 ) {
					break;
				}
				i++;
				var rel = step.attr( 'rel' );
				if ( rel ) {
					rels.push( step.attr( 'rel' ) );
				}
				step = step.parent();
			}
			rels.reverse();
			var id = rels.join( '.' );
			$.trackAction(id);
		}
		switch ( action.type ) {
			case 'replace':
			case 'encapsulate':
				var parts = { 'pre' : '', 'peri' : '', 'post' : '' };
				for ( part in parts ) {
					if ( part + 'Msg' in action.options ) {
						parts[part] = gM( action.options[part + 'Msg'], ( action.options[part] || null ) );
					} else {
						parts[part] = ( action.options[part] || '' )
					}
				}
				if ( 'regex' in action.options && 'regexReplace' in action.options ) {
					var selection = context.$textarea.textSelection( 'getSelection' );
					if ( selection != '' && selection.match( action.options.regex ) ) {
						parts.peri = selection.replace( action.options.regex,
							action.options.regexReplace );
						parts.pre = parts.post = '';
					}
				}
				context.$textarea.textSelection( 'encapsulateSelection', $.extend( action.options,
					parts, { 'replace': action.type == 'replace' } ) );
				break;
			case 'callback':
				if ( typeof action.execute == 'function' ) {
					action.execute( context );
				}
				break;
			case 'dialog':
				context.$textarea.wikiEditor( 'openDialog', action.module );
				break;
			default: break;
		}
	},
	buildGroup : function( context, id, group ) {
		var $group = $( '<div />' ).attr( { 'class' : 'group group-' + id, 'rel' : id } );
		var label = $.wikiEditor.autoMsg( group, 'label' );
		if ( label ) {
			$group.append( '<div class="label">' + label + '</div>' )
		}

		var empty = true;
		if ( 'tools' in group ) {
			for ( tool in group.tools ) {
				var tool =  $.wikiEditor.modules.toolbar.fn.buildTool( context, tool, group.tools[tool] );
				if ( tool ) {
					empty = false;
					$group.append( tool );
				}
			}
		}
		return empty ? null : $group;
	},
	buildTool : function( context, id, tool ) {
		if ( 'filters' in tool ) {
			for ( filter in tool.filters ) {
				if ( $( tool.filters[filter] ).size() == 0 ) {
					return null;
				}
			}
		}
		var label = $.wikiEditor.autoMsg( tool, 'label' );
		switch ( tool.type ) {
			case 'button':
				var src = tool.icon + '?' + wgWikiEditorIconVersion;
				if ( src.indexOf( 'http://' ) !== 0 && src.indexOf( 'https://' ) !== 0 ) {
					src = $.wikiEditor.imgPath + 'toolbar/' + src;
				}
				$button = $( '<img />' ).attr( {
					'src' : src,
					'width' : 22,
					'height' : 22,
					'alt' : label,
					'title' : label,
					'rel' : id,
					'class' : 'tool tool-button'
				} );
				if ( 'action' in tool ) {
					$button
						.data( 'action', tool.action )
						.data( 'context', context )
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).data( 'context' ), $(this).data( 'action' ), $(this)
							);
							return false;
						} );
				}
				return $button;
			case 'select':
				var $select = $( '<div />' )
					.attr( { 'rel' : id, 'class' : 'tool tool-select' } )
					.click( function() {
						var $options = $(this).find( '.options' );
						$options.animate( { 'opacity': 'toggle' }, 'fast' );
					} );
				$options = $( '<div />' ).addClass( 'options' );
				if ( 'list' in tool ) {
					for ( option in tool.list ) {
						var optionLabel = $.wikiEditor.autoMsg( tool.list[option], 'label' );
						$options.append(
							$( '<a />' )
								.data( 'action', tool.list[option].action )
								.data( 'context', context )
								.click( function() {
									$.wikiEditor.modules.toolbar.fn.doAction(
										$(this).data( 'context' ), $(this).data( 'action' ), $(this)
									);
								} )
								.text( optionLabel )
								.addClass( 'option' )
								.attr( 'rel', option )
						);
					}
				}
				$select.append( $( '<div />' ).addClass( 'menu' ).append( $options ) );
				$select.append( $( '<div />' ).addClass( 'label' ).text( label ) );
				return $select;
			default:
				return null;
		}
	},
	buildBookmark : function( context, id, page ) {
		var label = $.wikiEditor.autoMsg( page,
		'label' );
		return $( '<div />' )
			.text( label )
			.attr( 'rel', id )
			.data( 'context', context )
			.bind( 'mousedown', function() {
				$(this).parent().parent().find( '.page' ).hide();
				$(this).parent().parent().find( '.page-' + $(this).attr( 'rel' ) ).show();
				$(this).siblings().removeClass( 'current' );
				$(this).addClass( 'current' );
				var section = $(this).parent().parent().attr( 'rel' );
				
				//click tracking
				if($.trackAction != undefined){
					$.trackAction(section + '.' + $(this).attr('rel'));
				}
				
				$.cookie(
					'wikiEditor-' + $(this).data( 'context' ).instance + '-booklet-' + section + '-page',
					$(this).attr( 'rel' )
				);
			} );
	},
	buildPage : function( context, id, page ) {
		var $page = $( '<div />' ).attr( {
			'class' : 'page page-' + id,
			'rel' : id
		} );
		switch ( page.layout ) {
			case 'table':
				$page.addClass( 'page-table' );
				var html =
					'<table cellpadding=0 cellspacing=0 ' + 'border=0 width="100%" class="table table-"' + id + '">';
				if ( 'headings' in page ) {
					html += $.wikiEditor.modules.toolbar.fn.buildHeading( context, page.headings )
				}
				if ( 'rows' in page ) {
					for ( row in page.rows ) {
						html += $.wikiEditor.modules.toolbar.fn.buildRow( context, page.rows[row] )
					}
				}
				$page.html( html );
				break;
			case 'characters':
				$page.addClass( 'page-characters' );
				$characters = $( '<div />' ).data( 'context', context ).data( 'actions', {} );
				var actions = $characters.data( 'actions' );
				if ( 'language' in page ) {
					$characters.attr( 'lang', page.language );
				}
				if ( 'direction' in page ) {
					$characters.attr( 'dir', page.direction );
				}
				if ( 'characters' in page ) {
					var html = '';
					for ( character in page.characters ) {
						html += $.wikiEditor.modules.toolbar.fn.buildCharacter( page.characters[character], actions );
					}
					$characters
						.html( html )
						.children()
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).parent().data( 'context' ),
								$(this).parent().data( 'actions' )[$(this).attr( 'rel' )],
								$(this)
							);
							return false;
						} );
				}
				$page.append( $characters );
				break;
		}
		return $page;
	},
	buildHeading : function( context, headings ) {
		var html = '<tr>';
		for ( heading in headings ) {
			html += '<th>' + $.wikiEditor.autoMsg( headings[heading], ['html', 'text'] ) + '</th>';
		}
		return html;
	},
	buildRow : function( context, row ) {
		var html = '<tr>';
		for ( cell in row ) {
			html += '<td class="cell cell-' + cell + '" valign="top"><span>' +
				$.wikiEditor.autoMsg( row[cell], ['html', 'text'] ) + '</span></td>';
		}
		html += '</tr>';
		return html;
	},
	buildCharacter : function( character, actions ) {
		if ( typeof character == 'string' ) {
			character = {
				'label' : character,
				'action' : {
					'type' : 'encapsulate',
					'options' : {
						'pre' : character
					}
				}
			};
		} else if ( 0 in character && 1 in character ) {
			character = {
				'label' : character[0],
				'action' : {
					'type' : 'encapsulate',
					'options' : {
						'pre' : character[1]
					}
				}
			};
		}
		if ( 'action' in character && 'label' in character ) {
			actions[character.label] = character.action;
			return '<a rel="' + character.label + '" href="#">' + character.label + '</a>';
		}
	},
	buildTab : function( context, id, section ) {
		var selected = $.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		return $( '<span />' )
			.attr( { 'class' : 'tab tab-' + id, 'rel' : id } )
			.append(
				$( '<a />' )
					.addClass( selected == id ? 'current' : null )
					.attr( 'href', '#' )
					.text( $.wikiEditor.autoMsg( section, 'label' ) )
					.data( 'context', context )
					.bind( 'mouseup', function( e ) {
						$(this).blur();
					} )
					.bind( 'click', function( e ) {
						var $sections = $(this).data( 'context' ).$ui.find( '.sections' );
						var $section =
							$(this).data( 'context' ).$ui.find( '.section-' + $(this).parent().attr( 'rel' ) );
						var show = $section.css( 'display' ) == 'none';
						$previousSections = $section.parent().find( '.section:visible' );
						var dH = $previousSections.outerHeight();
						$previousSections.css( 'position', 'absolute' );
						$previousSections.fadeOut( 'fast', function() { $(this).css( 'position', 'relative' ); } );
						$(this).parent().parent().find( 'a' ).removeClass( 'current' );
						$sections.css('overflow', 'hidden');
						if ( show ) {
							$section.fadeIn( 'fast' );
							dH = $section.outerHeight() - dH;
							if ( context.modules.$toc ) {
								context.modules.$toc.animate({'height': "+="+dH}, $section.outerHeight() * 2);
							}
							$sections.animate( { 'height': $section.outerHeight() }, $section.outerHeight() * 2, function() { 
								$(this).css('overflow', 'visible').css('height', 'auto'); 
							} );
							$(this).addClass( 'current' );
						} else {
							$sections.css('height', $section.outerHeight() )
								.animate( { 'height': 0 }, $section.outerHeight() * 2, function() { 
									$(this).css('overflow', 'visible'); 
								} );
							if ( context.modules.$toc ) {
								context.modules.$toc.animate({'height': "-="+$section.outerHeight()}, $section.outerHeight() * 2);
							}
						}
						// Click tracking
						if($.trackAction != undefined){
							$.trackAction($section.attr('rel') + '.' + ( show ? 'show': 'hide' )  );
						}
						//
						$.cookie(
							'wikiEditor-' + $(this).data( 'context' ).instance + '-toolbar-section',
							show ? $section.attr( 'rel' ) : null
						);
						return false;
					} )
			);
	},
	buildSection : function( context, id, section ) {
		context.$textarea.trigger( 'wikiEditor-toolbar-buildSection-' + id, [section] );
		var selected = $.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		var $section;
		switch ( section.type ) {
			case 'toolbar':
				var $section = $( '<div />' ).attr( { 'class' : 'toolbar section section-' + id, 'rel' : id } );
				if ( 'groups' in section ) {
					for ( group in section.groups ) {
						$section.append(
							$.wikiEditor.modules.toolbar.fn.buildGroup( context, group, section.groups[group] )
						);
					}
				}
				break;
			case 'booklet':
				var $pages = $( '<div />' ).addClass( 'pages' );
				var $index = $( '<div />' ).addClass( 'index' );
				if ( 'pages' in section ) {
					for ( page in section.pages ) {
						$pages.append(
							$.wikiEditor.modules.toolbar.fn.buildPage( context, page, section.pages[page] )
						);
						$index.append(
							$.wikiEditor.modules.toolbar.fn.buildBookmark( context, page, section.pages[page] )
						);
					}
				}
				$section = $( '<div />' ).attr( { 'class' : 'booklet section section-' + id, 'rel' : id } )
					.append( $index )
					.append( $pages );
				$.wikiEditor.modules.toolbar.fn.updateBookletSelection( context, page, $pages, $index );
				break;
		}
		if ( $section !== null && id !== 'main' ) {
			var show = selected == id;
			$section.css( 'display', show ? 'block' : 'none' );
		}
		return $section;
	},
	updateBookletSelection : function( context, id, $pages, $index ) {
		var cookie = 'wikiEditor-' + context.instance + '-booklet-' + id + '-page';
		var selected = $.cookie( cookie );
		var $selectedIndex = $index.find( '*[rel=' + selected + ']' );
		if ( $selectedIndex.size() == 0 ) {
			selected = $index.children().eq( 0 ).attr( 'rel' );
			$.cookie( cookie, selected );
		}
		$pages.children().hide();
		$pages.find( '*[rel=' + selected + ']' ).show();
		$index.children().removeClass( 'current' );
		$selectedIndex.addClass( 'current' );
	},
	build : function( context, config ) {
		var $tabs = $( '<div />' ).addClass( 'tabs' ).appendTo( context.modules.$toolbar );
		var $sections = $( '<div />' ).addClass( 'sections' ).appendTo( context.modules.$toolbar );
		context.modules.$toolbar.append( $( '<div />' ).css( 'clear', 'both' ) );
		var sectionQueue = [];
		for ( section in config ) {
			if ( section == 'main' ) {
				context.modules.$toolbar.prepend(
					$.wikiEditor.modules.toolbar.fn.buildSection( context, section, config[section] )
				);
			} else {
				sectionQueue.push( {
					'$sections' : $sections,
					'context' : context,
					'id' : section,
					'config' : config[section]
				} );
				$tabs.append( $.wikiEditor.modules.toolbar.fn.buildTab( context, section, config[section] ) );
			}
		}
		$.eachAsync( sectionQueue, {
			'bulk' : 0,
			'end' : function() {
				// HACK: Opera doesn't seem to want to redraw after these bits
				// are added to the DOM, so we can just FORCE it!
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', 'relative' );
			},
			'loop' : function( i, s ) {
				s.$sections.append( $.wikiEditor.modules.toolbar.fn.buildSection( s.context, s.id, s.config ) );
				var $section = s.$sections.find( '.section:visible' );
				if ( $section.size() ) {
					$sections.animate( { 'height': $section.outerHeight() }, $section.outerHeight() * 2, function( ) {
						if ( context.modules.$toc ) {
							context.modules.$toc.height(
								context.$ui.find( '.wikiEditor-ui-left' )
									.outerHeight() - context.$ui.find( '.tab-toc' ).outerHeight()
							)
						}
					} );
				}
			}
		} );
	}
}

}; } )( jQuery );
