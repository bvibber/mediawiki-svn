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
				'end': undefined, // Position to end selection at. Defaults to start
				'startContainer': undefined, // Element to start selection in (iframe only)
				'endContainer': undefined, // Element to end selection in (iframe only). Defaults to startContainer
			}, options );
			if ( options.end === undefined )
				options.end = options.start;
			if ( options.endContainer == undefined )
				options.endContainer = options.startContainer;
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
 * This plugin provides a way to build a wiki-text editing user interface around a textarea.
 * 
 * @example To intialize without any modules:
 * 		$j( 'div#edittoolbar' ).wikiEditor();
 * 
 * @example To initialize with one or more modules, or to add modules after it's already been initialized:
 * 		$j( 'textarea#wpTextbox1' ).wikiEditor( 'addModule', 'toolbar', { ... config ... } );
 * 
 */
( function( $ ) {

/**
 * Global static object for wikiEditor that provides generally useful functionality to all modules and contexts.
 */
$.wikiEditor = {
	/**
	 * For each module that is loaded, static code shared by all instances is loaded into this object organized by
	 * module name. The existance of a module in this object only indicates the module is available. To check if a
	 * module is in use by a specific context check the context.modules object.
	 */
	'modules': {},
	/**
	 * In some cases like with the iframe's HTML file, it's convienent to have a lookup table of all instances of the
	 * WikiEditor. Each context contains an instance field which contains a key that corrosponds to a reference to the
	 * textarea which the WikiEditor was build around. This way, by passing a simple integer you can provide a way back
	 * to a specific context.
	 */
	'instances': [],
	/**
	 * For each browser name, an array of conditions that must be met are supplied in [operaton, value]-form where
	 * operation is a string containing a JavaScript compatible binary operator and value is either a number to be
	 * compared with $.browser.versionNumber or a string to be compared with $.browser.version. If a browser is not
	 * specifically mentioned, we just assume things will work.
	 */
	'browsers': {
		// Left-to-right languages
		'ltr': {
			// The toolbar layout is broken in IE6
			'msie': [['>=', 7]],
		    // jQuery UI appears to be broken in FF 2.0 - 2.0.0.4
			'firefox': [
				['>=', 2], ['!=', '2.0'], ['!=', '2.0.0.1'], ['!=', '2.0.0.2'], ['!=', '2.0.0.3'], ['!=', '2.0.0.4']
			],
			// Text selection bugs galore - this may be a different situation with the new iframe-based solution
			'opera': [['>=', 9.6]],
			// This should be checked again, but the usage of Safari 3.0 and lower is so small it's not a priority
			'safari': [['>=', 3.1]]
		},
		// Right-to-left languages
		'rtl': {
			// The toolbar layout is broken in IE 7 in RTL mode, and IE6 in any mode
			'msie': [['>=', 8]],
		    // jQuery UI appears to be broken in FF 2.0 - 2.0.0.4
			'firefox': [
				['>=', 2], ['!=', '2.0'], ['!=', '2.0.0.1'], ['!=', '2.0.0.2'], ['!=', '2.0.0.3'], ['!=', '2.0.0.4']
			],
			// Text selection bugs galore - this may be a different situation with the new iframe-based solution
			'opera': [['>=', 9.6]],
			// This should be checked again, but the usage of Safari 3.0 and lower is so small it's not a priority
			'safari': [['>=', 3.1]]
		}
	},
	/**
	 * Path to images - this is a bit messy, and it would need to change if this code (and images) gets moved into the
	 * core - or anywhere for that matter...
	 */
	'imgPath' : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/',
	/**
	 * Checks the current browser against the browsers object to determine if the browser has been black-listed or not.
	 * Because these rules are often very complex, the object contains configurable operators and can check against
	 * either the browser version number or string. This process also involves checking if the current browser is amung
	 * those which we have configured as compatible or not. If the browser was not configured as comptible we just go on
	 * assuming things will work - the argument here is to prevent the need to update the code when a new browser comes
	 * to market. The assumption here is that any new browser will be built on an existing engine or be otherwise so
	 * similar to another existing browser that things actually do work as expected. The merrits of this argument, which
	 * is essentially to blacklist rather than whitelist are debateable, but at this point we've decided it's the more
	 * "open-web" way to go.
	 */
	'isSupported': function() {
		// Check for and make use of a cached return value
		if ( typeof $.wikiEditor.supported != 'undefined' ) {
			return $.wikiEditor.supported;
		}
		// Check if we have any compatiblity information on-hand for the current browser
		if ( !( $.browser.name in $.wikiEditor.browsers[$( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'] ) ) {
			// Assume good faith :) 
			return $.wikiEditor.supported = true;
		}
		// Check over each browser condition to determine if we are running in a compatible client
		var browser = $.wikiEditor.browsers[$( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'][$.browser.name];
		for ( condition in browser ) {
			var op = browser[condition][0];
			var val = browser[condition][1];
			if ( typeof val == 'string' ) {
				if ( !( eval( '$.browser.version' + op + '"' + val + '"' ) ) ) {
					return $.wikiEditor.supported = false;
				}
			} else if ( typeof val == 'number' ) {
				if ( !( eval( '$.browser.versionNumber' + op + val ) ) ) {
					return $.wikiEditor.supported = false;
				}
			}
		}
		// Return and also cache the return value - this will be checked somewhat often
		return $.wikiEditor.supported = true;
	},
	/**
	 * Provides a way to extract messages from objects. Wraps the gM function from js2stopgap.js, which will be changing
	 * in the very near future, so let's keep and eye on this. It's also possible that this function will just be moved
	 * to the global mw object all together.
	 * 
	 * @param object Object to extract messages from
	 * @param property String of name of property which contains the message. This should be the base name of the
	 * property, which means that in the case of the object { this: 'that', fooMsg: 'bar' }, passing property as 'this'
	 * would return the raw text 'that', while passing property as 'foo' would return the internationalized message
	 * with the key 'bar'.
	 */
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
	/**
	 * Provieds a way to extract a property of an object in a certain language, falling back on the property keyed as
	 * 'default'. If such key doesn't exist, the object itself is considered the actual value, which should ideally
	 * be the case so that you may use a string or object of any number of strings keyed by language with a default.
	 * 
	 * @param object Object to extract property from
	 * @param lang Language code, defaults to wgUserLanguage
	 */
	'autoLang': function( object, lang ) {
		return object[lang || wgUserLanguage] || object['default'] || object;
	},
	/**
	 * Provieds a way to extract the path of an icon in a certain language, automatically appending a version number for
	 * caching purposes and prepending an image path when icon paths are relative.
	 * 
	 * @param icon Icon object from e.g. toolbar config
	 * @param path Default icon path, defaults to $.wikiEditor.imgPath
	 * @param lang Language code, defaults to wgUserLanguage
	 */
	'autoIcon': function( icon, path, lang ) {
		var src = $.wikiEditor.autoLang( icon, lang );
		path = path || $.wikiEditor.imgPath;
		// Prepend path if src is not absolute
		if ( src.substr( 0, 7 ) != 'http://' && src.substr( 0, 8 ) != 'https://' && src[0] != '/' ) {
			src = path + src;
		}
		return src + '?' + wgWikiEditorIconVersion;
	}
};

/**
 * jQuery plugin that provides a way to initialize a wikiEditor instance on a textarea.
 */
$.fn.wikiEditor = function() {

// Skip any further work when running in browsers that are unsupported
if ( !$j.wikiEditor.isSupported() ) {
	return $(this);
}

/* Initialization */

// The wikiEditor context is stored in the element's data, so when this function gets called again we can pick up right
// where we left off
var context = $(this).data( 'wikiEditor-context' );

// On first call, we need to set things up, but on all following calls we can skip right to the API handling
if ( typeof context == 'undefined' ) {
	
	// Star filling the context with useful data - any jQuery selections, as usual should be named with a preceding $
	context = {
		// Reference to the textarea element which the wikiEditor is being built around
		'$textarea': $(this),
		// Container for any number of mutually exclusive views that are accessible by tabs
		'views': {},
		// Container for any number of module-specific data - only including data for modules in use on this context
		'modules': {},
		// General place to shouve bits of data into
		'data': {},
		// Unique numeric ID of this instance used both for looking up and differentiating instances of wikiEditor
		'instance': $.wikiEditor.instances.push( $(this) ) - 1
	};
	
	/*
	 * Externally Accessible API
	 * 
	 * These are available using calls to $j(selection).wikiEditor( call, data ) where selection is a jQuery selection
	 * of the textarea that the wikiEditor instance was built around.
	 */
	
	context.api = {
		/**
		 * Activates a module on a specific context with optional configuration data.
		 * 
		 * @param data Either a string of the name of a module to add without any additional configuration parameters,
		 * or an object with members keyed with module names and valued with configuration objects.
		 */
		'addModule': function( context, data ) {
			var modules = {};
			if ( typeof data == 'string' ) {
				modules[data] = {};
			} else if ( typeof data == 'object' ) {
				modules = data;
			}
			for ( module in modules ) {
				// Check for the existance of an available module with a matching name and a create function
				if ( typeof module == 'string' && module in $.wikiEditor.modules ) {
					// Extend the context's core API with this module's own API calls
					if ( 'api' in $.wikiEditor.modules[module] ) {
						for ( call in $.wikiEditor.modules[module].api ) {
							// Modules may not overwrite existing API functions - first come, first serve
							if ( !( call in context.api ) ) {
								context.api[call] = $.wikiEditor.modules[module].api[call];
							}
						}
					}
					// Activate the module on this context
					if ( 'fn' in $.wikiEditor.modules[module] && 'create' in $.wikiEditor.modules[module].fn ) {
						// Add a place for the module to put it's own stuff
						context.modules[module] = {};
						// Tell the module to create itself on the context
						$.wikiEditor.modules[module].fn.create( context, modules[module] );
					}
				}
			}
		}
	};
	
	/* 
	 * Event Handlers
	 * 
	 * These act as filters returning false if the event should be ignored or returning true if it should be passed
	 * on to all modules. This is also where we can attach some extra information to the events.
	 */
	
	context.evt = {
		/**
		 * Filters change events, which occur when the user interacts with the contents of the iframe. The goal of this
		 * function is to both classify the scope of changes as 'division' or 'character' and to prevent further
		 * processing of events which did not actually change the content of the iframe.
		 */
		'change': function( event ) {
			// Event filtering
			switch ( event.type ) {
				case 'keypress':
					if ( /* TODO: test if something interesting was deleted */ true ) {
						event.data.scope = 'keydown';
					} else {
						event.data.scope = 'character';
					}
					break;
				case 'mousedown': // FIXME: mouseup?
					if ( /* TODO: test if text was dragged and dropped */ true ) {
						event.data.scope = 'division';
					} else {
						return false;
					}
					break;
				default:
					event.data.scope = 'division';
					break;
			}
			return true;
		}
	};
	
	/* Internal Functions */
	
	context.fn = {
		/**
		 * Executes core event filters as well as event handlers provided by modules.
		 */
		'trigger': function( name, event ) {
			// Event is an optional argument, but from here on out, at least the type field should be dependable
			if ( typeof event == 'undefined' ) {
				event = { 'type': 'custom' };
			}
			// Ensure there's a place for extra information to live
			if ( typeof event.data == 'undefined' ) {
				event.data = {};
			}
			// Allow filtering to occur
			if ( name in context.evt ) {
				if ( !context.evt[name]( event ) ) {
					return false;
				}
			}
			// Pass the event around to all modules activated on this context
			for ( module in context.modules ) {
				if (
					module in $.wikiEditor.modules &&
					'evt' in $.wikiEditor.modules[module] &&
					name in $.wikiEditor.modules[module].evt
				) {
					$.wikiEditor.modules[module].evt[name]( context, event );
				}
			}
		},
		/**
		 * Adds a button to the UI
		 */
		'addButton': function( options ) {
			// Ensure that buttons and tabs are visible
			context.$controls.show();
			context.$buttons.show();
			return $( '<button />' )
				.text( $.wikiEditor.autoMsg( options, 'caption' ) )
				.click( options.action )
				.appendTo( context.$buttons );
		},
		/**
		 * Adds a view to the UI, which is accessed using a set of tabs. Views are mutually exclusive and by default a
		 * wikitext view will be present. Only when more than one view exists will the tabs will be visible.
		 */
		'addView': function( options ) {
			// Adds a tab
			function addTab( options ) {
				// Ensure that buttons and tabs are visible
				context.$controls.show();
				context.$tabs.show();
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
							return false;
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
		 * FIXME: This section is a bit of a "wonky" section given it's supposed to keep compatibility with the
		 * textSelection plugin, which works on character-based manipulations as opposed to the node-based manipulations
		 * we use for the iframe. It's debatable whether compatibility with this plugin is even being done well, or for
		 * that matter should be done at all.
		 */
		
		/**
		 * Gets the complete contents of the iframe (in plain text, not HTML)
		 */
		'getContents': function() {
			// FIXME: Evil ua-sniffing action!
			if ( $.browser.name == 'msie' ) {
				return context.$content.text();
			}
			// We use .html() instead of .text() so HTML entities are handled right
			// Setting the HTML of the textarea doesn't work on all browsers, use a dummy <div> instead
			
			
			//get rid of the noincludes when getting text
			var $dummyDiv = $( '<div />' ).html( context.$content.html().replace( /\<br\>/g, "\n" ) );
			$dummyDiv.find( ".wikieditor-noinclude" ).each( function() { $( this ).remove(); } );
			return $dummyDiv.text();
			
		},
		/**
		 * Sets the complete contents of the iframe (in plain text, not HTML; HTML passed will be converted to entities)
		 * FIXME: Passing in options like this is sort of akward - it appears to be a way to make this compatible with
		 * the textSelection plugin - is this needed?
		 */
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
			} else if ( context.$iframe[0].contentWindow.document.selection ) { // should come last; Opera!
				retval = context.$iframe[0].contentWindow.document.selection.createRange();
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
				if ( range.startOffset != 0 ) {
					pre  = "\n" + options.pre;
				}
				// TODO: Will this still work with syntax highlighting?
				if ( range.endContainer == range.commonAncestorContainer ) {
					post += "\n";
				}
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
			context.$content.trigger(
				'encapsulateSelection', [ pre, options.peri, post, options.ownline, options.replace ]
			);
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
		 * @param startContainer Element in iframe to start selection in
		 * @param endContainer Element in iframe to end selection in
		 */
		'setSelection': function( options ) {
			// TODO: IE
			var sel = context.$iframe[0].contentWindow.getSelection();
			var sc = options.startContainer, ec = options.endContainer;
			sc = sc.jquery ? sc[0] : sc;
			ec = ec.jquery ? ec[0] : ec;
			while ( sc.firstChild && sc.nodeName != '#text' ) {
				sc = sc.firstChild;
			}
			while ( ec.firstChild && ec.nodeName != '#text' ) {
				ec = ec.firstChild;
			}
			// TODO: Can this be done in one call? sel.addRange()?
			//sel.removeAllRanges();
			sel.extend( sc, options.start );
			//if ( sel.
			sel.collapseToStart();
			if ( options.end != options.start || sc != ec ) {
				sel.extend( ec, options.end );
			}
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
			var html = context.$content.closest( 'html' ),
				body = context.$content.closest( 'body' );
			var y = $element.offset().top - context.$content.offset().top;
			if ( force || y < html.scrollTop() || y < body.scrollTop() 
				|| y > html.scrollTop() + context.$iframe.height() 
				|| y > body.scrollTop() + context.$iframe.height() ) {
					html.scrollTop( y );
					body.scrollTop( y );
				}
			$element.trigger( 'scrollToTop' );
		},
		/**
		 * Get the first element before the selection matching a certain selector
		 * @param selector Selector to match. Defaults to '*'
		 * @param getAll If true, get all matching elements before the selection
		 */
		'beforeSelection': function( selector, getAll ) {
			if ( typeof selector == 'undefined' )
				selector = '*';
			var retval = [];
			var range = context.$iframe[0].contentWindow.getSelection().getRangeAt( 0 );
			// Start at the selection's start and traverse the DOM backwards
			var e = range.startContainer;
			//TODO continue
		}
		
		/**
		 * End of "wonky" textSelection "compatible" section that needs attention.
		 */
		
	};
	
	/*
	 * Base UI Construction
	 * 
	 * The UI is built from several containers, the outer-most being a div classed as "wikiEditor-ui". These containers
	 * provide a certain amount of "free" layout, but in some situations procedural layout is needed, which is performed
	 * as a response to the "resize" event.
	 */
	
	// Encapsulate the textarea with some containers for layout
	context.$textarea
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-view wikiEditor-ui-view-wikitext' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-left' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
		.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
	// Get references to some of the newly created containers
	context.$ui = context.$textarea.parent().parent().parent().parent().parent();
	context.$wikitext = context.$textarea.parent().parent().parent().parent();
	// Add in tab and button containers
	context.$wikitext
		.before(
			$( '<div></div>' ).addClass( 'wikiEditor-ui-controls' )
				.append( $( '<div></div>' ).addClass( 'wikiEditor-ui-tabs' ).hide() )
				.append( $( '<div></div>' ).addClass( 'wikiEditor-ui-buttons' ) )
		)
		.before( $( '<div style="clear:both;"></div>' ) );
	// Get references to some of the newly created containers
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
	// Trigger the "resize" event anytime the window is resized
	$( window ).resize( function( event ) { context.fn.trigger( 'resize', event ) } );
	// Create an iframe in place of the text area
	context.$iframe = $( '<iframe></iframe>' )
		.attr( {
			'frameborder': 0,
			'src': wgScriptPath + '/extensions/UsabilityInitiative/js/plugins/jquery.wikiEditor.html?' +
				'instance=' + context.instance + '&ts=' + ( new Date() ).getTime(),
			'id': 'wikiEditor-iframe-' + context.instance
		} )
		.css( {
			'backgroundColor': 'white',
			'width': '100%',
			'height': context.$textarea.height(),
			'display': 'none',
			'overflow-y': 'scroll',
			'overflow-x': 'hidden'
		} )
		.insertAfter( context.$textarea )
		.load( function() {
			// Turn the document's design mode on
			context.$iframe[0].contentWindow.document.designMode = 'on';
			// Get a reference to the content area of the iframe
			context.$content = $( context.$iframe[0].contentWindow.document.body );
			// We need to properly escape any HTML entities like &amp;, &lt; and &gt; so they end up as visible
			// characters rather than actual HTML tags in the code editor container.
			context.$content.append(
				context.$textarea.val().replace( /</g, '&lt;' ).replace( />/g, '&gt;' )
			);
			// Reflect direction of parent frame into child
			if ( $( 'body' ).is( '.rtl' ) ) {
				context.$content.addClass( 'rtl' ).attr( 'dir', 'rtl' );
			}
			// Activate the iframe, encoding the content of the textarea and copying it to the content of the iframe
			context.$textarea.attr( 'disabled', true );
			context.$textarea.hide();
			context.$iframe.show();
			// Let modules know we're ready to start working with the content
			context.fn.trigger( 'ready' );
		} );
	// Attach a submit handler to the form so that when the form is submitted the content of the iframe gets decoded and
	// copied over to the textarea
	context.$textarea.closest( 'form' ).submit( function() {
		context.$textarea.attr( 'disabled', false );
		context.$textarea.val( context.$textarea.textSelection( 'getContents' ) );
	} );
}

/* API Execution */

// Since javascript gives arguments as an object, we need to convert them so they can be used more easily
arguments = $.makeArray( arguments );
// There would need to be some arguments if the API is being called
if ( arguments.length > 0 ) {
	// Handle API calls
	var call = arguments.shift();
	if ( call in context.api ) {
		context.api[call]( context, typeof arguments[0] == 'undefined' ? {} : arguments[0] );
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
		mw.load( ['$j.ui', '$j.ui.dialog', '$j.ui.draggable', '$j.ui.resizable' ], function() {
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
					if ( !( 'resizeme' in module ) || module.resizeme ) {
						dialogDiv
							.bind( 'dialogopen', $.wikiEditor.modules.dialogs.fn.resize )
							.find( '.ui-tabs' ).bind( 'tabsshow', function() {
								$(this).closest( '.ui-dialog-content' ).each(
									$.wikiEditor.modules.dialogs.fn.resize );
							});
					}
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
		// Make sure elements don't wrapped so we get an accurate idea of whether they really fit. Also temporarily show
		// hidden elements. Work around jQuery bug where <div style="display:inline;" /> inside a dialog is both
		// :visible and :hidden
		var oldHidden = $(this).find( '*' ).not( ':visible' );
		// Save the style attributes of the hidden elements to restore them later. Calling hide() after show() messes up
		// for elements hidden with a class
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
			wrapper.css( 'left', parseInt( wrapper.css( 'left' ) ) - ( wrapper.width() - oldWidth ) / 2 );
		}
		$(this).css( 'white-space', oldWS );
		oldHidden.each( function() {
			$(this).attr( 'style', $(this).data( 'oldstyle' ) );
		});		
	}
},
// This stuff is just hanging here, perhaps we could come up with a better home for this stuff
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

}; } ) ( jQuery );/* Highlight module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * Configuration
 */
cfg: {
	'styleVersion': 2
},
/**
 * Internally used event handlers
 */
evt: {
	change: function( context, event ) {
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
		if ( event.data.scope == 'keydown' ) {
			$.wikiEditor.modules.highlight.fn.scan( context, "" );
			$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
		}
	},
	ready: function( context, event ) {
		// Add our CSS to the iframe
		// Style version for wikiEditor.highlight.css is here
		// FIXME: That's not ideal
		context.$content.parent().find( 'head' ).append( $j( '<link />' ).attr( {
			'rel': 'stylesheet',
			'type': 'text/css',
			'href': wgScriptPath + '/extensions/UsabilityInitiative/css/wikiEditor.highlight.css?' +
				$.wikiEditor.modules.highlight.cfg.styleVersion,
		} ) );
		// Highlight stuff for the first time
		$.wikiEditor.modules.highlight.fn.scan( context, "" );
		$.wikiEditor.modules.highlight.fn.mark( context, "", "" );
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a highlight module within a wikiEditor
	 * 
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// hook $.wikiEditor.modules.highlight.evt.change to context.evt.change
	},
	/**
	 * Divides text into divisions
	 */
	divide: function( context ) {
		/*
		 * We need to add some markup to the iframe content to encapsulate divisions
		 */
	},
	/**
	 * Isolates division which was affected by most recent change
	 */
	isolate: function( context ) {
		/*
		 * A change just occured, and we need to know which sections were affected
		 */
		return []; // array of sections?
	},
	/**
	 * Strips division of HTML
	 * 
	 * @param division
	 */
	strip: function( context, division ) {
		return $( '<div />' ).html( division.html().replace( /\<br[^\>]*\>/g, "\n" ) ).text();
	},
	/**
	 * Scans text division for tokens
	 * 
	 * @param division
	 */
	scan: function( context, division ) {
		/**
		 * Builds a Token object
		 * 
		 * @param offset
		 * @param label
		 */
		function Token( offset, label ) {
			this.offset = offset;
			this.label = label;
		}
		// Reset tokens
		var tokenArray = context.modules.highlight.tokenArray = [];
		// We need to look over some text and find interesting areas, then return the positions of those areas as tokens
		var text = context.fn.getContents();
		for ( module in $.wikiEditor.modules ) {
			if ( 'exp' in $.wikiEditor.modules[module] ) {
			   for ( var i = 0; i < $.wikiEditor.modules[module].exp.length; i++ ) {
					var regex = $.wikiEditor.modules[module].exp[i].regex;
					var label = $.wikiEditor.modules[module].exp[i].label;
					var markAfter = false;
					if ( typeof $.wikiEditor.modules[module].exp[i].markAfter != 'undefined' ) {
						markAfter = true;
					}
					match = text.match( regex );
					var oldOffset = 0;
					while ( match != null ) {
						var markOffset = 0;
						if ( markAfter ) {
							markOffset += match[0].length;
						}
						tokenArray.push(
							new Token( match.index + oldOffset + markOffset, label )
						);
						oldOffset += match.index + match[0].length;
						newSubstring = text.substring( oldOffset );
						match = newSubstring.match( regex );
					}
				}
			}
		}
		tokenArray.sort( function( a, b ) { return a.offset - b.offset; } );
		context.fn.trigger( 'scan' );
	},
	/**
	 * Marks up text with HTML
	 * 
	 * @param division
	 * @param tokens
	 */
	// FIXME: What do division and tokens do?
	mark: function( context, division, tokens ) {
		// Reset markers
		var markers = context.modules.highlight.markers = [];
		// Get all markers
		context.fn.trigger( 'mark' );
		markers.sort( function( a, b ) { return a.start - b.start || a.end - b.end; } );
		// Traverse the iframe DOM, inserting markers where they're needed. The loop traverses all leaf nodes in the
		// DOM, and uses DOM methods rather than jQuery because it has to work with text nodes and for performance.
		var pos = 0;
		var node = context.$content.get( 0 );
		var next = null;
		var i = 0; // index for markers[]
		var startNode = null;
		var depth = 0, nextDepth = 0, startDepth = null;
		// Find the leftmost leaf node in the tree
		while ( node.firstChild ) {
			node = node.firstChild;
			depth++;
		}
		while ( i < markers.length && node ) {
			// Find the next leaf node
			var p = node;
			nextDepth = depth;
			while ( p && !p.nextSibling ) {
				p = p.parentNode;
				nextDepth--;
			}
			p = p ? p.nextSibling : null;
			while ( p && p.firstChild ) {
				p = p.firstChild;
				nextDepth++;
			}
			next = p;
			if ( node.nodeName != '#text' ) {
				if ( node.nodeName == 'BR' ) {
					pos++;
				}
				// Skip this node
				node = next;
				depth = nextDepth;
				continue;
			}
			var newPos = pos + node.nodeValue.length;
			// We want to isolate each marker, so we may need to split textNodes
			// if a marker starts or end halfway one.
			if ( !startNode && markers[i].start >= pos && markers[i].start < newPos ) {
				// The next marker starts somewhere in this textNode
				if ( markers[i].start > pos ) {
					// Split off the prefix
					// This leaves the prefix in the current node and puts
					// the rest in a new node, which we immediately advance to
					node = node.splitText( markers[i].start - pos );
					pos = markers[i].start;
				}
				startNode = node;
				startDepth = depth;
			}
			// TODO: What happens when wrapping a zero-length string?
			// TODO: Detect that something is wrapped but shouldn't be any more and unwrap it
			if ( startNode && markers[i].end > pos && markers[i].end <= newPos ) {
				// The marker ends somewhere in this textNode
				if ( markers[i].end < newPos ) {
					// Split off the suffix - This puts the suffix in a new node and leaves the rest in the current
					// node. We have to make sure the split-off node will be visited correctly
					// node.nodeValue.length - ( newPos - markers[i].end )
					next = node.splitText( node.nodeValue.length - newPos + markers[i].end );
					newPos = markers[i].end;
				}
				// Now wrap everything between startNode and node (may be equal). First find the common ancestor of
				// startNode and node. ca1 and ca2 will be children of this common ancestor, such that ca1 is an
				// ancestor of startNode and ca2 of node. We also check that startNode and node are the leftmost and
				// rightmost leaves in the subtrees rooted at ca1 and ca2 respectively; if this is not the case, we
				// can't cleanly wrap things without misnesting and we silently fail.
				var ca1 = startNode, ca2 = node;
				// Correct for startNode and node possibly not having the same depth
				if ( startDepth > depth ) {
					for ( var j = 0; j < startDepth - depth && ca1; j++ ) {
						ca1 = ca1.parentNode;
					}
				}
				else if ( startDepth < depth ) {
					for ( var j = 0; j < depth - startDepth && ca2; j++ ) {
						ca2 = ca2.parentNode;
					}
				}
				while ( ca1 && ca2 && ca1.parentNode != ca2.parentNode ) {
					if ( ca1.parentNode.firstChild != ca1 || ca2.parentNode.lastChild != ca2 ) {
						// startNode and node are not the leftmost and rightmost leaves
						ca1 = ca2 = null;
					} else {
						ca1 = ca1.parentNode;
						ca2 = ca2.parentNode;
					}
				}
				if ( ca1 && ca2 && markers[i].needsWrap( ca1, ca2 ) ) {
					// We have to store things like .parentNode and .nextSibling because appendChild() changes these
					// properties
					var newNode = ca1.ownerDocument.createElement( 'div' );
					var commonAncestor = ca1.parentNode;
					var nextNode = ca2.nextSibling;
					// Append all nodes between ca1 and ca2 (inclusive) to newNode
					var n = ca1;
					while ( n != nextNode ) {
						var ns = n.nextSibling;
						newNode.appendChild( n );
						n = ns;
					}
					
					// Insert newNode in the right place
					if ( nextNode ) {
						commonAncestor.insertBefore( newNode, nextNode );
					} else {
						commonAncestor.appendChild( newNode );
					}
					
					// Allow the module adding this marker to manipulate it
					markers[i].afterWrap( newNode );
				}
				// Clear for next iteration
				startNode = null;
				startDepth = null;
				i++;
			}
			pos = newPos;
			node = next;
			depth = nextDepth;
		}
	}
}

}; })( jQuery );

/* Preview module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.preview = {

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
		if ( 'initialized' in context.modules.preview ) {
			return;
		}
		context.modules.preview = {
			'initialized': true,
			'previewText': null,
			'changesText': null
		};
		context.modules.preview.$preview = context.fn.addView( {
			'name': 'preview',
			'titleMsg': 'wikieditor-preview-tab',
			'init': function( context ) {
				// Gets the latest copy of the wikitext
				var wikitext = context.fn.getContents();
				// Aborts when nothing has changed since the last preview
				if ( context.modules.preview.previewText == wikitext ) {
					return;
				}
				context.modules.preview.$preview.find( '.wikiEditor-preview-contents' ).empty();
				context.modules.preview.$preview.find( '.wikiEditor-preview-loading' ).show();
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
							typeof data.parse == 'undefined' ||
							typeof data.parse.text == 'undefined' ||
							typeof data.parse.text['*'] == 'undefined'
						) {
							return;
						}
						context.modules.preview.previewText = wikitext;
						context.modules.preview.$preview.find( '.wikiEditor-preview-loading' ).hide();
						context.modules.preview.$preview.find( '.wikiEditor-preview-contents' )
							.html( data.parse.text['*'] )
							.find( 'a:not([href^=#])' ).click( function() { return false; } );
					},
					'json'
				);
			}
		} );
		
		context.$changesTab = context.fn.addView( {
			'name': 'changes',
			'titleMsg': 'wikieditor-preview-changes-tab',
			'init': function( context ) {
				// Gets the latest copy of the wikitext
				var wikitext = context.fn.getContents();
				// Aborts when nothing has changed since the last time
				if ( context.modules.preview.changesText == wikitext ) {
					return;
				}
				context.$changesTab.find( 'table.diff tbody' ).empty();
				context.$changesTab.find( '.wikiEditor-preview-loading' ).show();
				
				var postdata = {
					'action': 'query',
					'indexpageids': '',
					'prop': 'revisions',
					'titles': wgPageName,
					'rvdifftotext': wikitext,
					'rvprop': '',
					'format': 'json'
				};
				var section = $( '[name=wpSection]' ).val();
				if ( section != '' )
					postdata['rvsection'] = section;
				
				$.post( wgScriptPath + '/api.php', postdata, function( data ) {
						// Add diff CSS
						if ( $( 'link[href=' + stylepath + '/common/diff.css]' ).size() == 0 ) {
							$( 'head' ).append( $( '<link />' ).attr( {
								'rel': 'stylesheet',
								'type': 'text/css',
								'href': stylepath + '/common/diff.css'
							} ) );
						}
						try {
							var diff = data.query.pages[data.query.pageids[0]]
								.revisions[0].diff['*'];
							context.$changesTab.find( 'table.diff tbody' )
								.html( diff );
							context.$changesTab
								.find( '.wikiEditor-preview-loading' ).hide();
							context.modules.preview.changesText = wikitext;
						} catch (e) { } // "blah is undefined" error, ignore
					}, 'json'
				);
			}
		} );
		
		var loadingMsg = gM( 'wikieditor-preview-loading' );
		context.modules.preview.$preview
			.add( context.$changesTab )
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
		context.$changesTab.find( '.wikiEditor-preview-contents' )
			.html( '<table class="diff"><col class="diff-marker" /><col class="diff-content" />' +
				'<col class="diff-marker" /><col class="diff-content" /><tbody /></table>' );
	}
}

}; } )( jQuery );/* Publish module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.publish = {

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
		// Build the dialog behind the Publish button
		var dialogID = 'wikiEditor-' + context.instance + '-dialog';
		$.wikiEditor.modules.dialogs.fn.create(
			context,
			{
				previewsave: {
					id: dialogID,
					titleMsg: 'wikieditor-publish-dialog-title',
					html: '\
						<div class="wikiEditor-dialog-copywarn"></div>\
						<div class="wikiEditor-dialog-editoptions">\
							<form>\
								<label for="wikiEditor-' + context.instance + '-dialog-summary"\
									rel="wikieditor-publish-dialog-summary"></label>\
								<br />\
								<input type="text" id="wikiEditor-' + context.instance + '-dialog-summary"\
									style="width: 100%;" />\
								<br />\
								<input type="checkbox"\
									id="wikiEditor-' + context.instance + '-dialog-minor" />\
								<label for="wikiEditor-' + context.instance + '-dialog-minor"\
									rel="wikieditor-publish-dialog-minor"></label>\
								<br />\
								<input type="checkbox"\
									id="wikiEditor-' + context.instance + '-dialog-watch" />\
								<label for="wikiEditor-' + context.instance + '-dialog-watch"\
									rel="wikieditor-publish-dialog-watch"></label>\
							</form>\
						</div>',
					init: function() {
						$(this).find( '[rel]' ).each( function() {
							$(this).text( gM( $(this).attr( 'rel' ) ) );
						});
						$(this).find( '.wikiEditor-dialog-copywarn' )
							.html( $( '#editpage-copywarn' ).html() );
						
						if ( $( '#wpMinoredit' ).size() == 0 )
							$( '#wikiEditor-' + context.instance + '-dialog-minor' ).hide();
						else if ( $( '#wpMinoredit' ).is( ':checked' ) )
							$( '#wikiEditor-' + context.instance + '-dialog-minor' )
								.attr( 'checked', 'checked' );
						if ( $( '#wpWatchthis' ).size() == 0 )
							$( '#wikiEditor-' + context.instance + '-dialog-watch' ).hide();
						else if ( $( '#wpWatchthis' ).is( ':checked' ) )
							$( '#wikiEditor-' + context.instance + '-dialog-watch' )
								.attr( 'checked', 'checked' );
						
						$(this).find( 'form' ).submit( function( e ) {
							$(this).closest( '.ui-dialog' ).find( 'button:first' ).click();
							e.preventDefault();
						});
					},
					dialog: {
						buttons: {
							'wikieditor-publish-dialog-publish': function() {
								var minorChecked = $( '#wikiEditor-' + context.instance +
									'-dialog-minor' ).is( ':checked' ) ?
										'checked' : '';
								var watchChecked = $( '#wikiEditor-' + context.instance +
									'-dialog-watch' ).is( ':checked' ) ?
										'checked' : '';
								$( '#wpMinoredit' ).attr( 'checked', minorChecked );
								$( '#wpWatchthis' ).attr( 'checked', watchChecked );
								$( '#wpSummary' ).val( $j( '#wikiEditor-' + context.instance +
									'-dialog-summary' ).val() );
								$( '#editform' ).submit();
							},
							'wikieditor-publish-dialog-goback': function() {
								$(this).dialog( 'close' );
							}
						},
						open: function() {
							$( '#wikiEditor-' + context.instance + '-dialog-summary' ).focus();
						},
						width: 500
					},
					resizeme: false
				}
			}
		);
		context.fn.addButton( {
			'captionMsg': 'wikieditor-publish-button-publish',
			'action': function() {
				$( '#' + dialogID ).dialog( 'open' );
				return false;
			}
		} );
		context.fn.addButton( {
			'captionMsg': 'wikieditor-publish-button-cancel',
			'action': function() { }
		} );
	}
}

}; } )( jQuery );/* TemplateEditor module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.templateEditor = {

/**
 * Event handlers
 */
evt: {
	mark: function( context, event ) {
		// Get references to the markers and tokens from the current context
		var markers = context.modules.highlight.markers;
		var tokenArray = context.modules.highlight.tokenArray;
		// Collect matching level 0 template call boundaries from the tokenArray
		var level = 0;
		
		var tokenIndex = 0;
		while ( tokenIndex < tokenArray.length ){
			while ( tokenIndex < tokenArray.length && tokenArray[tokenIndex].label != 'TEMPLATE_BEGIN' ) {
				tokenIndex++;
			}
			//open template
			if ( tokenIndex < tokenArray.length ) {
				var beginIndex = tokenIndex;
				var endIndex = -1; //no match found
				var openTemplates = 1;
				var templatesMatched = false;
				while ( tokenIndex < tokenArray.length && endIndex == -1 ) {
					tokenIndex++;
					if ( tokenArray[tokenIndex].label == 'TEMPLATE_BEGIN' ) {
						openTemplates++;
					} else if ( tokenArray[tokenIndex].label == 'TEMPLATE_END' ) {
						openTemplates--;
						if ( openTemplates == 0 ) {
							endIndex = tokenIndex;
						} //we can stop looping
					}
				}//while finding template ending
				if ( endIndex != -1 ) {
					markers.push( {
						start: tokenArray[beginIndex].offset,
						end: tokenArray[endIndex].offset,
						needsWrap: function( ca1, ca2 ) {
							return !$( ca1.parentNode ).is( 'div.wikiEditor-template' ) ||
								ca1.previousSibling != null || ca1.nextSibling != null;
						},
						afterWrap: $.wikiEditor.modules.templateEditor.fn.stylize
					} );
				} else { //else this was an unmatched opening
					tokenArray[beginIndex].label = 'TEMPLATE_FALSE_BEGIN';
					tokenIndex = beginIndex;
				}
			}//if opentemplates
		}
	}
},
/**
 * Regular expressions that produce tokens
 */
exp: [
	{ 'regex': /{{/, 'label': "TEMPLATE_BEGIN" },
	{ 'regex': /}}/, 'label': "TEMPLATE_END", 'markAfter': true }
],
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates template form module within wikieditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// Initialize module within the context
		context.modules.templateEditor = {};
	},
	stylize: function( wrappedTemplate ) {
		$( wrappedTemplate ).each( function() {
			if ( typeof $(this).data( 'model' ) != 'undefined' ) {
				// We have a model, so all this init stuff has already happened
				return;
			}
			// Hide this
			$(this).addClass( 'wikiEditor-nodisplay wikiEditor-template' );
			// Build a model for this
			$(this).data( 'model' , new $.wikiEditor.modules.templateEditor.fn.model( $(this).text() ) );
			var model = $(this).data( 'model' );
			// Expand
			function expandTemplate( $displayDiv ) {
				// Housekeeping
				$displayDiv.removeClass( 'wikiEditor-template-collapsed' );
				$displayDiv.addClass( 'wikiEditor-template-expanded' );
				$displayDiv.text( model.getText() );
			};
			// Collapse
			function collapseTemplate( $displayDiv ) {
				// Housekeeping
				$displayDiv.addClass( 'wikiEditor-template-collapsed' );
				$displayDiv.removeClass( 'wikiEditor-template-expanded' );
				$displayDiv.text( model.getName() );
			};
			// Build the collapsed version of this template
			var $visibleDiv = $( "<div />" ).addClass( 'wikiEditor-noinclude' );
			// Let these two know about each other
			$(this).data( 'display', $visibleDiv );
			$visibleDiv.data( 'wikitext', $(this) );
			$(this).after( $visibleDiv );
			// Add click handler
			$visibleDiv.mousedown( function() {
				// Is collapsed, switch to expand
				if ( $(this).hasClass( 'wikiEditor-template-collapsed' ) ) {
					expandTemplate( $(this) );
				} else {
					collapseTemplate( $(this) );
				}
			});
			collapseTemplate( $visibleDiv );
		});
	},
	/**
	 * Builds a template model from given wikitext representation, allowing object-oriented manipulation of the contents
	 * of the template while preserving whitespace and formatting.
	 * 
	 * @param wikitext String of wikitext content
	 */
	model: function( wikitext ) {
		
		/* Private Functions */
		
		/**
		 * Builds a Param object.
		 * 
		 * @param name
		 * @param value
		 * @param number
		 * @param nameIndex
		 * @param equalsIndex
		 * @param valueIndex
		 */
		function Param( name, value, number, nameIndex, equalsIndex, valueIndex ) {
			this.name = name;
			this.value = value;
			this.number = number;
			this.nameIndex = nameIndex;
			this.equalsIndex = equalsIndex;
			this.valueIndex = valueIndex;
		}
		/**
		 * Builds a Range object.
		 * 
		 * @param begin
		 * @param end
		 */
		function Range( begin, end ) {
			this.begin = begin;
			this.end = end;
		}
		/**
		 * Set 'original' to true if you want the original value irrespective of whether the model's been changed
		 * 
		 * @param name
		 * @param value
		 * @param original
		 */
		function getSetValue( name, value, original ) {
			var valueRange;
			var rangeIndex;
			var retVal;
			if ( isNaN( name ) ) {
				// It's a string!
				if ( typeof paramsByName[name] == 'undefined' ) {
					// Does not exist
					return "";
				}
				rangeIndex = paramsByName[name];
			} else {
				// It's a number!
				rangeIndex = parseInt( name );
			}
			if ( typeof params[rangeIndex]  == 'undefined' ) {
				// Does not exist
				return "";
			}
			valueRange = ranges[params[rangeIndex].valueIndex];
			if ( typeof valueRange.newVal == 'undefined' || original ) {
				// Value unchanged, return original wikitext
				retVal = wikitext.substring( valueRange.begin, valueRange.end );
			} else {
				// New value exists, return new value
				retVal = valueRange.newVal;
			}
			if ( value != null ) {
				ranges[params[rangeIndex].valueIndex].newVal = value;
			}
			return retVal;
		};
		
		/* Public Functions */
		
		/**
		 * Get template name
		 */
		this.getName = function() {
			if( typeof ranges[templateNameIndex].newVal == 'undefined' ) {
				return wikitext.substring( ranges[templateNameIndex].begin, ranges[templateNameIndex].end );
			} else {
				return ranges[templateNameIndex].newVal;
			}
		};
		/**
		 * Set template name (if we want to support this)
		 * 
		 * @param name
		 */
		this.setName = function( name ) {
			ranges[templateNameIndex].newVal = name;
		};
		/**
		 * Set value for a given param name / number
		 * 
		 * @param name
		 * @param value
		 */
		this.setValue = function( name, value ) {
			return getSetValue( name, value, false );
		};
		/**
		 * Get value for a given param name / number
		 * 
		 * @param name
		 */
		this.getValue = function( name ) {
			return getSetValue( name, null, false );
		};
		/**
		 * Get original value of a param
		 * 
		 * @param name
		 */
		this.getOriginalValue = function( name ) {
			return getSetValue( name, null, true );
		};
		/**
		 * Get a list of all param names (numbers for the anonymous ones)
		 */
		this.getAllParamNames = function() {
			return paramsByName;
		};
		/**
		 * Get the initial params
		 */
		this.getAllInitialParams = function(){
			return params;
		}
		/**
		 * Get original template text
		 */
		this.getOriginalText = function() {
			return wikitext;
		};
		/**
		 * Get modified template text
		 */
		this.getText = function() {
			newText = "";
			for ( i = 0 ; i < ranges.length; i++ ) {
				if( typeof ranges[i].newVal == 'undefined' ) {
					newText += wikitext.substring( ranges[i].begin, ranges[i].end );
				} else {
					newText += ranges[i].newVal;
				}
			}
			return newText;
		};
		
		// Whitespace* {{ whitespace* nonwhitespace:
		if ( wikitext.match( /\s*{{\s*\S*:/ ) ) {
			// We have a parser function!
		}
		/*
		 * Take all template-specific characters that are not particular to the template we're looking at, namely {|=},
		 * and convert them into something harmless, in this case 'X'
		 */
		// Get rid of first {{ with whitespace
		var sanatizedStr = wikitext.replace( /{{/, "  " );
		// Replace end
		endBraces = sanatizedStr.match( /}}\s*$/ );
		sanatizedStr = sanatizedStr.substring( 0, endBraces.index ) + "  " +
			sanatizedStr.substring( endBraces.index + 2 );
		// Match the open braces we just found with equivalent closing braces note, works for any level of braces
		while ( sanatizedStr.indexOf( '{{' ) != -1 ) {
			startIndex = sanatizedStr.indexOf( '{{' ) + 1;
			openBraces = 2;
			endIndex = startIndex;
			while ( openBraces > 0 ) {
				var brace = sanatizedStr[++endIndex];
				openBraces += brace == '}' ? -1 : brace == '{' ? 1 : 0;
			}
			sanatizedSegment = sanatizedStr.substring( startIndex,endIndex ).replace( /[{}|=]/g , 'X' );
			sanatizedStr =
				sanatizedStr.substring( 0, startIndex ) + sanatizedSegment + sanatizedStr.substring( endIndex );
		}
		/*
		 * Parse 1 param at a time
		 */
		var ranges = [];
		var params = [];
		var templateNameIndex = 0;
		var doneParsing = false;
		oldDivider = 0;
		divider = sanatizedStr.indexOf( '|', oldDivider );
		if ( divider == -1 ) {
			divider = sanatizedStr.length;
			doneParsing = true;
		}
		nameMatch = wikitext.substring( oldDivider, divider ).match( /[^{\s]+/ );
		if ( nameMatch != undefined ) {
			ranges.push( new Range( oldDivider,nameMatch.index ) ); //whitespace and squiggles upto the name
			templateNameIndex = ranges.push( new Range( nameMatch.index,
				nameMatch.index + nameMatch[0].length ) );
			templateNameIndex--; //push returns 1 less than the array
			ranges[templateNameIndex].old = wikitext.substring( ranges[templateNameIndex].begin,
				ranges[templateNameIndex].end );
		}
		params.push( ranges[templateNameIndex].old ); //put something in params (0)
		/*
		 * Start looping over params
		 */
		var currentParamNumber = 0;
		var valueEndIndex;
		var paramsByName = [];
		while ( !doneParsing ) {
			currentParamNumber++;
			oldDivider = divider;
			divider = sanatizedStr.indexOf( '|', oldDivider + 1 );
			if ( divider == -1 ) {
				divider = sanatizedStr.length;
				doneParsing = true;
			}
			currentField = sanatizedStr.substring( oldDivider+1, divider );
			if ( currentField.indexOf( '=' ) == -1 ) {
				// anonymous field, gets a number
				valueBegin = currentField.match( /\S+/ ); //first nonwhitespace character
				valueBeginIndex = valueBegin.index + oldDivider + 1;
				valueEnd = currentField.match( /[^\s]\s*$/ ); //last nonwhitespace character
				valueEndIndex = valueEnd.index + oldDivider + 2;
				ranges.push( new Range( ranges[ranges.length-1].end,
					valueBeginIndex ) ); //all the chars upto now
				nameIndex = ranges.push( new Range( valueBeginIndex, valueBeginIndex ) ) - 1;
				equalsIndex = ranges.push( new Range( valueBeginIndex, valueBeginIndex ) ) - 1;
				valueIndex = ranges.push( new Range( valueBeginIndex, valueEndIndex ) ) - 1;
				params.push( new Param(
					currentParamNumber,
					wikitext.substring( ranges[valueIndex].begin, ranges[valueIndex].end ),
					currentParamNumber,
					nameIndex,
					equalsIndex,
					valueIndex
				) );
				paramsByName[currentParamNumber] = currentParamNumber;
			} else {
				// There's an equals, could be comment or a value pair
				currentName = currentField.substring( 0, currentField.indexOf( '=' ) );
				// Still offset by oldDivider - first nonwhitespace character
				nameBegin = currentName.match( /\S+/ );
				if ( nameBegin == null ) {
					// This is a comment inside a template call / parser abuse. let's not encourage it
					divider++;
					currentParamNumber--;
					continue;
				}
				nameBeginIndex = nameBegin.index + oldDivider + 1;
				// Last nonwhitespace and non } character
				nameEnd = currentName.match( /[^\s]\s*$/ );
				nameEndIndex = nameEnd.index + oldDivider + 2;
				// All the chars upto now 
				ranges.push( new Range( ranges[ranges.length-1].end, nameBeginIndex ) );
				nameIndex = ranges.push( new Range( nameBeginIndex, nameEndIndex ) ) - 1;
				currentValue = currentField.substring( currentField.indexOf( '=' ) + 1);
				oldDivider += currentField.indexOf( '=' ) + 1;
				// First nonwhitespace character
				valueBegin = currentValue.match( /\S+/ );
				valueBeginIndex = valueBegin.index + oldDivider + 1;
				// Last nonwhitespace and non } character
				valueEnd = currentValue.match( /[^\s]\s*$/ );
				valueEndIndex = valueEnd.index + oldDivider + 2;
				// All the chars upto now
				equalsIndex = ranges.push( new Range( ranges[ranges.length-1].end, valueBeginIndex) ) - 1;
				valueIndex = ranges.push( new Range( valueBeginIndex, valueEndIndex ) ) - 1;
				params.push( new Param(
					wikitext.substring( nameBeginIndex, nameEndIndex ),
					wikitext.substring( valueBeginIndex, valueEndIndex ),
					currentParamNumber,
					nameIndex,
					equalsIndex,
					valueIndex
				) );
				paramsByName[wikitext.substring( nameBeginIndex, nameEndIndex )] = currentParamNumber;
			}
		}
		// The rest of the string
		ranges.push( new Range( valueEndIndex, wikitext.length ) );
		
		// Save vars
		this.ranges = ranges;
		this.wikitext = wikitext;
		this.params = params;
		this.paramsByName = paramsByName;
		this.templateNameIndex = templateNameIndex;
	} // model
}

}; } )( jQuery );
/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * Configuration
 */
cfg: {
	// Default width of table of contents
	defaultWidth: '166px',
	// Minimum width to allow resizing to before collapsing the table of contents - used when resizing and collapsing
	minimumWidth: '70px',
},
/**
 * API accessible functions
 */
api: {
	//
},
/**
 * Event handlers
 */
evt: {
	ready: function( context, event ) {
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context );
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
	resize: function( context, event ) {
		context.modules.toc.$toc.height(
			context.$ui.find( '.wikiEditor-ui-left' ).height() - 
			context.$ui.find( '.tab-toc' ).outerHeight()
		);
	}
},
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
		if ( '$toc' in context.modules.toc ) {
			return;
		}
		
		var height = context.$ui.find( '.wikiEditor-ui-left' ).height();
		context.modules.toc.$toc = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.data( 'context', context );
		context.$ui.find( '.wikiEditor-ui-right' )
			.css( 'width', $.wikiEditor.modules.toc.cfg.defaultWidth )
			.append( context.modules.toc.$toc );
		context.modules.toc.$toc.height(
			context.$ui.find( '.wikiEditor-ui-left' ).height()
		);
		context.$ui.find( '.wikiEditor-ui-left' )
			.css( 'marginRight', "-" + $.wikiEditor.modules.toc.cfg.defaultWidth )
			.children()
			.css( 'marginRight', $.wikiEditor.modules.toc.cfg.defaultWidth );
	},
	
	unhighlight: function( context ) {
		context.modules.toc.$toc.find( 'div' ).removeClass( 'current' );
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
			var sectionLink = context.modules.toc.$toc.find( 'div.section-' + section );
			sectionLink.addClass( 'current' );
			
			// Scroll the highlighted link into view if necessary
			var relTop = sectionLink.offset().top - context.modules.toc.$toc.offset().top;
			var scrollTop = context.modules.toc.$toc.scrollTop();
			var divHeight = context.modules.toc.$toc.height();
			var sectionHeight = sectionLink.height();
			if ( relTop < 0 )
				// Scroll up
				context.modules.toc.$toc.scrollTop( scrollTop + relTop );
			else if ( relTop + sectionHeight > divHeight )
				// Scroll down
				context.modules.toc.$toc.scrollTop( scrollTop + relTop + sectionHeight - divHeight );
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
			.css( { 'marginTop': '1px', 'position': 'absolute', 'left': 'auto', 'right': 0, 'top': pT } )
			.fadeOut( 'fast', function() {
				$( this ).hide()
				.css( { 'marginTop': '0', 'width': '1px' } );
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).fadeIn( 'fast' );
			 } )
			.prev()
			.animate( { 'marginRight': '-1px' }, 'fast', function() {
				$( this ).css( 'marginRight', 0 );
				// Let the UI know things have moved around
				context.fn.trigger( 'resize' );
			} )
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
	expand: function( event ) {
		var $this = $( this ),
			context = $this.data( 'context' ),
			openWidth = context.modules.toc.$toc.data( 'openWidth' );
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		$this.parent()
			.show()
			.css( 'marginTop', '1px' )
			.animate( { 'width' : openWidth }, 'fast', function() {
				context.$content.trigger( 'mouseup' );
				$( this ).css( { 'marginTop': '0', 'position': 'relative', 'right': 'auto', 'top': 'auto' } );
			 } )
			.prev()
			.animate( { 'marginRight': ( parseFloat( openWidth ) * -1 ) }, 'fast' )
			.children()
			.animate( { 'marginRight': openWidth }, 'fast', function() {
				// Let the UI know things have moved around
				context.fn.trigger( 'resize' );
			} );
		$.cookie( 'wikiEditor-' + context.instance + '-toc-width',
			context.modules.toc.$toc.data( 'openWidth' ) );
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
					.click( function( event ) {
						context.fn.scrollToTop( $(this).data( 'wrapper' ) );
						context.$textarea.textSelection( 'setSelection', {
							'start': 0,
							'startContainer': $(this).data( 'wrapper' )
						} );
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
					context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc' ); return false;
				} )
				.find( 'a' )
				.text( gM( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl' )
				.append( '<a href="#" />' )
				.bind( 'click.wikiEditor-toc', function() {
					context.modules.toc.$toc.trigger( 'expand.wikiEditor-toc' ); return false;
				} )
				.hide()
				.find( 'a' )
				.text( gM( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.toc.$toc );
			context.$ui.find( '.wikiEditor-ui-left .wikiEditor-ui-top' ).append( $expandControl );
			context.fn.trigger( 'resize' );
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
							.css( {
								'position': 'absolute',
								'z-index': 2,
								'left': 0,
								'top': 0,
								'bottom': 0,
								'right': 0
							} )
							.appendTo( context.$ui.find( '.wikiEditor-ui-left' ) );
						$this.resizable( 'option', 'maxWidth', $this.parent().width() - 450 );
					},
					resize: function( e, ui ) {
						// for some odd reason, ui.size.width seems a step ahead of what the *actual* width of
						// the resizable is
						$( this ).css( { 'width': ui.size.width, 'top': 'auto', 'height': 'auto' } )
							.data( 'wikiEditor-ui-left' ).css( 'marginRight', ( -1 * ui.size.width ) )
							.children().css( 'marginRight', ui.size.width );
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					},
					stop: function ( e, ui ) {
						context.$ui.find( '.wikiEditor-ui-resize-mask' ).remove();
						context.$content.trigger( 'mouseup' );
						if( ui.size.width < parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) ) {
							context.modules.toc.$toc.trigger( 'collapse' );
						} else {
							context.modules.toc.$toc.data( 'openWidth', ui.size.width );
							$.cookie( 'wikiEditor-' + context.instance + '-toc-width', ui.size.width );
						}
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					}
				});
			// Convert our east resize handle into a secondary west resize handle
			var handle = $j( 'body' ).is( '.rtl' ) ? 'w' : 'e'
			context.$ui.find( '.ui-resizable-' + handle )
				.removeClass( 'ui-resizable-' + handle )
				.addClass( 'ui-resizable-' + ( handle == 'w' ? 'e' : 'w' ) )
				.addClass( 'wikiEditor-ui-toc-resize-grip' );
			// Bind collapse and expand event handlers to the TOC
			context.modules.toc.$toc
				.bind( 'collapse.wikiEditor-toc', $.wikiEditor.modules.toc.fn.collapse )
				.bind( 'expand.wikiEditor-toc', $.wikiEditor.modules.toc.fn.expand  );
			context.modules.toc.$toc.data( 'openWidth', $.wikiEditor.modules.toc.cfg.defaultWidth );
			// If the toc-width cookie is set, reset the widths based upon that
			if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) == 0 ) {
				context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc', { data: context } );
			} else if ( $.cookie( 'wikiEditor-' + context.instance + '-toc-width' ) > 0 ) {
				var initialWidth = $.cookie( 'wikiEditor-' + context.instance + '-toc-width' );
				if( initialWidth < parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) )
					initialWidth = parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) + 1;
				context.modules.toc.$toc.data( 'openWidth', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-right' )
					.css( 'width', initialWidth + 'px' );
				context.$ui.find( '.wikiEditor-ui-left' )
					.css( 'marginRight', ( parseFloat( initialWidth ) * -1 ) + 'px' )
					.children()
					.css( 'marginRight', initialWidth + 'px' );
			}
		}
		
		// Build outline from wikitext
		var outline = [], h = 0;
		
		// Traverse all text nodes in context.$content
		function traverseTextNodes() {
			if ( this.nodeName != '#text' ) {
				$( this.childNodes ).each( traverseTextNodes );
				return;
			}
			var text = this.nodeValue;
			
			// Get the previous and next node in Euler tour order
			var p = this;
			while( !p.previousSibling )
				p = p.parentNode;
			var prev = p ? p.previousSibling : null;
			
			p = this;
			while ( p && !p.nextSibling )
				p = p.parentNode;
			var next = p ? p.nextSibling : null;
			
			// Edge case: there are more equals signs,
			// but they're not all in the <div>. Eat them.
			if ( prev && prev.nodeName == '#text' ) {
				var prevText = prev.nodeValue;
				while ( prevText.substr( -1 ) == '=' ) {
					prevText = prevText.substr( 0, prevText.length - 1 );
					text = '=' + text;
				}
				prev.nodeValue = prevText;
			}
			var next = this.nextSibling;
			if ( next && next.nodeName == '#text' ) {
				var nextText = next.nodeValue;
				while ( nextText.substr( 0, 1 ) == '=' ) {
					nextText = nextText.substr( 1 );
					text = text + '=';
				}
				next.nodeValue = nextText;
			}
			if ( text != this.nodeValue )
				this.nodeValue = text;
			
			var match = text.match( /^(={1,6})(.+?)\1\s*$/ );
			if ( !match ) {
				if ( $(this).parent().is( '.wikiEditor-toc-header' ) )
					// Header has become invalid
					// Remove the class but keep the <div> intact
					// to prevent issues with Firefox
					// TODO: Fix this issue
					//$(this).parent()
					//	.removeClass( 'wikiEditor-toc-header' );
					$(this).parent().replaceWith( text );
				return;
			}
			
			// Wrap the header in a <div>, unless it's already wrapped
			var div;
			if ( $(this).parent().is( '.wikiEditor-toc-header' ) )
				div = $(this).parent();
			else if ( $(this).parent().is( 'div' ) )
				div = $(this).parent().addClass( 'wikiEditor-toc-header' );
			else {
				div = $( '<div />' )
					.text( text )
					.css( 'display', 'inline' )
					.addClass( 'wikiEditor-toc-header' );
				$(this).replaceWith( div );
			}
			outline[h] = { 'text': match[2], 'wrapper': div, 'level': match[1].length, 'index': h + 1 };
			h++;
		}
		context.$content.each( traverseTextNodes );
		
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
		context.modules.toc.$toc.html( buildList( structure ) );
		
		if ( wgNavigableTOCResizable && !context.$ui.data( 'resizableDone' ) ) {
			buildResizeControls();
			buildCollapseControls();
		}
		context.modules.toc.$toc.find( 'div' ).autoEllipse( { 'position': 'right', 'tooltip': true } );
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
					var $sections = context.modules.toolbar.$toolbar
					.find( 'div.sections' );
					var $tabs = context.modules.toolbar.$toolbar
					.find( 'div.tabs' );
					for ( section in data[type] ) {
						if ( section == 'main' ) {
							// Section
							context.modules.toolbar.$toolbar
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
					var $section = context.modules.toolbar.$toolbar
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
					var $group = context.modules.toolbar.$toolbar
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
					var $pages = context.modules.toolbar.$toolbar
					.find( 'div[rel=' + data.section + '].section .pages' );
					var $index = context.modules.toolbar.$toolbar
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
					var $table = context.modules.toolbar.$toolbar.find(
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
					$characters = context.modules.toolbar.$toolbar.find(
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
					context.modules.toolbar.$toolbar.find( index ).remove();
					$.wikiEditor.modules.toolbar.fn.updateBookletSelection(
						context,
						null,
						context.modules.toolbar.$toolbar.find( target ),
						context.modules.toolbar.$toolbar.find( index )
					);
				}
			} else {
				// Just a section, remove the tab too!
				context.modules.toolbar.$toolbar.find( tab ).remove();
			}
			js_log('target is: ' + target);
			context.modules.toolbar.$toolbar.find( target ).remove();
		}
	}
},
/**
 * Event handlers
 */
