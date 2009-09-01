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
 * This plugin provides a generic way to add suggestions to a text box
 * Usage:
 *
 * Set options
 *     $('#textbox').suggestions({ option1: value1, option2: value2 });
 *     $('#textbox').suggestions( option, value );
 * Get option:
 *     value = $('#textbox').suggestions( option );
 * Initialize:
 *     $('#textbox').suggestions();
 * 
 * Available options:
 * animationDuration: How long (in ms) the animated growing of the results box
 *     should take (default: 200)
 * cancelPending(): Function called when any pending asynchronous suggestions
 *     fetches should be canceled (optional). Executed in the context of the
 *     textbox
 * delay: Number of ms to wait for the user to stop typing (default: 120)
 * fetch(query): Callback that should fetch suggestions and set the suggestions
 *     property (required). Executed in the context of the textbox
 * maxGrowFactor: Maximum width of the suggestions box as a factor of the width
 *     of the textbox (default: 2)
 * maxRows: Maximum number of suggestion rows to show
 * submitOnClick: If true, submit the form when a suggestion is clicked
 *     (default: false)
 * suggestions: Array of suggestions to display (default: [])
 * 
 */
(function($) {
$.fn.suggestions = function( param, param2 ) {
	/**
	 * Handle special keypresses (arrow keys and escape)
	 * @param key Key code
	 */
	function processKey( key ) {
		switch ( key ) {
			case 40:
				// Arrow down
				if ( conf._data.div.is( ':visible' ) ) {
					highlightResult( 'next', true );
				} else {
					// Load suggestions right now
					updateSuggestions( false );
				}
			break;
			case 38:
				// Arrow up
				if ( conf._data.div.is( ':visible' ) ) {
					highlightResult( 'prev', true );
				}
			break;
			case 27:
				// Escape
				conf._data.div.hide();
				restoreText();
				cancelPendingSuggestions();
			break;
			default:
				updateSuggestions( true );
		}
	}
	
	/**
	 * Restore the text the user originally typed in the textbox,
	 * before it was overwritten by highlightResult(). This restores the
	 * value the currently displayed suggestions are based on, rather than
	 * the value just before highlightResult() overwrote it; the former
	 * is arguably slightly more sensible.
	 */
	function restoreText() {
		conf._data.textbox.val( conf._data.prevText );
	}
	
	/**
	 * Ask the user-specified callback for new suggestions. Any previous
	 * delayed call to this function still pending will be canceled.
	 * If the value in the textbox hasn't changed since the last time
	 * suggestions were fetched, this function does nothing.
	 * @param delayed If true, delay this by the user-specified delay
	 */
	function updateSuggestions( delayed ) {
		// Cancel previous call
		if ( conf._data.timerID != null )
			clearTimeout( conf._data.timerID );
		if ( delayed )
			setTimeout( doUpdateSuggestions, conf.delay );
		else
			doUpdateSuggestions();
	}
	
	/**
	 * Delayed part of updateSuggestions()
	 * Don't call this, use updateSuggestions( false ) instead
	 */
	function doUpdateSuggestions() {
		if ( conf._data.textbox.val() == conf._data.prevText )
			// Value in textbox didn't change
			return;
		
		conf._data.prevText = conf._data.textbox.val();
		conf.fetch.call ( conf._data.textbox,
			conf._data.textbox.val() );
	}
	
	/**
	 * Called when the user changes the suggestions post-init.
	 * Typically happens asynchronously from conf.fetch()
	 */
	function suggestionsChanged() {
		conf._data.div.show();
		updateSuggestionsTable();
		fitContainer();
		trimResultText();
	}
	
	/**
	 * Cancel any delayed updateSuggestions() call and inform the user so
	 * they can cancel their result fetching if they use AJAX or something 
	 */
	function cancelPendingSuggestions() {
		if ( conf._data.timerID != null )
			clearTimeout( conf._data.timerID );
		conf.cancelPending.call( this );
	}
	
	/**
	 * Rebuild the suggestions table
	 */
	function updateSuggestionsTable() {
		// If there are no suggestions, hide the div
		if ( conf.suggestions.length == 0 ) {
			conf._data.div.hide();
			return;
		}
		
		var table = conf._data.div.children( 'table' );
		table.empty();
		for ( var i = 0; i < conf.suggestions.length; i++ ) {
			var td = $( '<td />' ) // FIXME: why use a span?
				.append( $( '<span />' ).text( conf.suggestions[i] ) );
				//.addClass( 'os-suggest-result' ); //FIXME: use descendant selector
			$( '<tr />' )
				.addClass( 'os-suggest-result' ) // FIXME: use descendant selector
				.attr( 'rel', i )
				.data( 'text', conf.suggestions[i] )
				.append( td )
				.appendTo( table );
		}
	}
	
	/**
	 * Make the container fit into the screen
	 */
	function fitContainer() {
		if ( conf._data.div.is( ':hidden' ) )
			return;
		
		// FIXME: Mysterious -20 from mwsuggest.js,
		// presumably to make room for a scrollbar
		var availableHeight = $( 'body' ).height() - (
			Math.round( conf._data.div.offset().top ) -
			$( document ).scrollTop() ) - 20;
		var rowHeight = conf._data.div.find( 'tr' ).outerHeight();
		var numRows = Math.floor( availableHeight / rowHeight );
		
		// Show at least 2 rows if there are multiple results
		if ( numRows < 2 && conf.suggestions.length >= 2 )
			numRows = 2;
		if ( numRows > conf.maxRows )
			numRows = conf.maxRows;
		
		var tableHeight = conf._data.div.find( 'table' ).outerHeight();
		if ( numRows * rowHeight < tableHeight ) {
			// The container is too small
			conf._data.div.height( numRows * rowHeight );
			conf._data.visibleResults = numRows;
		} else {
			// The container is possibly too large
			conf._data.div.height( tableHeight );
			conf._data.visibleResults = conf.suggestions.length;
		}
	}
	
	/**
	 * If there are results wider than the container, try to grow the
	 * container or trim them to end with "..."
	 */
	function trimResultText() {
		if ( conf._data.div.is( ':hidden' ) )
			return;
		
		// Try to grow the container so all results fit
		// Can't use each() here because the inner function can read
		// but not write maxWidth for some crazy reason
		var maxWidth = 0;
		var spans = conf._data.div.find( 'span' ).get();
		for ( var i = 0; i < spans.length; i++ )
			if ( $(spans[i]).outerWidth() > maxWidth )
				maxWidth = $(spans[i]).outerWidth();
		
		// FIXME: Some mysterious fixing going on here
		// FIXME: Left out Opera fix for now
		// FIXME: This doesn't check that the container won't run off the screen
		// FIXME: This should try growing to the left instead if no space on the right
		var fix = 0;
		if ( conf._data.visibleResults < conf.suggestions.length )
			fix = 20;
		//else
		//	fix = operaWidthFix();
		if ( fix < 4 )
			// FIXME: Make 4px configurable?
			fix = 4; // Always pad at least 4px
		maxWidth += fix;
		
		var textBoxWidth = conf._data.textbox.outerWidth();
		var factor = maxWidth / textBoxWidth;
		if ( factor > conf.maxGrowFactor ) 
			factor = conf.maxGrowFactor;
		if ( factor < 1 )
			// Don't shrink the container to be smaller
			// than the textbox
			factor = 1;
		var newWidth = Math.round( textBoxWidth * factor );
		if ( newWidth != conf._data.div.outerWidth() )
			conf._data.div.animate( { width: newWidth },
				conf.animationDuration );
		// FIXME: mwsuggest.js has this inside the if != block
		// but I don't think that's right
		newWidth -= fix;
		
		// If necessary, trim and add ...
		conf._data.div.find( 'tr' ).each( function() {
			var span = $(this).find( 'span' );
			if ( span.outerWidth() > newWidth ) {
				var span = $(this).find( 'span' );
				span.text( span.text() + '...' );
				
				// While it's still too wide and the last
				// iteration shrunk it, remove the character
				// before '...'
				while ( span.outerWidth() > newWidth && span.text().length > 3 ) {
					span.text( span.text().substring( 0,
						span.text().length - 4 ) + '...' );
				}
				$(this).attr( 'title', $(this).data( 'text' ) );
			}
		});
	}
	
	/**
	 * Get a jQuery object for the currently highlighted row
	 */
	function getHighlightedRow() {
		return conf._data.div.find( '.os-suggest-result-hl' );
	}
	
	/**
	 * Highlight a result in the results table
	 * @param result <tr> to highlight: jQuery object, or 'prev' or 'next'
	 * @param updateTextbox If true, put the suggestion in the textbox
	 */
	function highlightResult( result, updateTextbox ) {
		// TODO: Use our own class here
		var selected = getHighlightedRow();
		if ( !result.get || selected.get( 0 ) != result.get( 0 ) ) {
			if ( result == 'prev' ) {
				result = selected.prev();
			} else if ( result == 'next' ) {
				if ( selected.size() == 0 )
					// No item selected, go to the first one
					result = conf._data.div.find( 'tr:first' );
				else {
					result = selected.next();
					if ( result.size() == 0 )
						// We were at the last item, stay there
						result = selected;
				}
			}
			
			selected.removeClass( 'os-suggest-result-hl' );
			result.addClass( 'os-suggest-result-hl' );
		}
		
		if ( updateTextbox ) {
			if ( result.size() == 0 )
				restoreText();
			else
				conf._data.textbox.val( result.data( 'text' ) );
		}
		
		if ( result.size() > 0 && conf._data.visibleResults < conf.suggestions.length ) {
			// Not all suggestions are visible
			// Scroll if needed
			
			// height of a result row
			var rowHeight = result.outerHeight();
			// index of first visible element
			var first = conf._data.div.scrollTop() / rowHeight;  
			// index of last visible element
			var last = first + conf._data.visibleResults - 1;
			// index of element to scroll to
			var to = result.attr( 'rel' );
			
			if ( to < first )
				// Need to scroll up
				conf._data.div.scrollTop( to * rowHeight );
			else if ( result.attr( 'rel' ) > last )
				// Need to scroll down
				conf._data.div.scrollTop( ( to - conf._data.visibleResults + 1 ) * rowHeight );
		}
	}
	
	/**
	 * Initialize the widget
	 */
	function init() {
		if ( typeof conf != 'object' || typeof conf._data != 'undefined' )
			// Configuration not set or init already done
			return;
		
		// Set defaults
		if ( typeof conf.animationDuration == 'undefined' )
			conf.animationDuration = 200;
		if ( typeof conf.cancelPending != 'function' )
			conf.cancelPending = function() {};
		if ( typeof conf.delay == 'undefined' )
			conf.delay = 250;
		if ( typeof conf.maxGrowFactor == 'undefined' )
			conf.maxGrowFactor = 2;
		if ( typeof conf.maxRows == 'undefined' )
			conf.maxRows = 7;
		if ( typeof conf.submitOnClick == 'undefined' )
			conf.submitOnClick = false;
		if ( typeof conf.suggestions != 'object' )
			conf.suggestions = [];
		
		conf._data = {};
		conf._data.textbox = $(this);
		conf._data.timerID = null; // ID of running timer
		conf._data.prevText = null; // Text in textbox when suggestions were last fetched
		conf._data.visibleResults = 0; // Number of results visible without scrolling
		conf._data.mouseDownOn = $( [] ); // Suggestion the last mousedown event occured on
	
		// Create container div for suggestions
		conf._data.div = $( '<div />' )
			.addClass( 'os-suggest' ) //TODO: use own CSS
			.css( {
				top: Math.round( $(this).offset().top ) + this.offsetHeight,
				left: Math.round( $(this).offset().left ),
				width: $(this).outerWidth()
			})
			.hide()
			.appendTo( $( 'body' ) );
		
		// Create results table
		$( '<table />' )
			.addClass( 'os-suggest-results' ) // TODO: use descendant selector
			.width( $(this).outerWidth() ) // TODO: see if we need Opera width fix 
			.appendTo( conf._data.div );
		
		$(this)
			// Stop browser autocomplete from interfering
			.attr( 'autocomplete', 'off')
			.keydown( function( e ) {
				// Store key pressed to handle later
				conf._data.keypressed = (e.keyCode == undefined) ? e.which : e.keyCode;
				conf._data.keypressed_count = 0;
			})
			.keypress( function() {
				conf._data.keypressed_count++;
				processKey( conf._data.keypressed );
			})
			.keyup( function() {
				// Some browsers won't throw keypress() for
				// arrow keys. If we got a keydown and a keyup
				// without a keypress in between, solve that
				if (conf._data.keypressed_count == 0 )
					processKey( conf._data.keypressed );
			})
			.blur( function() {
				// When losing focus because of a mousedown
				// on a suggestion, don't hide the suggestions 
				if ( conf._data.mouseDownOn.size() > 0 )
					return;
				conf._data.div.hide();
				cancelPendingSuggestions();
			});
		
		conf._data.div
			.mouseover( function( e ) {
				var tr = $( e.target ).closest( '.os-suggest tr' );
				highlightResult( tr, false );
			})
			// Can't use click() because the container div is hidden
			// when the textbox loses focus. Instead, listen for a
			// mousedown followed by a mouseup on the same <tr>
			.mousedown( function( e ) {
				var tr = $( e.target ).closest( '.os-suggest tr' );
				conf._data.mouseDownOn = tr;
			})
			.mouseup( function( e ) {
				var tr = $( e.target ).closest( '.os-suggest tr' );
				var other = conf._data.mouseDownOn;
				conf._data.mouseDownOn = $( [] );
				if ( tr.get( 0 ) != other.get( 0 ) )
					return;
				 
				highlightResult( tr, true );
				conf._data.div.hide();
				conf._data.textbox.focus();
				if ( conf.submitOnClick )
					conf._data.textbox.closest( 'form' )
						.submit();
			});
	}
	
	function getProperty( prop ) {
		return ( param[0] == '_' ? undefined : conf[param] );
	}
	
	function setProperty( prop, value ) {
		if ( typeof conf == 'undefined' ) {
			$(this).data( 'suggestionsConfiguration', {} );
			conf = $(this).data( 'suggestionsConfiguration' );
		}
		if ( prop[0] != '_' )
			conf[prop] = value;
		if ( prop == 'suggestions' && conf._data )
			// Setting suggestions post-init
			suggestionsChanged();
	}
	
	
	// Body of suggestions() starts here
	var conf = $(this).data( 'suggestionsConfiguration' );
	if ( typeof param == 'object' )
		return this.each( function() {
			// Bulk-set properties
			for ( key in param ) {
				// Make sure that this in setProperty()
				// is set right
				setProperty.call( this, key, param[key] );
			}
		});
	else if ( typeof param == 'string' ) {
		if ( typeof param2 != 'undefined' )
			return this.each( function() {
				setProperty( param, param2 );
			});
		else
			return getProperty( param );
	} else if ( typeof param != 'undefined' )
		// Incorrect usage, ignore
		return this;
	
	// No parameters given, initialize
	return this.each( init );
};})(jQuery);
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
	open: function( context, data ) {
		if ( data.dialog in $.wikiEditor.modules.dialogs.modules ) {
			$( '#' + $.wikiEditor.modules.dialogs.modules[data.dialog].id ).dialog( 'open' );
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
		// Accept additional modules
		if ( 'modules' in config ) {
			for ( module in config.modules ) {
				$.wikiEditor.modules.dialogs.modules[module] = config.modules[module];
			}
		}
		// Build out modules immediately
		for ( module in $.wikiEditor.modules.dialogs.modules ) {
			var module = $.wikiEditor.modules.dialogs.modules[module];
			if ( $( '#' + module.id ).size() == 0 ) {
				var configuration = module.dialog;
				// Add some stuff to configuration
				configuration.bgiframe = true;
				configuration.autoOpen = false;
				configuration.modal = true;
				configuration.title = gM( module.titleMsg );
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
					.dialog( configuration );
			}
		}
	}
},
/**
 * Stock modules
 */
