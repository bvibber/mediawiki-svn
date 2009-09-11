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

/*

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

/**
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
		// Validate ccontextration using fallback values
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
					if ( context.config.suggestions.length == 0 ) {
						// Hide the dive when no suggestion exist
						context.data.$container.hide();
					} else {
						// Rebuild the suggestions list
						context.data.$container.show();
						var $results = context.data.$container.children( '.suggestions-results' );
						$results.empty();
						for ( var i = 0; i < context.config.suggestions.length; i++ ) {
							$result = $( '<div />' )
								.addClass( 'suggestions-result' )
								.attr( 'rel', i )
								.data( 'text', context.config.suggestions[i] );
							// Allow custom rendering
							if ( typeof context.config.result.render == 'function' ) {
								context.config.result.render.call( $result, context.config.suggestions[i] );
							} else {
								$result.text( context.config.suggestions[i] );
							}
							$results.append( $result );
						}
						// Update the size and position of the list
						context.data.$container.css( {
							'top': context.config.$region.offset().top + context.config.$region.outerHeight(),
							'bottom': 'auto',
							'width': context.config.$region.outerWidth(),
							'height': 'auto',
							'left': context.config.$region.offset().left,
							'right': 'auto'
						} );
					}
				}
				break;
			case 'maxRows':
				context.config[property] = Math.max( 1, Math.min( 100, value ) );
				break;
			case 'delay':
				context.config[property] = Math.max( 0, Math.min( 12000, value ) );
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
		var selected = context.data.$container.find( '.suggestions-result-current' )
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
			}
		}
		$.suggestions.special( context );
	},
	/**
	 * Respond to keypress event
	 * @param {Integer} key Code of key pressed
	 */
	keypress: function( context, key ) {
		switch ( key ) {
			// Arrow down
			case 40:
				if ( context.data.$container.is( ':visible' ) ) {
					$.suggestions.highlight( context, 'next', true );
				} else {
					$.suggestions.update( context, false );
				}
				context.data.$textbox.trigger( 'change' );
				break;
			// Arrow up
			case 38:
				if ( context.data.$container.is( ':visible' ) ) {
					$.suggestions.highlight( context, 'prev', true );
				}
				context.data.$textbox.trigger( 'change' );
				break;
			// Escape
			case 27:
				context.data.$container.hide();
				$.suggestions.restore( context );
				$.suggestions.cancel( context );
				context.data.$textbox.trigger( 'change' );
				break;
			// Enter
			case 13:
				context.data.$container.hide();
				break;
			default:
				$.suggestions.update( context, true );
				break;
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
					'delay': 1200,
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
							highlight( $result, true );
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
				} )
				.keypress( function() {
					context.data.keypressedCount++;
					$.suggestions.keypress( context, context.data.keypressed );
				} )
				.keyup( function() {
					// Some browsers won't throw keypress() for arrow keys. If we got a keydown and a keyup without a
					// keypress in between, solve it
					if ( context.data.keypressedCount == 0 ) {
						$.suggestions.keypress( context, context.data.keypressed );
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
( function( $ ) { $.fn.extend( {

getSelection: function() {
	var e = this.jquery ? this[0] : this;
	var retval = '';
	if ( e.style.display == 'none' ) {
		// Do nothing
	} else if ( document.selection && document.selection.createRange ) {
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
 * 
 * @param pre Text to insert before selection
 * @param peri Text to insert at caret if selection is empty
 * @param post Text to insert after selection
 * @param ownline If true, put the inserted text is on its own line
 * @param replace If true, replaces any selected text with peri; if false, peri is ignored and selected text is left alone
 */
encapsulateSelection: function( pre, peri, post, ownline, replace ) {
	/**
	 * Check if the selected text is the same as the insert text
	 */ 
	function checkSelectedText() {
		if ( !selText ) {
			selText = peri;
			isSample = true;
		} else if ( replace ) {
			selText = peri;
		} else if ( selText.charAt( selText.length - 1 ) == ' ' ) {
			// Exclude ending space char
			selText = selText.substring(0, selText.length - 1);
			post += ' '
		}
	}
	var e = this.jquery ? this[0] : this;
	var selText = $(this).getSelection();
	var isSample = false;
	if ( e.style.display == 'none' ) {
		// Do nothing
	} else if ( document.selection && document.selection.createRange ) {
		// IE/Opera
		if ( document.documentElement && document.documentElement.scrollTop ) {
			var winScroll = document.documentElement.scrollTop;
		} else if ( document.body ) {
			var winScroll = document.body.scrollTop;
		}
		$(this).focus();
		var range = document.selection.createRange();
		if ( ownline && range.moveStart ) {
			var range2 = document.selection.createRange();
			range2.collapse();
			range2.moveStart( 'character', -1 );
			// FIXME: Which check is correct?
			if ( range2.text != "\r" && range2.text != "\n" && range3.text != "" ) {
				pre = "\n" + pre;
			}
			var range3 = document.selection.createRange();
			range3.collapse( false );
			range3.moveEnd( 'character', 1 );
			if ( range3.text != "\r" && range3.text != "\n" && range3.text != "" ) {
				post += "\n";
			}
		}
		checkSelectedText();
		range.text = pre + selText + post;
		if ( isSample && range.moveStart ) {
			if ( window.opera ) {
				post = post.replace( /\n/g, '' );
			}
			range.moveStart( 'character', - post.length - selText.length );
			range.moveEnd( 'character', - post.length );
		}
		range.select();
		if ( document.documentElement && document.documentElement.scrollTop ) {
			document.documentElement.scrollTop = winScroll
		} else if ( document.body ) {
			document.body.scrollTop = winScroll;
		}
	} else if ( e.selectionStart || e.selectionStart == '0' ) {
		// Mozilla
		var textScroll = e.scrollTop;
		$(this).focus();
		var startPos = e.selectionStart;
		var endPos = e.selectionEnd;
		checkSelectedText();
		if ( ownline ) {
			if ( startPos != 0 && e.value.charAt( startPos - 1 ) != "\n" ) {
				pre = "\n" + pre;
			}
			if ( e.value.charAt( endPos ) != "\n" ) {
				post += "\n";
			}
		}
		e.value = e.value.substring( 0, startPos ) + pre + selText + post + e.value.substring( endPos, e.value.length );
		if ( isSample ) {
			e.selectionStart = startPos + pre.length;
			e.selectionEnd = startPos + pre.length + selText.length;
		} else {
			e.selectionStart = startPos + pre.length + selText.length + post.length;
			e.selectionEnd = e.selectionStart;
		}
		e.scrollTop = textScroll;
	}
	$(this).trigger( 'encapsulateSelection', [ pre, peri, post, ownline, replace ] );
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
 getCaretPosition: function() {
	function getCaret( e ) {
		var caretPos = 0;
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
		} else if ( e.selectionStart || e.selectionStart == '0' ) {
			// Firefox support
			caretPos = e.selectionStart;
		}
		return caretPos;
	}
	return getCaret( this.get( 0 ) );
},
setSelection: function( start, end ) {
	return this.each( function() {
		if ( this.selectionStart || this.selectionStart == '0' ) {
			this.selectionStart = start;
			this.selectionEnd = end;
		} else if ( document.body.createTextRange ) {
			var selection = document.body.createTextRange;
			selection.setToElementText( this );
			var length = selection.text.length;
			selection.moveStart( 'character', start );
			selection.moveEnd( 'character', -length + end );
			selection.select();
		}
	});
},
/**
 * Ported from Wikia's LinkSuggest extension
 * https://svn.wikia-code.com/wikia/trunk/extensions/wikia/LinkSuggest
 * 
 * Scroll a textarea to a certain offset
 * @param pos Byte offset
 */
scrollToCaretPosition: function( pos ) {
	function getLineLength( e ) {
		return Math.floor( e.scrollWidth / ( $.os.name == 'linux' ? 7 : 8 ) );
	}
	function getCaretScrollPosition( e ) {
		var text = e.value.replace( /\r/g, "" );
		var caret = $( e ).getCaretPosition();
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
		$(this).focus();
		if ( this.selectionStart || this.selectionStart == '0' ) {
			// Mozilla
			this.selectionStart = pos;
			this.selectionEnd = pos;
			$(this).scrollTop( getCaretScrollPosition( this ) );
		} else if ( document.selection && document.selection.createRange ) {
			// IE / Opera
			/*
			 * IE automatically scrolls the section to the bottom of the page,
			 * except if it's already in view and the cursor position hasn't
			 * changed, in which case it does nothing. In that case we'll force
			 * it to act by moving one character back and forth.
			 */
			var range = document.selection.createRange();
			var oldPos = $(this).getCaretPosition();
			var goBack = false;
			if ( oldPos == pos ) {
				pos++;
				goBack = true;
			}
			range.moveToElementText( this );
			range.collapse();
			range.move( 'character', pos );
			range.select();
			this.scrollTop += range.offsetTop;
			if ( goBack ) {
				range.move( 'character', -1 );
				range.select();
			}
		}
		$(this).trigger( 'scrollToPosition' );
	} );
}

} ); } )( jQuery );/**
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
	'supportedBrowsers': {
		'ltr': { 'msie': 7, 'firefox': 2, 'opera': 9, 'safari': 3, 'chrome': 1, 'camino': 1 },
		'rtl': { 'msie': 8, 'firefox': 2, 'opera': 9, 'safari': 3, 'chrome': 1, 'camino': 1 }
	},
	/**
	 * Path to images - this is a bit messy, and it would need to change if
	 * this code (and images) gets moved into the core - or anywhere for
	 * that matter...
	 */
	imgPath : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/'
};

$.wikiEditor.isSupportKnown = function() {
	return ( function( supportedBrowsers ) {
		return $.browser.name in supportedBrowsers;
	} )( $.wikiEditor.supportedBrowsers[$( 'body.rtl' ).size() ? 'rtl' : 'ltr'] );
};
$.wikiEditor.isSupported = function() {
	return ( function( supportedBrowsers ) {
		return $.browser.name in supportedBrowsers && $.browser.versionNumber >= supportedBrowsers[$.browser.name];
	} )( $.wikiEditor.supportedBrowsers[$( 'body.rtl' ).size() ? 'rtl' : 'ltr'] );
};
// Wraps gM from js2, but allows raw text to supercede
$.wikiEditor.autoMsg = function( object, property ) {
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
};

$.fn.wikiEditor = function() {

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'wikiEditor-context' );

/* API */

// The first time this is called, we expect context to be undefined, meaning
// the editing UI has not yet been, and still needs to be, built. However, each
// additional call after that is expected to be an API call, which contains a
// string as the first argument which corresponds to a supported API call
if ( typeof context !== 'undefined' ) {
	// Since javascript gives arguments as an object, we need to convert them
	// so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			context.api[call]( context, arguments[0] == undefined ? {} : arguments[0] );
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

var instance = $.wikiEditor.instances.length;
context = { '$textarea': $(this), 'modules': {}, 'data': {}, 'instance': instance };
$.wikiEditor.instances[instance] = $(this);

// Encapsulate the textarea with some containers for layout
$(this)
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ).attr( 'id', 'wikiEditor-ui' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ).attr( 'id', 'wikiEditor-ui-bottom' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ).attr( 'id', 'wikiEditor-ui-text' ) );

// Get a reference to the outer container
context.$ui = $(this).parent().parent().parent();
context.$ui.after( $( '<div style="clear:both;"></div>' ) );
// Attach a container in the top
context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ).attr( 'id', 'wikiEditor-ui-top' ) );