evt: {
	resize: function( context, event ) {
		context.$ui.find( '.sections' ).height( context.$ui.find( '.sections .section:visible' ).outerHeight() );
	}
},
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates a toolbar module within a wikiEditor
	 *
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create : function( context, config ) {
		if ( '$toolbar' in context.modules.toolbar ) {
			return;
		}
		context.modules.toolbar.$toolbar = $( '<div />' )
			.addClass( 'wikiEditor-ui-toolbar' )
			.attr( 'id', 'wikiEditor-ui-toolbar' );
		$.wikiEditor.modules.toolbar.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-top' ).append( context.modules.toolbar.$toolbar );
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
				var src = $.wikiEditor.autoIcon( tool.icon, $.wikiEditor.imgPath + 'toolbar/' );
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
						$previousSections.css( 'position', 'absolute' );
						$previousSections.fadeOut( 'fast', function() { $(this).css( 'position', 'relative' ); } );
						$(this).parent().parent().find( 'a' ).removeClass( 'current' );
						$sections.css( 'overflow', 'hidden' );
						if ( show ) {
							$section.fadeIn( 'fast' );
							$sections
								.css( 'display', 'block' )
								.animate( { 'height': $section.outerHeight() }, $section.outerHeight() * 2, function() {
									$(this).css( 'overflow', 'visible' ).css( 'height', 'auto' );
									context.fn.trigger( 'resize' );
								} );
							$(this).addClass( 'current' );
						} else {
							$sections
								.css( 'height', $section.outerHeight() )
								.animate( { 'height': 'hide' }, $section.outerHeight() * 2, function() {
									$(this).css( { 'overflow': 'visible', 'height': 0 } );
									context.fn.trigger( 'resize' );
								} );
						}
						// Click tracking
						if($.trackAction != undefined){
							$.trackAction($section.attr('rel') + '.' + ( show ? 'show': 'hide' )  );
						}
						// Save the currently visible section
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
		var $tabs = $( '<div />' ).addClass( 'tabs' ).appendTo( context.modules.toolbar.$toolbar );
		var $sections = $( '<div />' ).addClass( 'sections' ).appendTo( context.modules.toolbar.$toolbar );
		context.modules.toolbar.$toolbar.append( $( '<div />' ).css( 'clear', 'both' ) );
		var sectionQueue = [];
		for ( section in config ) {
			if ( section == 'main' ) {
				context.modules.toolbar.$toolbar.prepend(
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
						context.fn.trigger( 'resize' );
					} );
				}
			}
		} );
	}
}

}; } )( jQuery );