modules: {
	'insert-link': {
		titleMsg: 'edittoolbar-tool-link-title',
		id: 'edittoolbar-link-dialog',
		// TODO: break this line
		html: '<div id="edittoolbar-link-tabs"><ul><li><a href="#edittoolbar-link-dialog-tab-int" rel="edittoolbar-tool-link-int"></a></li><li><a href="#edittoolbar-link-dialog-tab-ext" rel="edittoolbar-tool-link-ext"></a></li></ul><div id="edittoolbar-link-dialog-tab-int"><form><label for="edittoolbar-link-int-target" rel="edittoolbar-tool-link-int-target"></label> <input type="text" id="edittoolbar-link-int-target" /> <div id="edittoolbar-link-int-target-status" style="display: inline;"></div><br /><label for="edittoolbar-link-int-text" rel="edittoolbar-tool-link-int-text"></label> <input type="text" id="edittoolbar-link-int-text" /></form></div><div id="edittoolbar-link-dialog-tab-ext"><form><label for="edittoolbar-link-ext-target" rel="edittoolbar-tool-link-ext-target"></label> <input type="text" id="edittoolbar-link-ext-target" /><br /><label for="edittoolbar-link-ext-text" rel="edittoolbar-tool-link-ext-text"></label> <input type="text" id="edittoolbar-link-ext-text" /></form></div></div>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
			$( '#edittoolbar-link-tabs' ).tabs();
			
			// Link int-target and int-text fields
			// This means mirroring the contents of int-target in int-text
			// as long as int-text itself hasn't been changed by the user
			$( '#edittoolbar-link-int-target' ).bind( 'keypress paste', function() {
				// $(this).val() is the old value, before the keypress
				if ( $( '#edittoolbar-link-int-text' ).data( 'untouched' ) )
					// Defer this until $(this).val() has been updated
					setTimeout("$( '#edittoolbar-link-int-text' ).val( $( '#edittoolbar-link-int-target' ).val() );", 0);
			});
			$( '#edittoolbar-link-int-text' ).bind( 'keypress paste', function() {
				$(this).data( 'untouched', false );
			});
			$( '#edittoolbar-link-ext-target' ).val( 'http://' );
			
			// Page existence check widget
			var existsImg = $.wikiEditor.modules.toolbar.imgPath + 'insert-link-exists.png';
			var notexistsImg = $.wikiEditor.modules.toolbar.imgPath + 'insert-link-notexists.png';
			var invalidImg = $.wikiEditor.modules.toolbar.imgPath + 'insert-link-invalid.png';
			var loadingImg = $.wikiEditor.modules.toolbar.imgPath + 'loading.gif';
			var existsMsg = gM( 'edittoolbar-tool-link-int-target-status-exists' );
			var notexistsMsg = gM( 'edittoolbar-tool-link-int-target-status-notexists' );
			var invalidMsg = gM( 'edittoolbar-tool-link-int-target-status-invalid' );
			var loadingMsg = gM( 'edittoolbar-tool-link-int-target-status-loading' );
			$( '#edittoolbar-link-int-target-status' )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-exists',
					'src': existsImg,
					'alt': existsMsg,
					'title': existsMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-notexists',
					'src': notexistsImg,
					'alt': notexistsMsg,
					'title': notexistsMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-invalid',
					'src': invalidImg,
					'alt': invalidMsg,
					'title': invalidMsg } ) )
				.append( $( '<img />' ).attr( {
					'id': 'edittoolbar-link-int-target-status-loading',
					'src': loadingImg,
					'alt': loadingMsg,
					'title': loadingMsg } ) )
				.data( 'cache', {} )
				.children().hide();
			
			function updateExistence( target ) {
				function updateWidget( status ) {
					$( '#edittoolbar-link-int-target-status' ).children().hide();
					$( '#edittoolbar-link-int-target-status-' + status ).show();
				}
				
				// Abort previous request
				var request = $( '#edittoolbar-link-int-target-status' ).data( 'request' );
				if ( request )
					request.abort();
				
				var target = $( '#edittoolbar-link-int-target' ).val();
				var cache = $( '#edittoolbar-link-int-target-status' ).data( 'cache' );
				if ( cache[target] ) {
					updateWidget( cache[target] );
					return;
				}
				
				if ( target == '' ) {
					// Hide the widget when the textbox is empty
					$( '#edittoolbar-link-int-target-status' ).children().hide();
					return;
				}
				if ( target.indexOf( '|' ) != -1 ) {
					// Title contains | , which means it's invalid
					// but confuses the API. Show invalid and bypass API
					updateWidget( 'invalid' );
					return;
				}
				
				updateWidget( 'loading' );
				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					dataType: 'json',
					data: {
						'action': 'query',
						'indexpageids': '',
						'titles': target,
						'format': 'json'
					},
					success: function( data ) {
						// TODO: What happens if data.query.pageids is undefined?
						var page = data.query.pages[data.query.pageids[0]];
						var status = 'exists';
						if ( typeof page.missing != 'undefined' )
							status = 'notexists';
						else if ( typeof page.invalid != 'undefined' )
							status = 'invalid';
						
						cache[target] = status;
						updateWidget( status );
					}
				});
				// Save request object so it can be aborted if necessary
				$( '#edittoolbar-link-int-target-status' ).data( 'request', request );	
			}
			
			$( '#edittoolbar-link-int-target' ).bind( 'keypress paste', function() {
				// Cancel the running timer if applicable
				if ( typeof $(this).data( 'timerID' ) != 'undefined' )
					clearTimeout( $(this).data( 'timerID' ) );
				
				// Delay fetch for a while
				// FIXME: Make 250 configurable elsewhere
				var timerID = setTimeout( updateExistence, 250 );
				$(this).data( 'timerID', timerID );
			}).change( function() {
				// Cancel the running timer if applicable
				if ( typeof $(this).data( 'timerID' ) != 'undefined' )
					clearTimeout( $(this).data( 'timerID' ) );
				
				// Fetch right now
				updateExistence();
			});
		},
		dialog: {
			width: 550, // FIXME: autoresize width
			buttons: {
				'edittoolbar-tool-link-insert': function() {
					function escapeInternalText( s ) {
						return s.replace( /(]{2,})/g, '<nowiki>$1</nowiki>' );
					}
					function escapeExternalTarget( s ) {
						return s.replace( / /g, '%20' )
							.replace( /]/g, '%5D' );
					}
					function escapeExternalText( s ) {
						return s.replace( /(]+)/g, '<nowiki>$1</nowiki>' );
					}
					var insertText = '';
					var whitespace = [ '', '' ];
					switch ( $( '#edittoolbar-link-tabs' ).tabs( 'option', 'selected' ) ) {
						case 0: // Internal link
							// FIXME: Exactly how fragile is this?
							if ( $( '#edittoolbar-link-int-target-status-invalid' ).is( ':visible' ) ) {
								// Refuse to add links to invalid titles
								alert( gM( 'edittoolbar-tool-link-int-invalid' ) );
								return;
							}
							var target = $( '#edittoolbar-link-int-target' ).val();
							var text = $( '#edittoolbar-link-int-text' ).val();
							whitespace = $( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace' );
							if ( target == text )
								insertText = '[[' + target + ']]';
							else
								insertText = '[[' + target + '|' + escapeInternalText( text ) + ']]';
						break;
						case 1:
							var target = $( '#edittoolbar-link-ext-target' ).val();
							var text = $( '#edittoolbar-link-ext-text' ).val();
							var escTarget = escapeExternalTarget( target );
							var escText = escapeExternalText( text );
							whitespace = $( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace' );
							if ( escTarget == escText )
								insertText = escTarget;
							else if ( text == '' )
								insertText = '[' + escTarget + ']';
							else
								insertText = '[' + escTarget + ' ' + escText + ']';
						break;
					}
					// Preserve whitespace in selection when replacing
					insertText = whitespace[0] + insertText + whitespace[1];
					$.wikiEditor.modules.toolbar.fn.doAction( $(this).data( 'context' ), {
						type: 'replace',
						options: {
							pre: insertText
						}
					}, $(this) );
					$(this).dialog( 'close' );
				},
				'edittoolbar-tool-link-cancel': function() {
					$(this).dialog( 'close' );
				}
			},
			open: function() {
				// Smart pre-fill text fields
				var selection = $(this).data( 'context' ).$textarea.getSelection();
					$( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace', [ '', '' ] );
					$( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace', [ '', '' ] );
				if ( selection != '' ) {
					var inttext, inttarget, exttext, exttarget;
					var matches;
					var tab = -1;
					if ( ( matches = selection.match( /^(\s*)\[\[([^\]\|]+)(\|([^\]\|]*))?\]\](\s*)$/ ) ) ) {
						// [[foo|bar]] or [[foo]]
						inttarget = matches[2];
						inttext = ( matches[4] ? matches[4] : matches[2] );
						tab = 0;
						// Preserve whitespace when replacing
						$( '#edittoolbar-link-dialog-tab-int' ).data( 'whitespace', [ matches[1], matches[5] ] );
					} else if ( ( matches = selection.match( /^(\s*)\[([^\] ]+)( ([^\]]+))?\](\s*)$/ ) ) ) {
						// [http://www.example.com foo] or [http://www.example.com]
						exttarget = matches[2];
						exttext = ( matches[4] ? matches[4] : '' );
						tab = 1;
						// Preserve whitespace when replacing
						$( '#edittoolbar-link-dialog-tab-ext' ).data( 'whitespace', [ matches[1], matches[5] ] );
					} else {
						inttarget = inttext = exttext = selection;
						exttarget = 'http://';
					}
					
					// val() doesn't trigger the change event, so let's do that ourselves
					if ( typeof inttext != 'undefined' )
						$( '#edittoolbar-link-int-text' ).val( inttext ).change();
					if ( typeof inttarget != 'undefined' )
						$( '#edittoolbar-link-int-target' ).val( inttarget ).change();
					if ( typeof exttext != 'undefined' )
						$( '#edittoolbar-link-ext-text' ).val( exttext ).change();
					if ( typeof exttarget != 'undefined' )
						$( '#edittoolbar-link-ext-target' ).val( exttarget ).change();
					if ( tab != -1 )
						$( '#edittoolbar-link-tabs' ).tabs( 'select', tab );
				}
				$( '#edittoolbar-link-int-text' ).data( 'untouched',
					$( '#edittoolbar-link-int-text' ).val() == $( '#edittoolbar-link-int-target' ).val()
				);
			}
		}
	},
	'insert-table': {
		titleMsg: 'edittoolbar-tool-table-title',
		id: 'edittoolbar-table-dialog',
		html: '<form><fieldset><legend rel="edittoolbar-tool-table-dimensions"></legend>' +
			'<table><tr><td class="label"><label for="edittoolbar-table-dimensions-columns"' +
			' rel="edittoolbar-tool-table-dimensions-columns"></label></td>' +
			'<td><input type="text" id="edittoolbar-table-dimensions-columns" size="2" /></td>' +
			'<td class="label"><label for="edittoolbar-table-dimensions-rows"' +
			' rel="edittoolbar-tool-table-dimensions-rows"></label></td>' +
			'<td><input type="text" id="edittoolbar-table-dimensions-rows" size="2" /></td></tr>' +
			'<tr><td class="label"><label for="edittoolbar-table-dimensions-headercolumns"' +
			' rel="edittoolbar-tool-table-dimensions-headercolumns"></label></td>' +
			'<td><input type="text" id="edittoolbar-table-dimensions-headercolumns" size="2" /></td>' +
			'<td class="label"><label for="edittoolbar-table-dimensions-headerrows"' +
			' rel="edittoolbar-tool-table-dimensions-headerrows"></label></td>' +
			'<td><input type="text" id="edittoolbar-table-dimensions-headerrows" size="2" /></td></tr>' +
			'</table></fieldset></form>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
		},
		dialog: {
			width: 350, // FIXME: autoresize
			buttons: {
				'edittoolbar-tool-table-insert': function() {
					var rows = parseInt( $( '#edittoolbar-table-dimensions-rows' ).val() );
					var cols = parseInt( $( '#edittoolbar-table-dimensions-columns' ).val() );
					var hrows = parseInt( $( '#edittoolbar-table-dimensions-headerrows' ).val() );
					var hcols = parseInt( $( '#edittoolbar-table-dimensions-headercolumns' ).val() );
					
					var table = "{|\n";
					for ( var r = 0; r < rows; r++ ) {
						table += "|-\n";
						for ( var c = 0; c < cols; c++ ) {
							var isHeader = ( r < hrows || c < hcols );
							var delim = isHeader ? '!' : '|';
							if ( c > 0 )
								delim += delim;
							table += delim + ' ' +
								gM( isHeader ?
									'edittoolbar-tool-table-example-header' :
									'edittoolbar-tool-table-example',
									[ r + 1, c + 1 ] ) + ' ';
						}
						// Replace trailing space by newline
						// table[table.length - 1] is read-only
						table = table.substr( 0, table.length - 1 ) + "\n";
					}
					table += "|}";
					$.wikiEditor.modules.toolbar.fn.doAction(
						$(this).data( 'context' ), {
							type: 'encapsulate',
							options: {
								pre: table,
								ownline: true
							}
						}, $(this) );
					$(this).dialog( 'close' );
				},
				'edittoolbar-tool-table-cancel': function() {
					$(this).dialog( 'close' );
				}
			}
		}
	},
	'search-and-replace': {
		titleMsg: 'edittoolbar-tool-replace-title',
		id: 'edittoolbar-replace-dialog',
		// TODO: break this line
		html: '<form><fieldset><label for="edittoolbar-replace-search" rel="edittoolbar-tool-replace-search"></label> <input type="text" id="edittoolbar-replace-search" /><br /><label for="edittoolbar-replace-replace" rel="edittoolbar-tool-replace-replace"></label> <input type="text" id="edittoolbar-replace-replace" /><br /><input type="checkbox" id="edittoolbar-replace-case" /><label for="edittoolbar-replace-case" rel="edittoolbar-tool-replace-case"></label><br /><input type="checkbox" id="edittoolbar-replace-regex" /><label for="edittoolbar-replace-regex" rel="edittoolbar-tool-replace-regex"></label><br /><input type="checkbox" id="edittoolbar-replace-all" /><label for="edittoolbar-replace-all" rel="edittoolbar-tool-replace-all"></label></fieldset></form>',
		init: function() {
			$(this).find( '[rel]' ).each( function() {
				$(this).text( gM( $(this).attr( 'rel' ) ) );
			});
		},
		dialog: {
			width: 350, // FIXME: autoresize width
			buttons: {
				'edittoolbar-tool-replace-button': function() {
					var searchStr = $( '#edittoolbar-replace-search' ).val();
					var replaceStr = $( '#edittoolbar-replace-replace' ).val();
					var flags = '';
					if ( !$( '#edittoolbar-replace-case' ).is( ':checked' ) )
						flags += 'i';
					if ( $( '#edittoolbar-replace-all' ).is( ':checked' ) )
						flags += 'g';
					if ( !$( '#edittoolbar-replace-regex' ).is( ':checked' ) )
						searchStr = RegExp.escape( searchStr );
					var regex = new RegExp( searchStr, flags );
					var $textarea = $(this).data( 'context' ).$textarea;
					if ( !$textarea.val().match( regex ) )
						alert( gM( 'edittoolbar-tool-replace-nomatch' ) );
					else
						$textarea.val( $textarea.val().replace( regex, replaceStr ) );
					// TODO: Hook for wikEd
				},
				'edittoolbar-tool-replace-close': function() {
					$(this).dialog( 'close' );
				}
			}
		}
	}
}

}; } ) ( jQuery );/**
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
	}
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
$.fn.wikiEditor = function() {

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'context' );

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
return $(this).data( 'context', context );

};})(jQuery);/**
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
			.bind( 'mouseup scrollToPosition',
				function( event ) {
					var context = $(this).data( 'context' );
					$(this).eachAsync( {
						bulk: 0,
						loop: function() {
							$.wikiEditor.modules.toc.fn.update( context );
						}
					} );
				}
			);
	},
	/**
	 * Highlight the section the cursor is currently within
	 * 
	 * @param {Object} context
	 */
	update: function( context ) {
		context.modules.$toc.find( 'a' ).removeClass( 'currentSelection' );
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
				offset = position;
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

}; } ) ( jQuery );/**
 * Toolbar module for wikiEditor
 */
