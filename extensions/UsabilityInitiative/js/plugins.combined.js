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
 */
encapsulateSelection: function( pre, peri, post ) {
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
	if ( document.selection  && document.selection.createRange ) {
		// IE/Opera
		if ( document.documentElement && document.documentElement.scrollTop ) {
			var winScroll = document.documentElement.scrollTop;
		} else if ( document.body ) {
			var winScroll = document.body.scrollTop;
		}
		e.focus();
		var range = document.selection.createRange();
		selText = range.text;
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
		e.focus();
		var startPos = e.selectionStart;
		var endPos = e.selectionEnd;
		selText = e.value.substring( startPos, endPos );
		checkSelectedText();
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
	$(this).trigger( 'encapsulateSelection' );
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
			range = document.selection.createRange();
			oldPos = $(this).bytePos();
			goBack = false;
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
(function($) { $.wikiEditor = { 'modules': {} }; $.fn.wikiEditor = function() {

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
			context.api[call]( arguments );
		}
		// Store the context for next time and return
		return $(this).data( 'context', context );
	}
	// Nothing to do, just return
	return $(this);
}

/* Construction */

context = { '$textarea': $(this), 'modules': {}, 'data': {} };
// Encapsulate the textarea with some containers for layout
$(this)
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
	.wrap( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
// Get a refrence to the outter container
context.$ui = $(this).parent().parent().parent();
// Attach a container in the top
context.$ui.prepend( $( '<div></div>' ).addClass( 'wikiEditor-ui-top' ) );
// Create a set of standard methods for internal and external use
context.api = {
	addModule: function() {
		if ( arguments.length >= 1 && arguments[0].length >= 1 ) {
			var module = arguments[0][0];
			var configuration = ( arguments[0][1] ? arguments[0][1] : {} );
			// Check if the module is supported and that there's a create
			// method available for it
			if (
				module in $.wikiEditor.modules &&
				'create' in $.wikiEditor.modules[module]
			) {
				$.wikiEditor.modules[module].create( context, configuration );
			}
		}
	}
};
// Each browser seems to do this differently, so let's keep our editor
// consistent by allways starting at the begining
context.$textarea.scrollToCaretPosition( 0 );
// If there was a configuration passed, we can get started adding
// modules right away - which is done using the same API that could be used
// explicitly by the user
if ( arguments.length > 0 && typeof arguments[0] == 'object' ) {
	if ( 'modules' in arguments[0] ) {
		for ( module in arguments[0].modules ) {
			context.api.addModule( [module, arguments[0].modules[module]] );
		}
	}
}
// Store the context for next time, and support chaining
return $(this).data( 'context', context );;

};})(jQuery);/**
 * TOC Module for wikiEditor
 */
