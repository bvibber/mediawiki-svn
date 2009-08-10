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
 * These plugins provide extra functionality for interaction with textareas.
 */
( function( $ ) { $.fn.extend( {
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
 */
encapsulateSelection: function( pre, peri, post, ownline ) {
	/**
	 * Check if the selected text is the same as the insert text
	 */ 
	function checkSelectedText() {
		if ( !selText ) {
			selText = peri;
			isSample = true;
		} else if ( selText.charAt( selText.length - 1 ) == ' ' ) {
			// Exclude ending space char
			selText = selText.substring(0, selText.length - 1);
			post += ' '
		}
	}
	var e = this.jquery ? this[0] : this;
	var selText;
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
		selText = range.text;
		if ( ownline && range.moveStart ) {
			var range2 = document.selection.createRange();
			range2.collapse();
			range2.moveStart( 'character', -1 );
			// FIXME: Which check is correct?
			if ( range2.text != "\r" && range2.text != "\n" && range3.text != "" )
				pre = "\n" + pre;
			
			var range3 = document.selection.createRange();
			range3.collapse( false );
			range3.moveEnd( 'character', 1 );
			if ( range3.text != "\r" && range3.text != "\n" && range3.text != "" )
				post += "\n";
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
		selText = e.value.substring( startPos, endPos );
		checkSelectedText();
		if ( ownline ) {
			if ( startPos != 0 && e.value.charAt( startPos - 1 ) != "\n" )
				pre = "\n" + pre;
			if ( e.value.charAt( endPos ) != "\n" )
				post += "\n";
		}
		e.value = e.value.substring( 0, startPos ) + pre + selText + post +
			e.value.substring( endPos, e.value.length );
		if ( isSample ) {
			e.selectionStart = startPos + pre.length;
			e.selectionEnd = startPos + pre.length + selText.length;
		} else {
			e.selectionStart =
				startPos + pre.length + selText.length + post.length;
			e.selectionEnd = e.selectionStart;
		}
		e.scrollTop = textScroll;
	}
	$(this).trigger( 'encapsulateSelection', [ pre, peri, post, ownline ] );
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
		if($.browser.msie) {
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
					if ( preRange.
							compareEndPoints( "StartToEnd", preRange ) == 0 ) {
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
					if ( periRange.
							compareEndPoints( "StartToEnd", periRange ) == 0 ) {
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
					if ( postRange.
							compareEndPoints("StartToEnd", postRange) == 0 ) {
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
		if( nextSpace > lineLength && caret <= lineLength ) {
			charInLine = caret - lastSpaceInLine;
			row++;
		}
		return (
			$.os.name == 'mac' ? 13 : ( $.os.name == 'linux' ? 15 : 16 )
		) * row;
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
	});
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
(function($) {
$.wikiEditor = { 'modules': {}, 'instances': [] };
$.fn.wikiEditor = function() {

/* Initialization */

// The wikiEditor context is stored in the element, so when this function
// gets called again we can pick up where we left off
var context = $(this).data( 'context' );

/* API */

// The first time this is called, we expect context to be undefined, meaning
// the editing ui has not yet been, and still needs to be built, however each
// additional call after that is expected to be an API call, which contains a
// string as the first argument which corrosponds to a supported api call
if ( typeof context !== 'undefined' ) {
	// Since javascript gives arugments as an object, we need to convert them
	// so they can be used more easily
	arguments = $.makeArray( arguments );
	if ( arguments.length > 0 ) {
		// Handle API calls
		var call = arguments.shift();
		if ( call in context.api ) {
			context.api[call](
				context, arguments[0] == undefined ? {} : arguments[0]
			);
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

var instance = $.wikiEditor.instances.length;
context = {
	'$textarea': $(this), 'modules': {}, 'data': {}, 'instance': instance
};
$.wikiEditor.instances[instance] = $(this);

// Encapsulate the textarea with some containers for layout
$(this)
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' )
		.attr( 'id', 'wikiEditor-ui' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' )
		.attr( 'id', 'wikiEditor-ui-bottom' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' )
		.attr( 'id', 'wikiEditor-ui-text' ) );

// Get a reference to the outer container
context.$ui = $(this).parent().parent().parent();
context.$ui.after( $( '<div style="clear:both;"></div>' ) );
// Attach a container in the top
context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' )
	.attr( 'id', 'wikiEditor-ui-top' ) );
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
// Each browser seems to do this differently, so let's keep our editor
// consistent by allways starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// If there was a configuration passed, it's assumed to be for the addModule
// API call, so we can just send it on it's way right now
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	context.api.addModule( context, arguments[0] );
}
// Store the context for next time, and support chaining
return $(this).data( 'context', context );;

};})(jQuery);/**
 * TOC Module for wikiEditor
 */
(function($) { $.wikiEditor.modules.toc = {
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
		context.modules.$toc.css( 'width', '12em' );
		context.$ui.find( '.wikiEditor-ui-text' ).css(
			( $( 'body.rtl' ).size() ? 'marginLeft' : 'marginRight' ), '12em'
		);
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
					section < context.data.outline.length &&
					context.data.outline[section].position - 1 < position
				) {
					section++;
				}
				section = Math.max( 0, section );
			}
			context.modules.$toc.find( 'a.section-' + section )
				.addClass( 'currentSelection' );
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
								$(this).data( 'textbox' )
									.scrollToCaretPosition(
											$(this).data( 'position' )
									);
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
			// Use the lowest common denominator as the actual level
			var level = Math.min( startLevel, endLevel );
			text = $.trim( text.substr( level, text.length - ( level * 2 ) ) );
			// Add the heading data to the outline
			outline[h] = {
				'text': text,
				'position': position,
				'level': level,
				'index': h + 1
			};
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
			else if ( outline[i].level < lastLevel ) {
				nLevel -= Math.max( 1, lastLevel - outline[i].level );
			}
			outline[i].nLevel = nLevel;
			lastLevel = nLevel;
		}
		// Recursively build the structure and adds special item for section 0
		var structure = buildStructure( outline );
		structure.unshift(
			{ 'text': wgPageName.replace(/_/g, ' '), 'level': 1, 'index': 0, 'position': 0 }
		);
		context.modules.$toc.html( buildList( structure ) );
		// Cache the outline for later use
		context.data.outline = outline;
	}
}

};})(jQuery);/**
 * Toolbar module for wikiEditor
 */

(function($) { $.wikiEditor.modules.toolbar = {
/**
 * Path to images - this is a bit messy, and it would need to change if this
 * code (and images) gets moved into the core - or anywhere for that matter...
 */
imgPath: wgScriptPath +
	'/extensions/UsabilityInitiative/images/wikiEditor/toolbar/',
/**
 * API accessible functions
 */
api: {
	addToToolbar: function( context, data ) {
		//
	},
	modifyToolbar: function( context, data ) {
		//
	},
	removeFromToolbar: function( context, data ) {
		if ( typeof data.section == 'string' ) {
			var selector = 'div[rel=' + data.section + '].section';
			if ( typeof data.group == 'string' ) {
				selector += ' div[rel=' + data.group + '].group';
				if ( typeof data.tool == 'string' ) {
					selector += ' div[rel=' + data.tool + '].tool';
				}
			}
			context.modules.$toolbar.find( selector ).remove();
		}
	}
},
/**
 * Internally used functions
 */
fn: {
	// Wraps gM from js2, but allows raw text to supercede
	autoMsg: function( object, property ) {
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
	create: function( context, config ) {
		if ( '$toolbar' in context.modules ) {
			return;
		}
		context.modules.$toolbar = $( '<div></div>' )
			.addClass( 'wikiEditor-ui-toolbar' )
			.attr( 'id', 'wikiEditor-ui-toolbar' );
		$.wikiEditor.modules.toolbar.fn.build( context, config );
		context.$ui.find( '.wikiEditor-ui-top' )
			.append( context.modules.$toolbar );
	},
	/**
	 * Performs an operation based on parameters
	 * 
	 * @param {Object} context
	 * @param {Object} action
	 */
	doAction: function( context, action ) {
		switch ( action.type ) {
			case 'encapsulate':
				var parts = { 'pre': '', 'peri': '', 'post': '' };
				for ( part in parts ) {
					if ( part + 'Msg' in action.options ) {
						parts[part] = gM(
							action.options[part + 'Msg'],
							( action.options[part] || null )
						);
					} else {
						parts[part] = ( action.options[part] || '' )
					}
				}
				context.$textarea.encapsulateSelection(
					parts.pre, parts.peri, parts.post,
					action.options.ownline
				);
			break;
			default: break;
		}
	},
	buildSection: function( context, id, section ) {
		switch ( section.type ) {
			case 'toolbar':
				return $.wikiEditor.modules.toolbar.fn.buildToolbar(
					context, id, section
				);
			case 'booklet':
				return $.wikiEditor.modules.toolbar.fn.buildBooklet(
					context, id, section
				);
			default: return null;
		}
	},
	buildToolbar: function( context, id, toolbar ) {
		var $toolbar = $( '<div></div>' ).attr( {
			'class': 'toolbar section section-' + id,
			'rel': id
		} );
		if ( 'groups' in toolbar ) {
			for ( group in toolbar.groups ) {
				$toolbar.append(
					$.wikiEditor.modules.toolbar.fn.buildGroup(
						context, group, toolbar.groups[group]
					)
				);
			}
		}
		return $toolbar;
	},
	buildGroup: function( context, id, group ) {
		var $group = $( '<div></div>' ).attr( {
			'class': 'group group-' + id,
			'rel': id
		} );
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( group, 'label' );
		if ( label ) {
			$group.append(
				$( '<div></div>' ).text( label ).addClass( 'label' )
			)
		}
		if ( 'tools' in group ) {
			for ( tool in group.tools ) {
				$group.append(
					$.wikiEditor.modules.toolbar.fn.buildTool(
						context, tool, group.tools[tool]
					)
				);
			}
		}
		return $group;
	},
	buildTool: function( context, id, tool ) {
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
				$button = $( '<img />' ).attr( {
					'src': $.wikiEditor.modules.toolbar.imgPath + tool.icon,
					'alt': label,
					'title': label,
					'rel': id,
					'class': 'tool tool-' + id
				} );
				if ( 'action' in tool ) {
					$button
						.data( 'action', tool.action )
						.data( 'context', context )
						.click( function() {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).data( 'context' ),
								$(this).data( 'action' )
							);
							return false;
						} );
				}
				return $button;
			case 'select':
				var $select = $( '<select></select>' ).attr( {
					'rel': id,
					'class': 'tool tool-' + id
				} );
				$select.append( $( '<option></option>' ).text( label ) )
				if ( 'list' in tool ) {
					$select
						.data( 'list', tool.list )
						.data( 'context', context )
						.click( function() {
							var list = $(this).data( 'list' );
							var val = $(this).val();
							if ( val in list && 'action' in list[val] ) {
								$.wikiEditor.modules.toolbar.fn.doAction(
									$(this).data( 'context' ), list[val].action
								);
							}
							$(this)
								.find(":selected").attr( 'selected', false )
								.find(":first").attr( 'selected', true );
							return false;
						} );
					for ( option in tool.list ) {
						var optionLabel =
							$.wikiEditor.modules.toolbar.fn.autoMsg(
								tool.list[option], 'label'
							);
						$select.append(
							$( '<option></option>' )
								.text( optionLabel )
								.attr( 'value', option )
						);
					}
				}
				return $select;
			default: return null;
		}
	},
	buildBooklet: function( context, id, booklet ) {
		var selected = $.cookie(
			'wikiEditor-' + context.instance + '-booklet-' + id + '-page'
		);
		var $booklet = $( '<div></div>' ).attr( {
			'class': 'booklet section section-' + id,
			'rel': id
		} );
		var $pages = $( '<div></div>' ).attr( 'class', 'pages' );
		var $index = $( '<div></div>' ).attr( 'class', 'index' );
		if ( 'pages' in booklet ) {
			if ( !( selected in booklet.pages ) ) {
				selected = null;
			}
			for ( page in booklet.pages ) {
				if ( selected === null ) {
					selected = page;
				}
				var $page = $.wikiEditor.modules.toolbar.fn.buildPage(
					context, page, booklet.pages[page]
				);
				var $bookmark = $.wikiEditor.modules.toolbar.fn.buildBookmark(
					context, page, booklet.pages[page]
				);
				if ( selected == page ) {
					$page.show();
					$bookmark.addClass( 'current' );
				} else {
					$page.hide();
				}
				$pages.append( $page );
				$index.append( $bookmark );
			}
		}
		return $booklet.append( $index ).append( $pages );
	},
	buildBookmark: function( context, id, page ) {
		var label = $.wikiEditor.modules.toolbar.fn.autoMsg( page, 'label' );
		return $( '<div></div>' )
			.text( label )
			.attr( 'rel', id )
			.data( 'context', context )
			.click( function() {
				$(this)
					.parent()
					.parent()
					.find( '.page' )
					.hide();
				$(this)
					.parent()
					.parent()
					.find( '.page-' + $(this).attr( 'rel' ) )
					.show();
				$(this).siblings().removeClass( 'current' );
				$(this).addClass( 'current' );
				var section = $(this).parent().parent().attr( 'rel' );
				$.cookie(
					'wikiEditor-' + $(this).data( 'context' ).instance +
						'-booklet-' + section + '-page',
					$(this).attr( 'rel' )
				);
			} );
	},
	buildPage: function( context, id, page ) {
		var $page = $( '<div></div>' ).attr( {
			'class': 'page page-' + id,
			'rel': id
		} );
		switch( page.layout ) {
			case 'table':
				$page.addClass( 'page-table' );
				var $table = $( '<table></table>' ).attr( {
					'cellpadding': '0',
					'cellspacing': '0',
					'border': '0',
					'width': '100%',
					'class': 'table table-' + id
				} );
				if ( 'headings' in page ) {
					var $headings = $( '<tr></tr>' );
					for ( heading in page.headings ) {
						var content =
							$.wikiEditor.modules.toolbar.fn.autoMsg(
									page.headings[heading], 'content'
							);
						$headings.append(
							$( '<th></th>' ).text( content )
						);
					}
					$table.append( $headings );
				}
				if ( 'rows' in page ) {
					for ( row in page.rows ) {
						var $row = $( '<tr></tr>' );
						for ( cell in page.rows[row] ) {
							var $cell = $( '<td></td>' ).attr( {
								'class': 'cell cell-' + cell,
								'valign': 'top'
							} );
							var content =
								$.wikiEditor.modules.toolbar.fn.autoMsg(
										page.rows[row][cell], 'content'
								);
							$cell.append(
								$( '<span></span>' ).html( content )
							);
							$row.append( $cell );
						}
						$table.append( $row );
					}
				}
				$page.append( $table );
				break;
			case 'characters':
				$page.addClass( 'page-characters' );
				$characters = $( '<div></div>' );
				if ( 'language' in page ) {
					$characters.attr( 'lang', page.language );
				}
				if ( 'direction' in page ) {
					$characters.attr( 'dir', page.direction );
				}
				if ( 'characters' in page ) {
					for ( character in page.characters ) {
						var tool = page.characters[character];
						if ( typeof tool == 'string' ) {
							tool = {
								'label': tool,
								'action': {
									'type': 'encapsulate',
									'options': { 'pre': tool }
								}
							};
						} else if ( 0 in tool && 1 in tool ) {
							tool = {
								'label': tool[0],
								'action': {
									'type': 'encapsulate',
									'options': { 'pre': tool[1] }
								}
							};
						}
						if ( 'action' in tool && 'label' in tool ) {
							var $character = $( '<a></a>' )
								.attr( 'href', '#' )
								.text( tool.label )
								.data( 'context', context )
								.data( 'action', tool.action )
								.click( function() {
									$.wikiEditor.modules.toolbar.fn.doAction(
										$(this).data( 'context' ),
										$(this).data( 'action' )
									);
									return false;
								} );
							$characters.append( $character );
						}
					}
					$page.append( $characters );
				}
				break;
		}
		return $page;
	},
	build: function( context, config ) {
		var $tabs = $( '<div></div>' )
			.addClass( 'tabs' )
			.appendTo( context.modules.$toolbar );
		var $sections = $( '<div></div>' )
			.addClass( 'sections' )
			.appendTo( context.modules.$toolbar );
		context.modules.$toolbar.append(
			$( '<div></div>' ).addClass( 'break' )
		);
		var selected = $.cookie(
			'wikiEditor-' + context.instance + '-toolbar-section'
		);
		var sectionQueue = [];
		for ( section in config ) {
			if ( section == 'main' ) {
				context.modules.$toolbar.prepend(
					$.wikiEditor.modules.toolbar.fn.buildSection(
						context, section, config[section]
					)
				);
			} else {
				s = {
					'context': context,
					'$sections': $sections,
					'$tabs': $tabs,
					'section': section,
					'config': config[section],
					'selected': ( selected == section )
				};
				sectionQueue[sectionQueue.length] = s;
				s.$tabs.append(
					$( '<span></span>' )
						.attr( {
							'class': 'tab tab-' + s.section,
							'rel': s.section
						} )
						.append(
							$( '<a></a>' )
							.addClass( s.selected ? 'current' : null )
							.attr( 'href', '#' )
							.text(
								$.wikiEditor.modules.toolbar.fn.autoMsg(
									s.config, 'label'
								)
							)
							.data( 'context', s.context )
							.click( function() {
								var $section =
									$(this).data( 'context' ).$ui.find(
											'.section-' +
											$(this).parent().attr( 'rel' )
									);
								$(this).blur();
								var show = $section.css( 'display' ) == 'none';
								$section.parent().children().hide();
								$(this)
									.parent()
									.parent()
									.find( 'a' )
									.removeClass( 'current' );
								if ( show ) {
									$section.show();
									$(this).addClass( 'current' );
								}
								$.cookie(
									'wikiEditor-' +
										$(this).data( 'context' ).instance +
										'-toolbar-section',
									show ? $section.attr( 'rel' ) : null
								);
								return false;
							} )
						)
				);
			}
		}
		$.eachAsync( sectionQueue, {
			'bulk': 0,
			'end': function() {
				// HACK: Opera doesn't seem to want to redraw after these bits
				// are added to the DOM, so we can just FORCE it!
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', 'relative' );
			},
			'loop': function( i, s ) {
				s.$sections.append(
					$.wikiEditor.modules.toolbar.fn.buildSection(
						s.context, s.section, s.config
					)
					.css( 'display', s.selected ? 'block' : 'none' )
				);
				
			}
		} );
	}
}

};})(jQuery);