// Create a set of standard methods for internal and external use
context.api = {
	/**
	 * Accepts either a string of the name of a module to add without any
	 * additional configuration parameters, or an object with members keyed with
	 * module names and valued with configuration objects
	 */
	addModule: function( context, data ) {
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
// If there was a configuration passed, it's assumed to be for the addModule
// API call, so we can just send it on it's way right now
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	context.api.addModule( context, arguments[0] );
}
//Each browser seems to do this differently, so let's keep our editor
//consistent by always starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// Store the context for next time, and support chaining
return $(this).data( 'wikiEditor-context', context );

};})(jQuery);/**
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
		$.wikiEditor.modules.dialogs.fn.create( context, { 'modules': data } )
	},
	openDialog: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'open' );
		}
	},
	closeDialog: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'close' );
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
				for ( msg in configuration.buttons ) {
					configuration.buttons[gM( msg )] = configuration.buttons[msg];
					delete configuration.buttons[msg];
				}
				// Create the dialog <div>
				$( '<div /> ' )
					.attr( 'id', module.id )
					.html( module.html )
					.data( 'context', context )
					.appendTo( $( 'body' ) )
					.each( module.init )
					.dialog( configuration )
					.bind( 'dialogopen', $.wikiEditor.modules.dialogs.fn.resize )
					.find( '.ui-tabs' ).bind( 'tabsshow', function() {
						$(this).closest( '.ui-dialog-content' ).each(
							$.wikiEditor.modules.dialogs.fn.resize );
					});
			}
		}
	},
	
	/**
	 * Resize a dialog so its contents fit
	 *
	 * Usage: dialog.each( resize ); or dialog.bind( 'blah', resize );
	 */
	resize: function() {
		var wrapper = $(this).closest( '.ui-dialog' );
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
			$(this).width( $(this).get(0).scrollWidth );
			wrapper.width( wrapper.get(0).scrollWidth );
			$(this).dialog( { 'width': wrapper.width() } );
		}
		
		$(this).css( 'white-space', oldWS );
		oldHidden.each( function() {
			$(this).attr( 'style', $(this).data( 'oldstyle' ) );
		});
	}
},
'modules': {}

}; } ) ( jQuery );/**
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
	modifyTool : function( context, data ){
		
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
				context.$textarea.encapsulateSelection(
					parts.pre, parts.peri, parts.post, action.options.ownline, action.type == 'replace'
				);
				break;
			case 'callback':
				if ( typeof action.execute == 'function' ) {
					action.execute( context );
				}
				break;
			case 'dialog':
				context.$textarea.wikiEditor( 'openDialog', { 'dialog': action.module } );
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
		if ( 'tools' in group ) {
			for ( tool in group.tools ) {
				$group.append( $.wikiEditor.modules.toolbar.fn.buildTool( context, tool, group.tools[tool] ) );
			}
		}
		return $group;
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
				var src = tool.icon;
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
			.click(
				function() {
					
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
		var selected = $
		.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		return $( '<span />' )
		.attr( { 'class' : 'tab tab-' + id, 'rel' : id } )
		.append(
			$( '<a />' )
				.addClass( selected == id ? 'current' : null )
				.attr( 'href', '#' )
				.text( $.wikiEditor.autoMsg( section, 'label' ) )
				.data( 'context', context )
				.click( function() {
					var $section =
						$(this).data( 'context' ).$ui.find( '.section-' + $(this).parent().attr( 'rel' ) );
					$(this).blur();
					var show = $section.css( 'display' ) == 'none';
					$section.parent().children().hide("fast");
					$(this).parent().parent().find( 'a' ).removeClass( 'current' );
					if ( show ) {
						$section.show("fast");
						$(this).addClass( 'current' );
					}
					
					//click tracking
					if($.trackAction != undefined){
						$.trackAction($section.attr('rel') + '.' + ( show ? 'show': 'hide' )  );
					}
					
					$.cookie(
						'wikiEditor-' + $(this).data( 'context' ).instance + '-toolbar-section',
						show ? $section.attr( 'rel' ) : null
					);
					return false;
				} )
		);
	},
	buildSection : function( context, id, section ) {
		var selected = $
		.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
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
			$section.css( 'display', selected == id ? 'block' : 'none' );
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
				// HACK: Opera doesn't seem to want to redraw after
				// these bits
				// are added to the DOM, so we can just FORCE it!
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', 'relative' );
			},
			'loop' : function( i, s ) {
				s.$sections.append( $.wikiEditor.modules.toolbar.fn.buildSection( s.context, s.id, s.config ) );
			}
		} );
	}
}

}; } )( jQuery );
/**
 * TOC Module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.toc = {

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
	 * Creates a table of contents module within a wikiEditor
	 * 
	 * @param {Object} context Context object of editor to create module in
	 * @param {Object} config Configuration object to create module from
	 */
	create: function( context, config ) {
		if ( '$toc' in context.modules ) {
			return;
		}
		context.modules.$toc = $( '<div></div>' )
			.addClass( 'wikiEditor-ui-toc' )
			.attr( 'id', 'wikiEditor-ui-toc' );
		$.wikiEditor.modules.toc.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-bottom' )
			.append( context.modules.$toc );
		context.modules.$toc.height(
			context.$ui.find( '.wikiEditor-ui-bottom' ).height()
		);
		// Make some css modifications to make room for the toc on the right...
		// Perhaps this could be configurable?
		context.modules.$toc
			.css( 'width', '12em' )
			.css( 'marginTop', -( context.$ui.find( '.wikiEditor-ui-bottom' ).height() ) );
		context.$ui.find( '.wikiEditor-ui-text' )
			.css( ( $( 'body.rtl' ).size() ? 'marginLeft' : 'marginRight' ), '12em' );
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context );
		$.wikiEditor.modules.toc.fn.update( context );
		context.$textarea
			.bind( 'keyup encapsulateSelection',
				function( event ) {
					var context = $(this).data( 'context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.build( context );
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			)
			.bind( 'mouseup scrollToPosition focus',
				function( event ) {
					var context = $(this).data( 'context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			)
			.blur( function() {
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
	},
 
	unhighlight: function( context ) {
		context.modules.$toc.find( 'a' ).removeClass( 'currentSelection' );
	},
	/**
	 * Highlight the section the cursor is currently within
	 * 
	 * @param {Object} context
	 */
	update: function( context ) {
		$.wikiEditor.modules.toc.fn.unhighlight( context );
		var position = context.$textarea.getCaretPosition();
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
			context.modules.$toc.find( 'a.section-' + section ).addClass( 'currentSelection' );
		}
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
			var list = $( '<ul></ul>' );
			for ( i in structure ) {
				var item = $( '<li></li>' )
					.append(
						$( '<a></a>' )
							.attr( 'href', '#' )
							.addClass( 'section-' + structure[i].index )
							.data( 'textbox', context.$textarea )
							.data( 'position', structure[i].position )
							.click( function( event ) {
								$(this).data( 'textbox' ).scrollToCaretPosition( $(this).data( 'position' ) );
								event.preventDefault();
							} )
							.text( structure[i].text )
					);
				if ( structure[i].sections !== undefined ) {
					item.append( buildList( structure[i].sections ) );
				}
				list.append( item );
			}
			return list;
		}
		// Build outline from wikitext
		var outline = [];
		var wikitext = '\n' + context.$textarea.val() + '\n';
		var headings = wikitext.match( /\n={1,5}.*={1,5}(?=\n)/g );
		var offset = 0;
		headings = $.makeArray( headings );
		for ( var h = 0; h < headings.length; h++ ) {
			text = headings[h];
			// Get position of first occurence
			var position = wikitext.indexOf( text, offset );
			// Update offset to avoid stumbling on duplicate headings
			if ( position > offset ) {
				offset = position + 1;
			} else if ( position == -1 ) {
				// Not sure this is possible, or what should happen
				continue;
			}
			// Trim off whitespace
			text = $.trim( text );
			// Detect the starting and ending heading levels
			var startLevel = 0;
			for ( var c = 0; c < text.length; c++ ) {
				if ( text.charAt( c ) == '=' ) {
					startLevel++;
				} else {
					break;
				}
			}
			var endLevel = 0;
			for ( var c = text.length - 1; c >= 0; c-- ) {
				if ( text.charAt( c ) == '=' ) {
					endLevel++;
				} else {
					break;
				}
			}
			// Use the lowest number of =s as the actual level
			var level = Math.min( startLevel, endLevel );
			text = $.trim( text.substr( level, text.length - ( level * 2 ) ) );
			// Add the heading data to the outline
			outline[h] = { 'text': text, 'position': position, 'level': level, 'index': h + 1 };
		}
		// Normalize heading levels for list creation
		// This is based on Linker::generateTOC() so, it should behave like the
		// TOC on rendered articles does - which is considdered to be correct
		// at this point in time.
		var lastLevel = 0;
		var nLevel = 0;
		for ( var i = 0; i < outline.length; i++ ) {
			if ( outline[i].level > lastLevel ) {
				nLevel++;
			}
			else if ( outline[i].level < nLevel ) {
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
		if ( $( 'input[name=wpSection]' ).val() == '' )
			structure.unshift( { 'text': wgPageName.replace(/_/g, ' '), 'level': 1, 'index': 0, 'position': 0 } );
		context.modules.$toc.html( buildList( structure ) );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

}; } ) ( jQuery );