(function($) { $.wikiEditor.modules.toc = {
/**
 * Creates a table of contents module within a wikiEditor
 * 
 * @param {Object} context Context object of editor to create module in
 * @param {Object} configuration Configuration object to create module from
 */
create: function( context, configuration ) {
	if ( '$toc' in context.modules ) {
		return;
	}
	context.modules.$toc = $( '<div></div>' )
		.addClass( 'wikiEditor-ui-toc' );
	$.wikiEditor.modules.toc.build( context, configuration );
	context.$ui.find( '.wikiEditor-ui-bottom' )
		.append( context.modules.$toc );
	context.modules.$toc.height(
		context.$ui.find( '.wikiEditor-ui-bottom' ).height()
	);
	// Make some css modifications to make room for the toc on the right...
	// Perhaps this could be configurable?
	context.modules.$toc.css( 'width', '12em' );
	context.$ui.find( '.wikiEditor-ui-text' ).css( 'marginRight', '12em' );
	// Add the TOC to the document
	$.wikiEditor.modules.toc.build( context );
	$.wikiEditor.modules.toc.update( context );
	context.$textarea
		.bind( 'keyup encapsulateSelection',
			function( event ) {
				var context = $(this).data( 'context' );
				$(this).eachAsync( {
					bulk: 0,
					loop: function() {
						$.wikiEditor.modules.toc.build( context );
						$.wikiEditor.modules.toc.update( context );
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
						$.wikiEditor.modules.toc.update( context );
					}
				} );
			}
		);
},
/**
 * Highlight the section the cursor is currently within
 * 
 * @param target jQuery selection of element of containers with links to update
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
 * @param {Object} configuration
 * @param {String} editorId
 */
build: function( context, configuration, editorId ) {
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
		{ 'text': wgTitle, 'level': 1, 'index': 0, 'position': 0 }
	);
	context.modules.$toc.html( buildList( structure ) );
	// Cache the outline for later use
	context.data.outline = outline;
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
 * Creates a toolbar module within a wikiEditor
 * 
 * @param {Object} context Context object of editor to create module in
 * @param {Object} configuration Configuration object to create module from
 */
create: function( context, configuration ) {
	if ( '$toolbar' in context.modules ) {
		return;
	}
	context.modules.$toolbar = $( '<div></div>' )
		.addClass( 'wikiEditor-ui-toolbar' );
	$.wikiEditor.modules.toolbar.build( context, configuration );
	context.$ui.find( '.wikiEditor-ui-top' )
		.append( context.modules.$toolbar );
},
/**
 * Performs an operation based on parameters
 * 
 * @param {Object} action
 */
performAction: function( context, action ) {
	switch ( action.type) {
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
				parts.pre, parts.peri, parts.post
			);
		break;
		default: break;
	}
},
/**
 * Adds a toolbar section to a containing div
 * 
 * @param {Object} $section Container to add section content to
 * @param {Object} section Configuration to build toolbar from
 * @param {String} sectionId Unique identifier of this section
 */
addSection: function( context, $section, section, sectionId ) {
	/**
	 * Wraps performAction with tool specific UI interaction
	 */
	var useTool = function() {
		var tool = $(this).data( 'tool' );
		if ( 'type' in tool ) {
			switch ( tool.type ) {
				case 'button':
				case 'link':
					if ( 'action' in tool ) {
						$.wikiEditor.modules.toolbar.performAction(
							context, tool.action
						);
					}
					break;
				case 'select':
					if ( 'list' in tool && $(this).val() in tool.list ) {
						$.wikiEditor.modules.toolbar.performAction(
							context, tool.list[$(this).val()].action
						);
					}
					$(this).find(":selected").attr( 'selected', false );
					$(this).find(":first").attr( 'selected', true );
					break;
			}
		}
		return false;
	}
	/**
	 * Adds tools to a group
	 * 
	 * @param {Object} $group jQuery selection to add tools to
	 * @param {Object} tools Configurations for tools
	 * @param {String} sectionId Unique ID of section
	 */
	function addTools( $group, tools, sectionId ) {
		for ( tool in section.groups[group].tools ) {
			// Filters are the input to a jQuery selector. The tool will
			// only be shown if the selection contains one or more elements
			if ( 'filters' in tools[tool] ) {
				var skip = false;
				for ( filter in tools[tool].filters ) {
					if ( $( tools[tool].filters[filter] ).size() == 0 ) {
						skip = true;
					}
				}
				if ( skip ) {
					continue;
				}
			}
			var label = msg( tools[tool], 'label' );
			switch ( tools[tool].type ) {
				case 'button':
					$group.append(
						$( '<input />' )
						.attr( {
							'src': $.wikiEditor.modules.toolbar.imgPath +
								tools[tool].icon,
							'alt': label,
							'title': label,
							'type': 'image'
						} )
						.addClass( 'tool' )
						.addClass( 'tool-' + tool )
						.data( 'tool', tools[tool] )
						.click( useTool )
					);
					break;
				case 'select':
					var $select = $( '<select></select>' )
						.data( 'tool', tools[tool] )
						.change( useTool )
						.append( $( '<option></option>' ).text( label ) )
						.appendTo( $group );
					for ( option in tools[tool].list ) {
						$select.append(
							$( '<option></option>' )
								.text(
									msg( tools[tool].list[option], 'label' )
								)
								.attr( 'value', option )
						);
					}
					break;
			}
		}
	}
	/**
	 * Adds pages to a booklet
	 * 
	 * @param {Object} $index jQuery selection to add index entry to
	 * @param {Object} $pages jQuery selection to add pages to
	 * @param {Object} pages Configurations for pages
	 * @param {String} sectionId Unique ID of section
	 */
	function addPages( $index, $pages, pages, sectionId ) {
		var selected = $.cookie( sectionId ); 
		// The pages may have changed since the user was last here, so we
		// must check that the page they want to default to still exists
		if ( !( selected in pages ) ) {
			selected = null;
		}
		for ( page in pages ) {
			// If there's no layout property, we can just skip over this one
			if ( !( 'layout' in pages[page] ) ) {
				continue;
			}
			// When no page state is present, deafult to the first page
			if ( selected == null ) {
				selected = page;
			}
			// Add an entry to the index of pages so the user can navigate
			// from one to another
			$index.append(
				$( '<div></div>' )
					.attr( 'class', page === selected ? 'current' : null )
					.text( msg( pages[page], 'label' ) )
					.data( 'page', page )
					.data( 'sectionId', sectionId )
					.click( function() {
						$(this)
							.parent()
							.parent()
							.find( '.page' )
							.hide()
							.end()
							.parent()
							.find( 'div' )
							.removeClass( 'current' )
							.end()
							.parent()
							.parent()
							.find( '.page-' + $(this).data( 'page' ) )
							.show();
						$(this).addClass( 'current' );
						// Store the state each time the user changes pages
						$.cookie(
							$(this).data( 'sectionId'),
							$(this).data( 'page' )
						);
					} )
			);
			// Add the content of the page and only show the selected one
			var $page = $( '<div></div>' )
				.addClass( 'page' )
				.addClass( 'page-' + page )
				.css( 'display', page == selected ? 'block' : 'none' )
				.appendTo( $pages );
			// Depending on the layout, we can render different page types
			switch ( pages[page].layout ) {
				case 'table':
					var $table = $( '<table></table>' )
						.attr( {
							'cellpadding': '0',
							'cellspacing': '0',
							'border': '0',
							'width': '100%'
						} )
						.appendTo( $page );
					if (
						'headings' in pages[page] &&
						typeof pages[page].headings == 'object'
					) {
						var $headings = $( '<tr></tr>' ).appendTo( $table );
						for ( heading in pages[page].headings ) {
							var content = msg(
								pages[page].headings[heading], 'content'
							);
							$( '<th></th>' )
								.text( content )
								.appendTo( $headings );
						}
					}
					if (
						'rows' in pages[page] &&
						typeof pages[page].rows == 'object'
					) {
						for ( row in pages[page].rows ) {
							var $row = $( '<tr></tr>' ).appendTo( $table );
							for ( cell in pages[page].rows[row] ) {
								var content = msg(
									pages[page].rows[row][cell], 'content'
								);
								$( '<td></td>' )
									.addClass( cell )
									.attr( 'valign', 'top' )
									.append(
										$( '<span></span>' ).html( content )
									)
									.appendTo( $row );
							}
						}
					}
				break;
				case 'characters':
					var $characters = $( '<div></div>' )
						.attr( pages[page].attributes )
						.css( pages[page].styles )
						.appendTo( $page );
					if (
						'characters' in pages[page] &&
						typeof pages[page].characters == 'object'
					) {
						for ( character in pages[page].characters ) {
							var char = pages[page].characters[character];
							var tool = {};
							/*
							 * The contents of char may be a string, or an
							 * object. If it's a string the string is both
							 * the label and the inserted value treated as
							 * a pre parameter to the encapsulateSelection
							 * action. If it's an object, the object must
							 * contain a label or it will be skipped - and
							 * the entire object is passed through as the
							 * tool configuration so it must contain valid
							 * tool configuration content as well.
							 */
							if ( typeof char == 'string' ) {
								tool = {
									'type': 'link',
									'label': char,
									'action': {
										'type': 'encapsulate',
										'options': {
											'pre': char
										}
									}
								};
							} else if ( typeof char == 'object' ) {
								tool = char;
							} else {
								continue;
							}
							if ( !( 'label' in tool ) ) {
								continue;
							}
							$characters.append(
								$( '<a></a>' )
									.attr( 'href', '#' )
									.text( tool.label )
									.data( 'tool', tool )
									.click( useTool )
							);
						}
					}
					break;
			}
		}
	}
	// Wraps gM from js2, but allows raw text to supercede
	function msg( object, property ) {
		return object[property] || gM( object[property + 'Msg'] );
	}
	// Checks if a message of any kind is in an object
	function objHasMsg( object, property ) {
		return property in object || property + 'Msg' in object;
	}
	switch ( section.type ) {
		case 'toolbar':
			// Tools must be in groups, so if there're no groups this part
			// of the configuration is not valid and we need to skip over it
			if ( !( 'groups' in section ) ) {
				return;
			}
			for ( group in section.groups ) {
				var $group = $( '<div></div>' )
					.attr( 'class', 'group' )
					.appendTo( $section );
				if ( objHasMsg( section.groups[group], 'label' ) ) {
					$group.append(
						$( '<div></div>' )
							.attr( 'class', 'label' )
							.text( msg( section.groups[group], 'label' ) )
					)
				}
				addTools( $group, section.groups[group].tools, sectionId );
			}
		break;
		case 'booklet':
			if ( !( 'pages' in section ) ) {
				return;
			}
			var $index = $( '<div></div>' )
				.attr( 'class', 'index' )
				.appendTo( $section );
			var $pages = $( '<div></div>' )
				.attr( 'class', 'pages' )
				.appendTo( $section );
			addPages( $index, $pages, section.pages, sectionId );
			break;
	}
},
/**
 * Builds toolbar
 * 
 * @param {Object} textbox
 * @param {Object} configuration
 */
build: function( context, configuration, editorId ) {
	if ( 'main' in configuration ) {
		// Handle the main specially both for layout purposes and
		// so that it is rendered immediately while the other sections are
		// rendered asynchronously and possibly much later
		$.wikiEditor.modules.toolbar.addSection(
			context, context.modules.$toolbar, configuration.main, 'main'
		);
	}
	// Create a base name for keys that will be stored in a cookie which
	// maintain the state of which sections are open and closed
	var sectionIdBase = editorId + '-wikiEditor-ui-toolbar-section';
	// Create some containers for various elements and append them
	var $tabs = $( '<div></div>' )
		.addClass( 'tabs' )
		.appendTo( context.modules.$toolbar );
	var $sections = $( '<div></div>' )
		.addClass( 'sections' )
		.appendTo( context.modules.$toolbar );
	context.modules.$toolbar.append(
		$( '<div></div>' ).addClass( 'break' )
	);
	// To prevent slow page rendering times, we store the individual
	// section configurations in a queue to be built asynchrnously later on
	var sectionQueue = [];
	for ( section in configuration ) {
		// Skip over main section since it's been handled specially above
		if ( section == 'main' ) {
			continue;
		}
		// Add section container, initially in loading class - but that will
		// get removed once the section is done being built
		var sectionCookie = editorId + '-section';
		var sectionId = sectionCookie + '-' + section.type + '-' + section;
		var $section = $( '<div></div>' )
			.addClass( 'section loading' )
			.addClass( 'section-' + configuration[section].type )
			.addClass(
				'section-' + configuration[section].type + '-' + section
			)
			.attr( 'id', sectionId )
			.append(
				$( '<div></div>' )
					.addClass( 'spinner' )
					.text( gM( 'edittoolbar-loading' ) )
			)
			.appendTo( $sections );
		// Recall the state from cookie
		var current = false;
		if ( $.cookie( sectionCookie ) == sectionId ) {
			$section.attr( 'style', 'display:block' );
			current = true;
		}
		// Add section to queue for later processing
		sectionQueue[sectionQueue.length] = {
			'$section': $section,
			'tools': configuration[section],
			'id': sectionId
		};
		// Add a tab the user can click to hide and show the section
		$tabs.append(
			$( '<span></span>' )
				.attr( 'class', 'tab' )
				.append(
					$( '<a></a>' )
						.text(
							configuration[section].label ||
							gM( configuration[section].labelMsg )
						)
						.attr( { 'href': '#', 'rel': section } )
						.addClass( current ? 'current' : null )
						.data( '$section', $section )
						.data( 'sectionCookie', sectionCookie )
						.click( function() {
							var $section = $(this).data( '$section' );
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
								$(this).data( 'sectionCookie' ),
								show ? $section.attr( 'id' ) : null
							);
							return false;
						} )
				)
		);
	}
	// Process the section queue
	$.eachAsync( sectionQueue, {
		bulk: 0,
		loop: function( index, section ) {
			$.wikiEditor.modules.toolbar.addSection(
				context, section.$section, section.tools, section.id
			);
			// When addSection is done, we can remove the loading
			// class to hide the spinner and reveal the content
			section.$section.removeClass( 'loading' )
		}
	} );
}

};})(jQuery);