( function( $ ) { $.wikiEditor.modules.toolbar = {

/**
 * Path to images - this is a bit messy, and it would need to change if
 * this code (and images) gets moved into the core - or anywhere for
 * that matter...
 */
imgPath : wgScriptPath + '/extensions/UsabilityInitiative/images/wikiEditor/toolbar/',
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
	removeFromToolbar : function( context, data ) {
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
			context.modules.$toolbar.find( target ).remove();
		}
	}
},
/**
 * Internally used functions
 */
fn : {
	// Wraps gM from js2, but allows raw text to supercede
	autoMsg : function( object, property ) {
		// Accept array of possible properties, of which the first one found will be used
		if ( typeof property == 'array' ) {
			for ( i in property ) {
				if ( property[i] in object ) {
					property = property[i];
					break;
				} else if ( property[i] + 'Msg' in object ) {
					property = property[i] + 'Msg';
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
				context.$textarea.wikiEditor( 'open', { 'dialog': action.module } );
				break;
			default: break;
		}
	},
	buildGroup : function( context, id, group ) {
		var $group = $( '<div />' ).attr( { 'class' : 'group group-' + id, 'rel' : id } );
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( group, 'label' );
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
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( tool, 'label' );
		switch ( tool.type ) {
			case 'button':
				var src = tool.icon;
				if ( src.indexOf( 'http://' ) !== 0 && src.indexOf( 'https://' ) !== 0 ) {
					src = $.wikiEditor.modules.toolbar.imgPath + src;
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
						var optionLabel = $.wikiEditor.modules.toolbar.fn.autoMsg( tool.list[option], 'label' );
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
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( page,
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
			html += '<th>' + $.wikiEditor.modules.toolbar.fn.autoMsg( headings[heading], ['html', 'text'] ) + '</th>';
		}
		return html;
	},
	buildRow : function( context, row ) {
		var html = '<tr>';
		for ( cell in row ) {
			html += '<td class="cell cell-' + cell + '" valign="top"><span>' +
				$.wikiEditor.modules.toolbar.fn.autoMsg( row[cell], ['html', 'text'] ) + '</span></td>';
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
				.text( $.wikiEditor.modules.toolbar.fn.autoMsg( section, 'label' ) )
				.data( 'context', context )
				.click( function() {
					var $section =
						$(this).data( 'context' ).$ui.find( '.section-' + $(this).parent().attr( 'rel' ) );
					$(this).blur();
					var show = $section.css( 'display' ) == 'none';
					$section.parent().children().hide();
					$(this).parent().parent().find( 'a' ).removeClass( 'current' );
					if ( show ) {
						$section.show();
						$(this).addClass( 'current' );
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
