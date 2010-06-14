/**
 * Common version-independent functions
 */

if ( typeof mw == 'undefined' ) {
	mw = {};
}
/**
 * Base object for Usability Initiative functionality - naming is temporary
 */
mw.usability = {
	'messages': {}
}
/**
 * Load jQuery UI if requested, otherwise just execute the callback immediately.
 * This is a dirty hack used to work around a bug in older versions of Netscape,
 * which crash when trying to parse jQuery UI
 */
mw.usability.load = function( deps, callback ) {
	// If $j.ui is in deps, load jQuery UI
	var needJUI = false;
	for ( var i = 0; i < deps.length && !needJUI; i++ ) {
		if ( deps[i] == '$j.ui' ) {
			needJUI = true;
		}
	}
	if ( needJUI && typeof $j.ui == 'undefined' ) {
		$j.getScript( wgScriptPath + '/extensions/UsabilityInitiative/js/js2stopgap/jui.combined.min.js', callback );
	} else {
		callback();
	}
};
/**
 * Add messages to a local message table
 */
mw.usability.addMessages = function( messages ) {
	for ( var key in messages ) {
		this.messages[key] = messages[key];
	}
};
/**
 * Get a message
 */
mw.usability.getMsg = function( key, args ) {
	if ( !( key in this.messages ) ) {
		return '[' + key + ']';
	}
	var msg = this.messages[key];
	if ( typeof args == 'object' || typeof args == 'array' ) {
		for ( var argKey in args ) {
			msg = msg.replace( '\$' + (parseInt( argKey ) + 1), args[argKey] );
		}
	} else if ( typeof args == 'string' || typeof args == 'number' ) {
		msg = msg.replace( '$1', args );
	}
	return msg;
};
/**
 * Checks the current browser against a support map object to determine if the browser has been black-listed or not.
 * Because these rules are often very complex, the object contains configurable operators and can check against
 * either the browser version number or string. This process also involves checking if the current browser is amung
 * those which we have configured as compatible or not. If the browser was not configured as comptible we just go on
 * assuming things will work - the argument here is to prevent the need to update the code when a new browser comes
 * to market. The assumption here is that any new browser will be built on an existing engine or be otherwise so
 * similar to another existing browser that things actually do work as expected. The merrits of this argument, which
 * is essentially to blacklist rather than whitelist are debateable, but at this point we've decided it's the more
 * "open-web" way to go.
 * 
 * This function depends on the jquery browser plugin.
 * 
 * A browser map is in the following format:
 * {
 * 		'ltr': {
 * 			// Multiple rules with configurable operators
 * 			'msie': [['>=', 7], ['!=', 9]],
 *			// Blocked entirely
 * 			'iphone': false
 * 		},
 * 		'rtl': {
 * 			// Test against a string
 * 			'msie': [['!==', '8.1.2.3']],
 * 			// RTL rules do not fall through to LTR rules, you must explicity set each of them
 * 			'iphone': false
 * 		}
 * 	}
 * 
 * The user agent string is interpreted. Common browser names are as follows:
 * 		'msie', 'firefox', 'opera', 'safari', 'chrome', 'blackberry', 'ipod', 'iphone', 'ps3', 'konqueror'
 * 
 * @param Object of browser support map
 */
mw.usability.testBrowser = function( map ) {
	// Check over each browser condition to determine if we are running in a compatible client
	var browser = map[$j( 'body' ).is( '.rtl' ) ? 'rtl' : 'ltr'][$j.browser.name];
	if ( typeof browser !== 'object' ) {
		// Unknown, so we assume it's working
		return true;
	}
	for ( var condition in browser ) {
		var op = browser[condition][0];
		var val = browser[condition][1];
		if ( val === false ) {
			return false;
		} else if ( typeof val == 'string' ) {
			if ( !( eval( '$j.browser.version' + op + '"' + val + '"' ) ) ) {
				return false;
			}
		} else if ( typeof val == 'number' ) {
			if ( !( eval( '$j.browser.versionNumber' + op + val ) ) ) {
				return false;
			}
		}
	}
	return true;
};
/**
 * Finds the highest tabindex in use.
 * 
 * @return Integer of highest tabindex on the page
 */
mw.usability.getMaxTabIndex = function() {
	var maxTI = 0;
	$j( '[tabindex]' ).each( function() {
		var ti = parseInt( $j(this).attr( 'tabindex' ) );
		if ( ti > maxTI ) {
			maxTI = ti;
		}
	} );
	return maxTI;
};
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

// Cache ellipsed substrings for every string-width combination
var cache = { };

$.fn.autoEllipsis = function( options ) {
	options = $.extend( {
		'position': 'center',
		'tooltip': false,
		'restoreText': false,
		'hasSpan': false,
		'matchText': null
	}, options );
	$(this).each( function() {
		var $this = $(this);
		if ( options.restoreText ) {
			if ( ! $this.data( 'autoEllipsis.originalText' ) ) {
				$this.data( 'autoEllipsis.originalText', $this.text() );
			} else {
				$this.text( $this.data( 'autoEllipsis.originalText' ) );
			}
		}
		
		// container element - used for measuring against
		var $container = $this;
		// trimmable text element - only the text within this element will be trimmed
		var $trimmableText = null;
		// protected text element - the width of this element is counted, but next is never trimmed from it
		var $protectedText = null;

		if ( options.matchText ) {
			var text = $this.text();
			var matchedText = options.matchText;
			$trimmableText =  $( '<span />' )
				.css( 'whiteSpace', 'nowrap' )
				.addClass( 'autoellipsis-trimmed' )
				.text( $this.text().substr( matchedText.length, $this.text().length ) );
			$protectedText = $( '<span />' )
				.addClass( 'autoellipsis-matched' )
				.css( 'whiteSpace', 'nowrap' )
				.text( options.matchText );
			$container
				.empty()
				.append( $protectedText )
				.append( $trimmableText );
		} else {
			if ( options.hasSpan ) {
				$trimmableText = $this.children( options.selector );
			} else {
				$trimmableText = $( '<span />' )
					.css( 'whiteSpace', 'nowrap' )
					.text( $this.text() );
				$this
					.empty()
					.append( $trimmableText );
			}
		}
		
		var text = $container.text();
		var trimmableText = $trimmableText.text();
		var w = $container.width();
		var pw = $protectedText ? $protectedText.width() : 0;
		// Try cache
		if ( !( text in cache ) ) {
			cache[text] = {};
		}
		if ( options.matchText && !( options.matchText in cache[text] ) ) {
			cache[text][options.matchText] = {};
		}
		if ( !options.matchText && w in cache[text] ) {
			$container.html( cache[text][w] );
			if ( options.tooltip )
				$container.attr( 'title', text );
			return;
		}
		if( options.matchText && options.matchText in cache[text] && w in cache[text][options.matchText] ) {
			$container.html( cache[text][options.matchText][w] );
			if ( options.tooltip )
				$container.attr( 'title', text );
			return;
		}
		if ( $trimmableText.width() + pw > w ) {
			switch ( options.position ) {
				case 'right':
					// Use binary search-like technique for efficiency
					var l = 0, r = trimmableText.length;
					do {
						var m = Math.ceil( ( l + r ) / 2 );
						$trimmableText.text( trimmableText.substr( 0, m ) + '...' );
						if ( $trimmableText.width() + pw > w ) {
							// Text is too long
							r = m - 1;
						} else {
							l = m;
						}
					} while ( l < r );
					$trimmableText.text( trimmableText.substr( 0, l ) + '...' );
					break;
				case 'center':
					// TODO: Use binary search like for 'right'
					var i = [Math.round( trimmableText.length / 2 ), Math.round( trimmableText.length / 2 )];
					var side = 1; // Begin with making the end shorter
					while ( $trimmableText.outerWidth() + pw > w  && i[0] > 0 ) {
						$trimmableText.text( trimmableText.substr( 0, i[0] ) + '...' + trimmableText.substr( i[1] ) );
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
					while ( $trimmableText.outerWidth() + pw > w && r < trimmableText.length ) {
						$trimmableText.text( '...' + trimmableText.substr( r ) );
						r++;
					}
					break;
			}
		}
		if ( options.tooltip ) {
			$container.attr( 'title', text );
		}
		if ( options.matchText ) {
			cache[text][options.matchText][w] = $container.html();
		} else {
			cache[text][w] = $container.html();
		}
		
	} );
};

} )( jQuery );
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

			if (r.name === 'opera' && $.browser.version >= 9.8) {
				r.version = i.match( /version\/([0-9\.]*)/i )[1] || 10;
			}
			r.versionNumber = parseFloat(r.version, 10) || 0;
			r.versionX = (r.version !== x) ? (r.version + '').substr(0, 1) : x;
			r.className = r.name + r.versionX;

			return r;
		};

		a = (a.match(/Opera|Navigator|Minefield|KHTML|Chrome|PLAYSTATION 3/) ? m(a, [
			[/(Firefox|MSIE|KHTML,\slike\sGecko|Konqueror)/, ''],
			['Chrome Safari', 'Chrome'],
			['KHTML', 'Konqueror'],
			['Minefield', 'Firefox'],
			['Navigator', 'Netscape'],
			['PLAYSTATION 3', 'PS3']
		]) : a).toLowerCase();

		$.browser = $.extend((!z) ? $.browser : {}, c(a, /(camino|chrome|firefox|netscape|konqueror|lynx|msie|opera|safari|ipod|iphone|blackberry|ps3)/, [], /(camino|chrome|firefox|netscape|netscape6|opera|version|konqueror|lynx|msie|safari|ps3)(\/|\;?\s|)([a-z0-9\.\+]*?)(\;|dev|rel|\)|\s|$)/));

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
			$.collapsibleTabs.addData( $( this ) );
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
	addData: function( $collapsible ) {
		var $settings = $collapsible.parent().data( 'collapsibleTabsSettings' );
		$collapsible.data( 'collapsibleTabsSettings', {
			'expandedContainer': $settings.expandedContainer,
			'collapsedContainer': $settings.collapsedContainer,
			'expandedWidth': $collapsible.width(),
			'prevElement': $collapsible.prev()
		} );
	},
	getSettings: function( $collapsible ) {
		var $settings = $collapsible.data( 'collapsibleTabsSettings' );
		if ( typeof $settings == 'undefined' ) {
			$.collapsibleTabs.addData( $collapsible );
			$settings = $collapsible.data( 'collapsibleTabsSettings' );
		}
		return $settings;
	},
	handleResize: function( e ){
		$.collapsibleTabs.instances.each( function() {
			var $this = $( this ), data = $.collapsibleTabs.getSettings( $this );
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
					&& data.expandCondition( $.collapsibleTabs.getSettings( $( data.collapsedContainer ).children(
							data.collapsible+":first" ) ).expandedWidth ) ) {
				//move the element from the dropdown to the tab
				$this.trigger( "beforeTabExpand" );
				$.collapsibleTabs
					.moveToExpanded( data.collapsedContainer + " " + data.collapsible + ':first' );
			}
		});
	},
	moveToCollapsed: function( ele ) {
		var $moving = $( ele );
		var data = $.collapsibleTabs.getSettings( $moving );
		var dataExp = $.collapsibleTabs.getSettings( data.expandedContainer );
		dataExp.shifting = true;
		$moving
			.remove()
			.prependTo( data.collapsedContainer )
			.data( 'collapsibleTabsSettings', data );
		dataExp.shifting = false;
		$.collapsibleTabs.handleResize();
	},
	moveToExpanded: function( ele ) {
		var $moving = $( ele );
		var data = $.collapsibleTabs.getSettings( $moving );
		var dataExp = $.collapsibleTabs.getSettings( data.expandedContainer );
		dataExp.shifting = true;
		// remove this element from where it's at and put it in the dropdown menu
		$moving.remove().insertAfter( data.prevElement ).data( 'collapsibleTabsSettings', data );
		dataExp.shifting = false;
		$.collapsibleTabs.handleResize();
	}
};

} )( jQuery );
/*
 * jQuery Color Animations
 * Copyright 2007 John Resig
 * Released under the MIT and GPL licenses.
 */

(function(jQuery){

	// We override the animation for all of these color styles
	jQuery.each(['backgroundColor', 'borderBottomColor', 'borderLeftColor', 'borderRightColor', 'borderTopColor', 'color', 'outlineColor'], function(i,attr){
		jQuery.fx.step[attr] = function(fx){
			if ( fx.state == 0 ) {
				fx.start = getColor( fx.elem, attr );
				fx.end = getRGB( fx.end );
			}

			fx.elem.style[attr] = "rgb(" + [
				Math.max(Math.min( parseInt((fx.pos * (fx.end[0] - fx.start[0])) + fx.start[0]), 255), 0),
				Math.max(Math.min( parseInt((fx.pos * (fx.end[1] - fx.start[1])) + fx.start[1]), 255), 0),
				Math.max(Math.min( parseInt((fx.pos * (fx.end[2] - fx.start[2])) + fx.start[2]), 255), 0)
			].join(",") + ")";
		}
	});

	// Color Conversion functions from highlightFade
	// By Blair Mitchelmore
	// http://jquery.offput.ca/highlightFade/

	// Parse strings looking for color tuples [255,255,255]
	function getRGB(color) {
		var result;

		// Check if we're already dealing with an array of colors
		if ( color && color.constructor == Array && color.length == 3 )
			return color;

		// Look for rgb(num,num,num)
		if (result = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(color))
			return [parseInt(result[1]), parseInt(result[2]), parseInt(result[3])];

		// Look for rgb(num%,num%,num%)
		if (result = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(color))
			return [parseFloat(result[1])*2.55, parseFloat(result[2])*2.55, parseFloat(result[3])*2.55];

		// Look for #a0b1c2
		if (result = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(color))
			return [parseInt(result[1],16), parseInt(result[2],16), parseInt(result[3],16)];

		// Look for #fff
		if (result = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(color))
			return [parseInt(result[1]+result[1],16), parseInt(result[2]+result[2],16), parseInt(result[3]+result[3],16)];

		// Otherwise, we're most likely dealing with a named color
		return colors[jQuery.trim(color).toLowerCase()];
	}
	
	function getColor(elem, attr) {
		var color;

		do {
			color = jQuery.curCSS(elem, attr);

			// Keep going until we find an element that has color, or we hit the body
			if ( color != '' && color != 'transparent' || jQuery.nodeName(elem, "body") )
				break; 

			attr = "backgroundColor";
		} while ( elem = elem.parentNode );

		return getRGB(color);
	};
	
	// Some named colors to work with
	// From Interface by Stefan Petre
	// http://interface.eyecon.ro/

	var colors = {
		aqua:[0,255,255],
		azure:[240,255,255],
		beige:[245,245,220],
		black:[0,0,0],
		blue:[0,0,255],
		brown:[165,42,42],
		cyan:[0,255,255],
		darkblue:[0,0,139],
		darkcyan:[0,139,139],
		darkgrey:[169,169,169],
		darkgreen:[0,100,0],
		darkkhaki:[189,183,107],
		darkmagenta:[139,0,139],
		darkolivegreen:[85,107,47],
		darkorange:[255,140,0],
		darkorchid:[153,50,204],
		darkred:[139,0,0],
		darksalmon:[233,150,122],
		darkviolet:[148,0,211],
		fuchsia:[255,0,255],
		gold:[255,215,0],
		green:[0,128,0],
		indigo:[75,0,130],
		khaki:[240,230,140],
		lightblue:[173,216,230],
		lightcyan:[224,255,255],
		lightgreen:[144,238,144],
		lightgrey:[211,211,211],
		lightpink:[255,182,193],
		lightyellow:[255,255,224],
		lime:[0,255,0],
		magenta:[255,0,255],
		maroon:[128,0,0],
		navy:[0,0,128],
		olive:[128,128,0],
		orange:[255,165,0],
		pink:[255,192,203],
		purple:[128,0,128],
		violet:[128,0,128],
		red:[255,0,0],
		silver:[192,192,192],
		white:[255,255,255],
		yellow:[255,255,0]
	};
	
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
	 * Expand the field, make the callback
	 */
	expandField: function( e, context ) {
		context.config.beforeExpand.call( context.data.$field, context );
		context.data.$field
			.animate( { 'width': context.data.expandedWidth }, 'fast', function() {
				context.config.afterExpand.call( this, context );
			} );
	},
	/**
	 * Condense the field, make the callback
	 */
	condenseField: function( e, context ) {
		context.config.beforeCondense.call( context.data.$field, context );
		context.data.$field
			.animate( { 'width': context.data.condensedWidth }, 'fast', function() {
				context.config.afterCondense.call( this, context );
			} );
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
					// callback function for before collapse
					'beforeCondense': function( context ) {},
					// callback function for before expand
					'beforeExpand': function( context ) {},
					// callback function for after collapse
					'afterCondense': function( context ) {},
					// callback function for after expand
					'afterExpand': function( context ) {},
					// Whether the field should expand to the left or the right -- defaults to left
					'expandToLeft': true
				}
			};
		}
		
		/* API */
		// Handle various calling styles
		if ( args.length > 0 ) {
			if ( typeof args[0] == 'object' ) {
				// Apply set of properties
				for ( var key in args[0] ) {
					$.expandableField.configure( context, key, args[0][key] );
				}
			} else if ( typeof args[0] == 'string' ) {
				if ( args.length > 1 ) {
					// Set property values
					$.expandableField.configure( context, args[0], args[1] );
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
				.addClass( 'expandableField' )
				.focus( function( e ) {
					$.expandableField.expandField( e, context );
				} )
				.delayedBind( 250, 'blur', function( e ) {
					$.expandableField.condenseField( e, context );
				} );
		}
		// Store the context for next time
		$( this ).data( 'expandableField-context', context );
	} );
	return returnValue !== null ? returnValue : $(this);
};

} )( jQuery );
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
 * submitOnClick: Whether to submit the form containing the textbox when a suggestion is clicked
 *		Type: Boolean, Default: false
 * maxExpandFactor: Maximum suggestions box width relative to the textbox width.  If set to e.g. 2, the suggestions box
 *		will never be grown beyond 2 times the width of the textbox.
 *		Type: Number, Range: 1 - infinity, Default: 3
 * positionFromLeft: Whether to position the suggestion box with the left attribute or the right
 *		Type: Boolean, Default: true
 * highlightInput: Whether to hightlight matched portions of the input or not
 *		Type: Boolean, Default: false
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
					if ( context.data.$textbox.val().length == 0 ) {
						// Hide the div when no suggestion exist
						context.data.$container.hide();
					} else {
						// Rebuild the suggestions list
						context.data.$container.show();
						// Update the size and position of the list
						var newCSS = {
							'top': context.config.$region.offset().top + context.config.$region.outerHeight(),
							'bottom': 'auto',
							'width': context.config.$region.outerWidth(),
							'height': 'auto'
						}
						if ( context.config.positionFromLeft ) {
							newCSS['left'] = context.config.$region.offset().left;
							newCSS['right'] = 'auto';
						} else {
							newCSS['left'] = 'auto';
							newCSS['right'] = $( 'body' ).width() - ( context.config.$region.offset().left + context.config.$region.outerWidth() );
						}
						context.data.$container.css( newCSS );
						var $results = context.data.$container.children( '.suggestions-results' );
						$results.empty();
						var expWidth = -1;
						var $autoEllipseMe = $( [] );
						var matchedText = null;
						for ( var i = 0; i < context.config.suggestions.length; i++ ) {
							var text = context.config.suggestions[i];
							var $result = $( '<div />' )
								.addClass( 'suggestions-result' )
								.attr( 'rel', i )
								.data( 'text', context.config.suggestions[i] )
								.mouseover( function( e ) {
									$.suggestions.highlight(
										context, $(this).closest( '.suggestions-results div' ), false
									);
								} )
								.appendTo( $results );
							// Allow custom rendering
							if ( typeof context.config.result.render == 'function' ) {
								context.config.result.render.call( $result, context.config.suggestions[i] );
							} else {
								// Add <span> with text
								if( context.config.highlightInput ) {
									matchedText = text.substr( 0, context.data.prevText.length );
								}
								$result.append( $( '<span />' )
										.css( 'whiteSpace', 'nowrap' )
										.text( text )
									);
								
								// Widen results box if needed
								// New width is only calculated here, applied later
								var $span = $result.children( 'span' );
								if ( $span.outerWidth() > $result.width() && $span.outerWidth() > expWidth ) {
									expWidth = $span.outerWidth();
								}
								$autoEllipseMe = $autoEllipseMe.add( $result );
							}
						}
						// Apply new width for results box, if any
						if ( expWidth > context.data.$container.width() ) {
							var maxWidth = context.config.maxExpandFactor*context.data.$textbox.width();
							context.data.$container.width( Math.min( expWidth, maxWidth ) );
						}
						// autoEllipse the results. Has to be done after changing the width
						$autoEllipseMe.autoEllipsis( { hasSpan: true, tooltip: true, matchText: matchedText } );
					}
				}
				break;
			case 'maxRows':
				context.config[property] = Math.max( 1, Math.min( 100, value ) );
				break;
			case 'delay':
				context.config[property] = Math.max( 0, Math.min( 1200, value ) );
				break;
			case 'maxExpandFactor':
				context.config[property] = Math.max( 1, value );
				break;
			case 'submitOnClick':
			case 'positionFromLeft':
			case 'highlightInput':
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
				if( selected.is( '.suggestions-special' ) ) {
					result = context.data.$container.find( '.suggestions-result:last' )
				} else {
					result = selected.prev();
					if ( selected.length == 0 ) {
						// we are at the begginning, so lets jump to the last item
						if ( context.data.$container.find( '.suggestions-special' ).html() != "" ) {
							result = context.data.$container.find( '.suggestions-special' );
						} else {
							result = context.data.$container.find( '.suggestions-results div:last' );
						}
					}
				}
			} else if ( result == 'next' ) {
				if ( selected.length == 0 ) {
					// No item selected, go to the first one
					result = context.data.$container.find( '.suggestions-results div:first' );
					if ( result.length == 0 && context.data.$container.find( '.suggestions-special' ).html() != "" ) {
						// No suggestion exists, go to the special one directly
						result = context.data.$container.find( '.suggestions-special' );
					}
				} else {
					result = selected.next();
					if ( selected.is( '.suggestions-special' ) ) {
						result = $( [] );
					} else if (
						result.length == 0 &&
						context.data.$container.find( '.suggestions-special' ).html() != ""
					) {
						// We were at the last item, jump to the specials!
						result = context.data.$container.find( '.suggestions-special' );
					}
				}
			}
			selected.removeClass( 'suggestions-result-current' );
			result.addClass( 'suggestions-result-current' );
		}
		if ( updateTextbox ) {
			if ( result.length == 0 ) {
				$.suggestions.restore( context );
			} else {
				context.data.$textbox.val( result.data( 'text' ) );
				// .val() doesn't call any event handlers, so
				// let the world know what happened
				context.data.$textbox.change();
			}
			context.data.$textbox.trigger( 'change' );
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
				preventDefault = true;
				break;
			// Arrow up
			case 38:
				if ( wasVisible ) {
					$.suggestions.highlight( context, 'prev', true );
				}
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
				selected = context.data.$container.find( '.suggestions-result-current' );
				if ( selected.size() == 0 ) {
					// if nothing is selected, cancel any current requests and submit the form
					$.suggestions.cancel( context );
					context.config.$region.closest( 'form' ).submit();
				} else if ( selected.is( '.suggestions-special' ) ) {
					if ( typeof context.config.special.select == 'function' ) {
						context.config.special.select.call( selected, context.data.$textbox );
					}
				} else {
					if ( typeof context.config.result.select == 'function' ) {
						$.suggestions.highlight( context, selected, true );
						context.config.result.select.call( selected, context.data.$textbox );
					} else {
						$.suggestions.highlight( context, selected, true );
					}
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
		if ( typeof context == 'undefined' || context == null ) {
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
					'submitOnClick': false,
					'maxExpandFactor': 3,
					'positionFromLeft': true,
					'highlightInput': false
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
			// Setup the css for positioning the results box
			var newCSS = {
				'top': Math.round( context.data.$textbox.offset().top + context.data.$textbox.outerHeight() ),
				'width': context.data.$textbox.outerWidth(),
				'display': 'none'
			}
			if ( context.config.positionFromLeft ) {
				newCSS['left'] = context.config.$region.offset().left;
				newCSS['right'] = 'auto';
			} else {
				newCSS['left'] = 'auto';
				newCSS['right'] = $( 'body' ).width() - ( context.config.$region.offset().left + context.config.$region.outerWidth() );
			}
			
			context.data.$container = $( '<div />' )
				.css( newCSS )
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
						.mouseover( function( e ) {
							$.suggestions.highlight(
								context, $( e.target ).closest( '.suggestions-special' ), false
							);
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
					if ( context.data.mouseDownOn.length > 0 ) {
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
		var isSample = false;
		if ( this.style.display == 'none' ) {
			// Do nothing
		} else if ( this.selectionStart || this.selectionStart == '0' ) {
			// Mozilla/Opera
			$(this).focus();
			var selText = $(this).textSelection( 'getSelection' );
			var startPos = this.selectionStart;
			var endPos = this.selectionEnd;
			var scrollTop = this.scrollTop;
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
			// Setting this.value scrolls the textarea to the top, restore the scroll position
			this.scrollTop = scrollTop;
			if ( window.opera ) {
				options.pre = options.pre.replace( /\r?\n/g, "\r\n" );
				selText = selText.replace( /\r?\n/g, "\r\n" );
				options.post = options.post.replace( /\r?\n/g, "\r\n" );
			}
			if ( isSample && options.selectPeri ) {
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
			if ( context ) {
				context.fn.restoreStuffForIE();
			}
			var selText = $(this).textSelection( 'getSelection' );
			var scrollTop = this.scrollTop;
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
			if ( isSample && options.selectPeri && range.moveStart ) {
				range.moveStart( 'character', - options.post.length - selText.length );
				range.moveEnd( 'character', - options.post.length );
			}
			range.select();
			// Restore the scroll position
			this.scrollTop = scrollTop;
		}
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
			var preFinished = false;
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
				if ( !preFinished ) {
					if ( preRange.compareEndPoints( "StartToEnd", preRange ) == 0 ) {
						preFinished = true;
					} else {
						preRange.moveEnd( "character", -1 )
						if ( preRange.text == preText ) {
							rawPreText += "\r\n";
						} else {
							preFinished = true;
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
			} while ( ( !preFinished || !periFinished || !postFinished ) );
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
			var length = this.value.length;
			// IE doesn't count \n when computing the offset, so we won't either
			var newLines = this.value.match( /\n/g );
			if ( newLines) length = length - newLines.length;
			selection.moveStart( 'character', options.start );
			selection.moveEnd( 'character', -length + options.end );
			
			// This line can cause an error under certain circumstances (textarea empty, no selection)
			// Silence that error
			try {
				selection.select();
			} catch( e ) { }
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
			var range = document.body.createTextRange();
			var savedRange = document.selection.createRange();
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
			savedRange.select();
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
				'replace': false, // If there is a selection, replace it with peri instead of leaving it alone
				'selectPeri': true // Select the peri text if it was inserted (but not if there was a selection and replace==false)
			}, options );
			break;
		case 'getCaretPosition':
			options = $.extend( {
				'startAndEnd': false // Return [start, end] instead of just start
			}, options );
			// FIXME: We may not need character position-based functions if we insert markers in the right places
			break;
		case 'setSelection':
			options = $.extend( {
				'start': undefined, // Position to start selection at
				'end': undefined, // Position to end selection at. Defaults to start
				'startContainer': undefined, // Element to start selection in (iframe only)
				'endContainer': undefined // Element to end selection in (iframe only). Defaults to startContainer
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
	
	// IE selection restore voodoo
	var needSave = false;
	if ( hasIframe && context.savedSelection !== null ) {
		context.fn.restoreSelection();
		needSave = true;
	}
	retval = ( hasIframe ? context.fn : fn )[command].call( this, options );
	if ( hasIframe && needSave ) {
		context.fn.saveSelection();
	}
	return retval;
};

} )( jQuery );
/**
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
			// Layout issues in FF < 2
			'firefox': [['>=', 2]],
			// Text selection bugs galore - this may be a different situation with the new iframe-based solution
			'opera': [['>=', 9.6]],
			// jQuery minimums
			'safari': [['>=', 3]],
			'chrome': [['>=', 3]],
			'netscape': [['>=', 9]],
			'blackberry': false,
			'ipod': false,
			'iphone': false
		},
		// Right-to-left languages
		'rtl': {
			// The toolbar layout is broken in IE 7 in RTL mode, and IE6 in any mode
			'msie': [['>=', 8]],
			// Layout issues in FF < 2
			'firefox': [['>=', 2]],
			// Text selection bugs galore - this may be a different situation with the new iframe-based solution
			'opera': [['>=', 9.6]],
			// jQuery minimums
			'safari': [['>=', 3]],
			'chrome': [['>=', 3]],
			'netscape': [['>=', 9]],
			'blackberry': false,
			'ipod': false,
			'iphone': false
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
	 * @param module Module object, defaults to $.wikiEditor
	 */
	'isSupported': function( module ) {
		// Fallback to the wikiEditor browser map if no special map is provided in the module
		var mod = module && 'browsers' in module ? module : $.wikiEditor;
		// Check for and make use of cached value and early opportunities to bail
		if ( typeof mod.supported !== 'undefined' ) {
			// Cache hit
			return mod.supported;
		}
		// Run a browser support test and then cache and return the result
		return mod.supported = mw.usability.testBrowser( mod.browsers );
	},
	/**
	 * Checks if a module has a specific requirement
	 * @param module Module object
	 * @param requirement String identifying requirement
	 */
	'isRequired': function( module, requirement ) {
		if ( typeof module['req'] !== 'undefined' ) {
			for ( req in module['req'] ) {
				if ( module['req'][req] == requirement ) {
					return true;
				}
			}
		}
		return false;
	},
	/**
	 * Provides a way to extract messages from objects. Wraps the mw.usability.getMsg() function, which
	 * may eventually become a wrapper for some kind of core MW functionality.
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
			for ( var i in property ) {
				if ( property[i] in object || property[i] + 'Msg' in object ) {
					property = property[i];
					break;
				}
			}
		}
		if ( property in object ) {
			return object[property];
		} else if ( property + 'Msg' in object ) {
			if ( typeof object[property + 'Msg' ] == 'object' ) {
				// [ messageKey, arg1, arg2, ... ]
				return mw.usability.getMsg.apply( mw.usability, object[property + 'Msg' ] );
			} else {
				return mw.usability.getMsg( object[property + 'Msg'] );
			}
		} else {
			return '';
		}
	},
	/**
	 * Provides a way to extract a property of an object in a certain language, falling back on the property keyed as
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
	 * Provides a way to extract the path of an icon in a certain language, automatically appending a version number for
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
	},
	/**
	 * Get the sprite offset for a language if available, icon for a language if available, or the default offset or icon,
	 * in that order of preference.
	 * @param icon Icon object, see autoIcon()
	 * @param offset Offset object
	 * @param path Icon path, see autoIcon()
	 * @param lang Language code, defaults to wgUserLanguage
	 */
	'autoIconOrOffset': function( icon, offset, path, lang ) {
		lang = lang || wgUserLanguage;
		if ( typeof offset == 'object' && lang in offset ) {
			return offset[lang];
		} else if ( typeof icon == 'object' && lang in icon ) {
			return $.wikiEditor.autoIcon( icon, undefined, lang );
		} else {
			return $.wikiEditor.autoLang( offset, lang );
		}
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
if ( !context || typeof context == 'undefined' ) {
	
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
		'instance': $.wikiEditor.instances.push( $(this) ) - 1,
		// Array mapping elements in the textarea to character offsets
		'offsets': null,
		// Cache for context.fn.htmlToText()
		'htmlToTextMap': {},
		// The previous HTML of the iframe, stored to detect whether something really changed.
		'oldHTML': null,
		// Same for delayedChange()
		'oldDelayedHTML': null,
		// The previous selection of the iframe, stored to detect whether the selection has changed
		'oldDelayedSel': null,
		// Saved selection state for IE
		'savedSelection': null,
		// Stack of states in { html: [string] } form
		'history': [],
		// Current history state position - this is number of steps backwards, so it's always -1 or less
		'historyPosition': -1,
		/// The previous historyPosition, stored to detect if change events were due to an undo or redo action
		'oldDelayedHistoryPosition': -1
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
			for ( var module in modules ) {
				// Check for the existance of an available / supported module with a matching name and a create function
				if ( typeof module == 'string' && $.wikiEditor.isSupported( $.wikiEditor.modules[module] ) ) {
					// Extend the context's core API with this module's own API calls
					if ( 'api' in $.wikiEditor.modules[module] ) {
						for ( var call in $.wikiEditor.modules[module].api ) {
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
		'keydown': function( event ) {
			switch ( event.which ) {
				case 90: // z
				case 89: // y
					if ( event.which == 89 && !$.browser.msie ) { 
						// only handle y events for IE
						return true;
					} else if ( ( event.ctrlKey || event.metaKey ) && context.history.length ) {
						// HistoryPosition is a negative number between -1 and -context.history.length, in other words
						// it's the number of steps backwards from the latest state.
						var newPosition;
						if ( event.shiftKey || event.which == 89 ) {
							// Redo
							newPosition = context.historyPosition + 1;
						} else {
							// Undo
							newPosition = context.historyPosition - 1;
						}
						// Only act if we are switching to a valid state
						if ( newPosition >= ( context.history.length * -1 ) && newPosition < 0 ) {
							// Make sure we run the history storing code before we make this change
							context.fn.updateHistory( context.oldDelayedHTML != context.$content.html() );
							context.oldDelayedHistoryPosition = context.historyPosition;
							context.historyPosition = newPosition;
							// Change state
							// FIXME: Destroys event handlers, will be a problem with template folding
							context.$content.html(
								context.history[context.history.length + context.historyPosition].html
							);
							context.fn.purgeOffsets();
							if( context.history[context.history.length + context.historyPosition].sel ) {
								context.fn.setSelection( { 
									start: context.history[context.history.length + context.historyPosition].sel[0],
									end: context.history[context.history.length + context.historyPosition].sel[1]
								} );
							}
						}
						// Prevent the browser from jumping in and doing its stuff
						return false;
					}
					break;
					// Intercept all tab events to provide consisten behavior across browsers
					// Webkit browsers insert tab characters by default into the iframe rather than changing input focus
				case 9: //tab
						// if any modifier keys are pressed, allow the browser to do it's thing
						if ( event.ctrlKey || event.altKey || event.shiftKey ) { 
							return true;
						} else {
							var $tabindexList = $j( '[tabindex]:visible' ).sort( function( a, b ) {
								return a.tabIndex - b.tabIndex;
							} );
							for( var i=0; i < $tabindexList.length; i++ ) {
								if( $tabindexList.eq( i ).attr('id') == context.$iframe.attr( 'id' ) ) {
									$tabindexList.get( i + 1 ).focus();
									break;
								}
							}
							return false;
						}
					break;
				 case 86: //v
					 if ( event.ctrlKey && $.browser.msie ) {
						 //paste, intercepted for IE
						 context.evt.paste( event );
					 }
					 break;
			}
			return true;
		},
		'change': function( event ) {
			event.data.scope = 'division';
			var newHTML = context.$content.html();
			if ( context.oldHTML != newHTML ) {
				context.fn.purgeOffsets();
				context.oldHTML = newHTML;
				event.data.scope = 'realchange';
			}
			// Never let the body be totally empty
			if ( context.$content.children().length == 0 ) {
				context.$content.append( '<p></p>' );
			}
			return true;
		},
		'delayedChange': function( event ) {
			event.data.scope = 'division';
			var newHTML = context.$content.html();
			if ( context.oldDelayedHTML != newHTML ) {
				context.oldDelayedHTML = newHTML;
				event.data.scope = 'realchange';
				// Surround by <p> if it does not already have it
				var cursorPos = context.fn.getCaretPosition();
				var t = context.fn.getOffset( cursorPos[0] );
				if ( ! $.browser.msie && t && t.node.nodeName == '#text' && t.node.parentNode.nodeName.toLowerCase() == 'body' ) {
					$( t.node ).wrap( "<p></p>" );
					context.fn.purgeOffsets();
					context.fn.setSelection( { start: cursorPos[0], end: cursorPos[1] } );
				}
			}
			context.fn.updateHistory( event.data.scope == 'realchange' );
			return true;
		},
		'cut': function( event ) {
			setTimeout( function() {
				context.$content.find( 'br' ).each( function() {
					if ( $(this).parent().is( 'body' ) ) {
						$(this).wrap( $( '<p></p>' ) );
					}
				} );
			}, 100 );
			return true;
		},
		'paste': function( event ) {
			// Save the cursor position to restore it after all this voodoo
			var cursorPos = context.fn.getCaretPosition();
			var offset = 0;
			var oldLength = context.fn.getContents().length;
			
			//give everything the wikiEditor class so that we can easily pick out things without that class as pasted 
			context.$content.find( '*' ).addClass( 'wikiEditor' );
			if ( $.layout.name !== 'webkit' ) {
				context.$content.addClass( 'pasting' );
			}
			setTimeout( function() {
				
				// Kill stuff we know we don't want
				context.$content.find( 'script,style,img,input,select,textarea,hr,button,link,meta' ).remove();
				
				//anything without wikiEditor class was pasted.
				var $selection = context.$content.find( ':not(.wikiEditor)' );
				var nodeToDelete = [];
				var firstDirtyNode;
				if  ( $selection.length == 0 ) {
					firstDirtyNode = context.fn.getOffset( cursorPos[0] ).node;
				} else {
					firstDirtyNode = $selection.eq( 0 )[0];
				}
				while ( firstDirtyNode != null ) {
					//go up till we find the top pasted node
					while ( firstDirtyNode.parentNode.nodeName != 'BODY' 
						 && ! $( firstDirtyNode.parentNode ).hasClass( 'wikiEditor' ) 
						) {
						firstDirtyNode = firstDirtyNode.parentNode;
					}
					
					//go back till we find the first pasted node
					while ( firstDirtyNode.previousSibling != null
							&& ! $( firstDirtyNode.previousSibling ).hasClass( 'wikiEditor' )
						) {
						
						if ( $( firstDirtyNode.previousSibling ).hasClass( '#comment' ) ) {
							$( firstDirtyNode ).remove();
						} else {
							firstDirtyNode = firstDirtyNode.previousSibling;
						}
					}
					
					var $lastDirtyNode = $( firstDirtyNode );
					var cc = makeContentCollector( $.browser, null );
					while ( firstDirtyNode != null && ! $( firstDirtyNode ).hasClass( 'wikiEditor' ) ) {
						cc.collectContent(firstDirtyNode);
						
						cc.notifyNextNode(firstDirtyNode.nextSibling);
						pastedContent = cc.getLines();
						if ((pastedContent.length <= 1 || pastedContent[pastedContent.length - 1] !== "")
								&& firstDirtyNode.nextSibling) {
							nodeToDelete.push( firstDirtyNode );
							firstDirtyNode = firstDirtyNode.nextSibling;
							cc.collectContent(firstDirtyNode);
							cc.notifyNextNode(firstDirtyNode.nextSibling);
						}
						nodeToDelete.push( firstDirtyNode );
						firstDirtyNode = firstDirtyNode.nextSibling;
					}
					var ccData = cc.finish();
					var pastedContent = ccData.lines;
					if ( pastedContent.length == 0 && firstDirtyNode ) {
						offset += $( firstDirtyNode ).text().length;
					}
					
					if ( nodeToDelete.length > 0 ) {
						$lastDirtyNode = $( nodeToDelete[nodeToDelete.length - 1] );
					}
					
					var testVal = '';
					testVal = $( nodeToDelete[0] ).text();
					
					var pastedPretty = '';
					for ( var i = 0; i < pastedContent.length; i++ ) {
						//escape html
						pastedPretty = pastedContent[i].replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r?\n/g, '\\n');
						//replace leading white spaces with &nbsp;
						match = pastedContent[i].match(/^[\s]+[^\s]/);
						if ( match != null && match.length > 0  ) {
							index = match[0].length;
							leadingSpace = match[0].replace(/[\s]/g, '&nbsp;');
							pastedPretty = leadingSpace + pastedPretty.substring(index, pastedPretty.length);
						}
						
						$newElement = $( '<p class="wikiEditor" ></p>' );
						if ( pastedPretty ) {
							$newElement.html( '<span class = "wikiEditor">' + pastedPretty + '</span>' );
						} else {
							$newElement.html( '<br class="wikiEditor">' );
						}
						$newElement.insertAfter( $lastDirtyNode );
						offset += pastedPretty.length;
						$lastDirtyNode = $newElement;
					}
					
					while ( nodeToDelete.length > 0 ) {
						$( nodeToDelete.pop() ).remove();
					}
					
					//find the next node that may not be the next sibling (in IE)
					$selection = context.$content.find( ':not(.wikiEditor)' );
					if  ( $selection.length == 0 ) {
						firstDirtyNode = null;
					} else {
						firstDirtyNode = $selection.eq( 0 )[0];
					}
				}
				
				context.$content.find( '.wikiEditor' ).removeClass( 'wikiEditor' );
				
				//context.$content.find( '*' ).addClass( 'wikiEditor' );
				
				//now place the cursor at the end of pasted content
				var restoreTo = cursorPos[1] + offset;
				
				context.fn.setSelection( { start: restoreTo, end: restoreTo } );

		}, 0 );
		return true;
		},
		'ready': function( event ) {
			// Initialize our history queue
			context.history.push( { 'html': context.$content.html(), 'sel':  context.fn.getCaretPosition() } );
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
			
			var returnFromModules = null; //they return null by default
			// Pass the event around to all modules activated on this context
			for ( var module in context.modules ) {
				if (
					module in $.wikiEditor.modules &&
					'evt' in $.wikiEditor.modules[module] &&
					name in $.wikiEditor.modules[module].evt
				) {
					var ret = $.wikiEditor.modules[module].evt[name]( context, event );
					if (ret != null) {
						//if 1 returns false, the end result is false
						if( returnFromModules == null ) {
							returnFromModules = ret; 
						} else {
							returnFromModules = returnFromModules && ret;
						} 
					}
				}
			}
			if ( returnFromModules != null ) {
				return returnFromModules;
			} else {
				return true;
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
						.mousedown( function() {
							// No dragging!
							return false;
						} )
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
		'highlightLine': function( $element, mode ) {
			if ( !$element.is( 'p' ) ) {
				$element = $element.closest( 'p' );
			}
			$element.css( 'backgroundColor', '#AACCFF' );
			setTimeout( function() { $element.animate( { 'backgroundColor': 'white' }, 'slow' ); }, 100 );
			setTimeout( function() { $element.css( 'backgroundColor', 'white' ); }, 1000 );
		},
		'htmlToText': function( html ) {
			// This function is slow for large inputs, so aggressively cache input/output pairs
			if ( html in context.htmlToTextMap ) {
				return context.htmlToTextMap[html];
			}
			var origHTML = html;
			
			// We use this elaborate trickery for cross-browser compatibility
			// IE does overzealous whitespace collapsing for $( '<pre />' ).html( html );
			// We also do <br> and easy cases for <p> conversion here, complicated cases are handled later
			html = html
				.replace( /\r?\n/g, "" ) // IE7 inserts newlines before block elements
				.replace( /&nbsp;/g, " " ) // We inserted these to prevent IE from collapsing spaces
				.replace( /\<br[^\>]*\>\<\/p\>/gi, '</p>' ) // Remove trailing <br> from <p>
				.replace( /\<\/p\>\s*\<p[^\>]*\>/gi, "\n" ) // Easy case for <p> conversion
				.replace( /\<br[^\>]*\>/gi, "\n" ) // <br> conversion
				.replace( /\<\/p\>(\n*)\<p[^\>]*\>/gi, "$1\n" )
				// Un-nest <p> tags
				.replace( /\<p[^\>]*\><p[^\>]*\>/gi, '<p>' )
				.replace( /\<\/p\><\/p\>/gi, '</p>' );
			// Save leading and trailing whitespace now and restore it later. IE eats it all, and even Firefox
			// won't leave everything alone
			var leading = html.match( /^\s*/ )[0];
			var trailing = html.match( /\s*$/ )[0];
			html = html.substr( leading.length, html.length - leading.length - trailing.length );
			var $pre = $( '<pre>' + html + '</pre>' );
			$pre.find( '.wikiEditor-noinclude' ).each( function() { $( this ).remove(); } );
			// Convert tabs, <p>s and <br>s back
			$pre.find( '.wikiEditor-tab' ).each( function() { $( this ).text( "\t" ); } );
			$pre.find( 'br' ).each( function() { $( this ).replaceWith( "\n" ); } );
			// Converting <p>s is wrong if there's nothing before them, so check that.
			// .find( '* + p' ) isn't good enough because textnodes aren't considered
			$pre.find( 'p' ).each( function() {
				var text =  $( this ).text();
				// If this <p> is preceded by some text, add a \n at the beginning, and if
				// it's followed by a textnode, add a \n at the end
				// We need the traverser because there can be other weird stuff in between
				
				// Check for preceding text
				var t = new context.fn.rawTraverser( this.firstChild, this, $pre.get( 0 ), true ).prev();
				while ( t && t.node.nodeName != '#text' && t.node.nodeName != 'BR' && t.node.nodeName != 'P' ) {
					t = t.prev();
				}
				if ( t ) {
					text = "\n" + text;
				}
				
				// Check for following text
				t = new context.fn.rawTraverser( this.lastChild, this, $pre.get( 0 ), true ).next();
				while ( t && t.node.nodeName != '#text' && t.node.nodeName != 'BR' && t.node.nodeName != 'P' ) {
					t = t.next();
				}
				if ( t && !t.inP && t.node.nodeName == '#text' && t.node.nodeValue.charAt( 0 ) != '\n'
						&& t.node.nodeValue.charAt( 0 ) != '\r' ) {
					text += "\n";
				}
				$( this ).text( text );
			} );
			var retval;
			if ( $.browser.msie ) {
				// IE aggressively collapses whitespace in .text() after having done DOM manipulation,
				// but for some crazy reason this does work. Also convert \r back to \n
				retval = $( '<pre>' + $pre.html() + '</pre>' ).text().replace( /\r/g, '\n' );
			} else {
				retval = $pre.text();
			}
			return context.htmlToTextMap[origHTML] = leading + retval + trailing;
		},
		/**
		 * Get the first element before the selection that's in a certain class
		 * @param classname Class to match. Defaults to '', meaning any class
		 * @param strict If true, the element the selection starts in cannot match (default: false)
		 * @return jQuery object or null if unknown
		 */
		'beforeSelection': function( classname, strict ) {
			if ( typeof classname == 'undefined' ) {
				classname = '';
			}
			var e = null, offset = null;
			if ( context.$iframe[0].contentWindow.getSelection ) {
				// Firefox and Opera
				var selection = context.$iframe[0].contentWindow.getSelection();
				// On load, webkit seems to not have a valid selection
				if ( selection.baseNode !== null ) {
					// Start at the selection's start and traverse the DOM backwards
					// This is done by traversing an element's children first, then the element itself, then its parent
					e = selection.getRangeAt( 0 ).startContainer;
					offset = selection.getRangeAt( 0 ).startOffset;
				} else {
					return null;
				}
				
				// When the cursor is on an empty line, Opera gives us a bogus range object with
				// startContainer=endContainer=body and startOffset=endOffset=1
				var body = context.$iframe[0].contentWindow.document.body;
				if ( $.browser.opera && e == body && offset == 1 ) {
					return null;
				}
			}
			if ( !e && context.$iframe[0].contentWindow.document.selection ) {
				// IE
				// Because there's nothing like range.startContainer in IE, we need to do a DOM traversal
				// to find the element the start of the selection is in
				var range = context.$iframe[0].contentWindow.document.selection.createRange();
				// Set range2 to the text before the selection
				var range2 = context.$iframe[0].contentWindow.document.body.createTextRange();
				// For some reason this call throws errors in certain cases, e.g. when the selection is
				// not in the iframe
				try {
					range2.setEndPoint( 'EndToStart', range );
				} catch ( ex ) {
					return null;
				}
				var seekPos = context.fn.htmlToText( range2.htmlText ).length;
				var offset = context.fn.getOffset( seekPos );
				e = offset ? offset.node : null;
				offset = offset ? offset.offset : null;
				if ( !e ) {
					return null;
				}
			}
			if ( e.nodeName != '#text' ) {
				// The selection is not in a textnode, but between two non-text nodes
				// (usually inside the <body> between two <br>s). Go to the rightmost
				// child of the node just before the selection
				var newE = e.firstChild;
				for ( var i = 0; i < offset - 1 && newE; i++ ) {
					newE = newE.nextSibling;
				}
				while ( newE && newE.lastChild ) {
					newE = newE.lastChild;
				}
				e = newE || e;
			}
			
			// We'd normally use if( $( e ).hasClass( class ) in the while loop, but running the jQuery
			// constructor thousands of times is very inefficient
			var classStr = ' ' + classname + ' ';
			while ( e ) {
				if ( !strict && ( !classname || ( ' ' + e.className + ' ' ).indexOf( classStr ) != -1 ) ) {
					return $( e );
				}
				var next = e.previousSibling;
				while ( next && next.lastChild ) {
					next = next.lastChild;
				}
				e = next || e.parentNode;
				strict = false;
			}
			return $( [] );
		},
		/**
		 * Object used by traverser(). Don't use this unless you know what you're doing
		 */
		'rawTraverser': function( node, inP, ancestor, skipNoinclude ) {
			this.node = node;
			this.inP = inP;
			this.ancestor = ancestor;
			this.skipNoinclude = skipNoinclude;
			this.next = function() {
				var p = this.node;
				var nextInP = this.inP;
				while ( p && !p.nextSibling ) {
					p = p.parentNode;
					if ( p == this.ancestor ) {
						// We're back at the ancestor, stop here
						p = null;
					}
					if ( p && p.nodeName == "P" ) {
						nextInP = null;
					}
				}
				p = p ? p.nextSibling : null;
				if ( p && p.nodeName == "P" ) {
					nextInP = p;
				}
				do {
					// Filter nodes with the wikiEditor-noinclude class
					// Don't use $( p ).hasClass( 'wikiEditor-noinclude' ) because
					// $() is slow in a tight loop
					if ( this.skipNoinclude ) {
						while ( p && ( ' ' + p.className + ' ' ).indexOf( ' wikiEditor-noinclude ' ) != -1 ) {
							p = p.nextSibling;
						}
					}
					if ( p && p.firstChild ) {
						p = p.firstChild;
						if ( p.nodeName == "P" ) {
							nextInP = p;
						}
					}
				} while ( p && p.firstChild );
				// Instead of calling the rawTraverser constructor, inline it. This avoids function call overhead
				return p ? { 'node': p, 'inP': nextInP, 'ancestor': this.ancestor,
						'skipNoinclude': this.skipNoinclude, 'next': this.next, 'prev': this.prev } : null;
			};
			this.prev = function() {
				var p = this.node;
				var prevInP = this.inP;
				while ( p && !p.previousSibling ) {
					p = p.parentNode;
					if ( p == this.ancestor ) {
						// We're back at the ancestor, stop here
						p = null;
					}
					if ( p && p.nodeName == "P" ) {
						prevInP = null;
					}
				}
				p = p ? p.previousSibling : null;
				if ( p && p.nodeName == "P" ) {
					prevInP = p;
				}
				do {
					// Filter nodes with the wikiEditor-noinclude class
					// Don't use $( p ).hasClass( 'wikiEditor-noinclude' ) because
					// $() is slow in a tight loop
					if ( this.skipNoinclude ) {
						while ( p && ( ' ' + p.className + ' ' ).indexOf( ' wikiEditor-noinclude ' ) != -1 ) {
							p = p.previousSibling;
						}
					}
					if ( p && p.lastChild ) {
						p = p.lastChild;
						if ( p.nodeName == "P" ) {
							prevInP = p;
						}
					}
				} while ( p && p.lastChild );
				// Instead of calling the rawTraverser constructor, inline it. This avoids function call overhead
				return p ? { 'node': p, 'inP': prevInP, 'ancestor': this.ancestor,
						'skipNoinclude': this.skipNoinclude, 'next': this.next, 'prev': this.prev } : null;
			};
		},
		/**
		 * Get an object used to traverse the leaf nodes in the iframe DOM. This traversal skips leaf nodes
		 * inside an element with the wikiEditor-noinclude class. This basically wraps rawTraverser
		 *
		 * @param start Node to start at
		 * @return Traverser object, use .next() or .prev() to get a traverser object referring to the
		 *  previous/next node
		 */
		'traverser': function( start ) {
			// Find the leftmost leaf node in the tree
			var startNode = start.jquery ? start.get( 0 ) : start;
			var node = startNode;
			var inP = node.nodeName == "P" ? node : null;
			do {
				// Filter nodes with the wikiEditor-noinclude class
				// Don't use $( p ).hasClass( 'wikiEditor-noinclude' ) because
				// $() is slow in a tight loop
				while ( node && ( ' ' + node.className + ' ' ).indexOf( ' wikiEditor-noinclude ' ) != -1 ) {
					node = node.nextSibling;
				}
				if ( node && node.firstChild ) {
					node = node.firstChild;
					if ( node.nodeName == "P" ) {
						inP = node;
					}
				}
			} while ( node && node.firstChild );
			return new context.fn.rawTraverser( node, inP, startNode, true );
		},
		'getOffset': function( offset ) {
			if ( !context.offsets ) {
				context.fn.refreshOffsets();
			}
			if ( offset in context.offsets ) {
				return context.offsets[offset];
			}
			// Our offset is not pre-cached. Find the highest offset below it and interpolate
			// We need to traverse the entire object because for() doesn't traverse in order
			// We don't do in-order traversal because the object is sparse
			var lowerBound = -1;
			for ( var o in context.offsets ) {
				var realO = parseInt( o );
				if ( realO < offset && realO > lowerBound) {
					lowerBound = realO;
				}
			}
			if ( !( lowerBound in context.offsets ) ) {
				// Weird edge case: either offset is too large or the document is empty
				return null;
			}
			var base = context.offsets[lowerBound];
			return context.offsets[offset] = {
				'node': base.node,
				'offset': base.offset + offset - lowerBound,
				'length': base.length,
				'lastTextNode': base.lastTextNode
			};
		},
		'purgeOffsets': function() {
			context.offsets = null;
		},
		'refreshOffsets': function() {
			context.offsets = [ ];
			var t = context.fn.traverser( context.$content );
			var pos = 0, lastTextNode = null;
			while ( t ) {
				if ( t.node.nodeName != '#text' && t.node.nodeName != 'BR' ) {
					t = t.next();
					continue;
				}
				var nextPos = t.node.nodeName == '#text' ? pos + t.node.nodeValue.length : pos + 1;
				var nextT = t.next();
				var leavingP = t.node.nodeName == '#text' && t.inP && nextT && ( !nextT.inP || nextT.inP != t.inP );
				context.offsets[pos] = {
					'node': t.node,
					'offset': 0,
					'length': nextPos - pos + ( leavingP ? 1 : 0 ),
					'lastTextNode': lastTextNode
				};
				if ( leavingP ) {
					// <p>Foo</p> looks like "Foo\n", make it quack like it too
					// Basically we're faking the \n character much like we're treating <br>s
					context.offsets[nextPos] = {
						'node': t.node,
						'offset': nextPos - pos,
						'length': nextPos - pos + 1,
						'lastTextNode': lastTextNode
					};
				}
				pos = nextPos + ( leavingP ? 1 : 0 );
				if ( t.node.nodeName == '#text' ) {
					lastTextNode = t.node;
				}
				t = nextT;
			}
		},
		'saveSelection': function() {
			if ( !$.browser.msie ) {
				// Only IE needs this
				return;
			}
			if ( typeof context.$iframe != 'undefined' ) {
				context.$iframe[0].contentWindow.focus();
				context.savedSelection = context.$iframe[0].contentWindow.document.selection.createRange();
			} else {
				context.$textarea.focus();
				context.savedSelection = document.selection.createRange();
			}
		},
		'restoreSelection': function() {
			if ( !$.browser.msie || context.savedSelection === null ) {
				return;
			}
			if ( typeof context.$iframe != 'undefined' ) {
				context.$iframe[0].contentWindow.focus();
			} else {
				context.$textarea.focus();
			}
			context.savedSelection.select();
			context.savedSelection = null;
		},
		/**
		 * Update the history queue
		 *
		 * @param htmlChange pass true or false to inidicate if there was a text change that should potentially
		 * 	be given a new history state. 
		 */
		'updateHistory': function( htmlChange ) {
			var newHTML = context.$content.html();
			var newSel = context.fn.getCaretPosition();
			// Was text changed? Was it because of a REDO or UNDO action? 
			if (
				context.history.length == 0 ||
				( htmlChange && context.oldDelayedHistoryPosition == context.historyPosition )
			) {
				context.oldDelayedSel = newSel;
				// Do we need to trim extras from our history? 
				// FIXME: this should really be happing on change, not on the delay
				if ( context.historyPosition < -1 ) {
					//clear out the extras
					context.history.splice( context.history.length + context.historyPosition + 1 );
					context.historyPosition = -1;
				}
				context.history.push( { 'html': newHTML, 'sel': newSel } );
				// If the history has grown longer than 10 items, remove the earliest one
				while ( context.history.length > 10 ) {
					context.history.shift();
				}
			} else if ( context.oldDelayedSel != newSel ) {
				// If only the selection was changed, update it
				context.oldDelayedSel = newSel;
				context.history[context.history.length + context.historyPosition].sel = newSel;
			}
			// synch our old delayed history position until the next undo/redo action
			context.oldDelayedHistoryPosition = context.historyPosition;
		},
		/**
		 * Sets up the iframe in place of the textarea to allow more advanced operations
		 */
		'setupIframe': function() {
			context.$iframe = $( '<iframe></iframe>' )
				.attr( {
					'frameBorder': 0,
					'border': 0,
					'tabindex': 1,
					'src': wgScriptPath + '/extensions/UsabilityInitiative/js/plugins/jquery.wikiEditor.html?' +
						'instance=' + context.instance + '&ts=' + ( new Date() ).getTime() + '&is=content',
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
					// Internet Explorer will reload the iframe once we turn on design mode, so we need to only turn it
					// on during the first run, and then bail
					if ( !this.isSecondRun ) {
						// Turn the document's design mode on
						context.$iframe[0].contentWindow.document.designMode = 'on';
						// Let the rest of this function happen next time around
						if ( $.browser.msie ) {
							this.isSecondRun = true;
							return;
						}
					}
					// Get a reference to the content area of the iframe
					context.$content = $( context.$iframe[0].contentWindow.document.body );
					// Add classes to the body to influence the styles based on what's enabled
					for ( module in context.modules ) {
						context.$content.addClass( 'wikiEditor-' + module );
					}
					// If we just do "context.$content.text( context.$textarea.val() )", Internet Explorer will strip
					// out the whitespace charcters, specifically "\n" - so we must manually encode text and append it
					// TODO: Refactor this into a textToHtml() function
					var html = context.$textarea.val()
						// We're gonna use &esc; as an escape sequence
						.replace( /&esc;/g, '&esc;esc;' )
						// Escape existing uses of <p>, </p>, &nbsp; and <span class="wikiEditor-tab"></span>
						.replace( /\<p\>/g, '&esc;&lt;p&gt;' )
						.replace( /\<\/p\>/g, '&esc;&lt;/p&gt;' )
						.replace(
							/\<span class="wikiEditor-tab"\>\<\/span\>/g,
							'&esc;&lt;span&nbsp;class=&quot;wikiEditor-tab&quot;&gt;&lt;/span&gt;'
						)
						.replace( /&nbsp;/g, '&esc;&amp;nbsp;' );
					// We must do some extra processing on IE to avoid dirty diffs, specifically IE will collapse
					// leading spaces - browser sniffing is not ideal, but executing this code on a non-broken browser
					// doesn't cause harm
					if ( $.browser.msie ) {
						html = html.replace( /\t/g, '<span class="wikiEditor-tab"></span>' );
						if ( $.browser.versionNumber <= 7 ) {
							// Replace all spaces matching &nbsp; - IE <= 7 needs this because of its overzealous
							// whitespace collapsing
							html = html.replace( / /g, "&nbsp;" );
						} else {
							// IE8 is happy if we just convert the first leading space to &nbsp;
							html = html.replace( /(^|\n) /g, "$1&nbsp;" );
						}
					}
					// Use a dummy div to escape all entities
					// This'll also escape <br>, <span> and &nbsp; , so we unescape those after
					// We also need to unescape the doubly-escaped things mentioned above
					html = $( '<div />' ).text( '<p>' + html.replace( /\r?\n/g, '</p><p>' ) + '</p>' ).html()
						.replace( /&amp;nbsp;/g, '&nbsp;' )
						// Allow <p> tags to survive encoding
						.replace( /&lt;p&gt;/g, '<p>' )
						.replace( /&lt;\/p&gt;/g, '</p>' )
						// And <span class="wikiEditor-tab"></span> too
						.replace(
							/&lt;span( |&nbsp;)class=("|&quot;)wikiEditor-tab("|&quot;)&gt;&lt;\/span&gt;/g,
							'<span class="wikiEditor-tab"></span>'
						)
						// Empty <p> tags need <br> tags in them 
						.replace( /<p><\/p>/g, '<p><br></p>' )
						// Unescape &esc; stuff
						.replace( /&amp;esc;&amp;amp;nbsp;/g, '&amp;nbsp;' )
						.replace( /&amp;esc;&amp;lt;p&amp;gt;/g, '&lt;p&gt;' )
						.replace( /&amp;esc;&amp;lt;\/p&amp;gt;/g, '&lt;/p&gt;' )
						.replace(
							/&amp;esc;&amp;lt;span&amp;nbsp;class=&amp;quot;wikiEditor-tab&amp;quot;&amp;gt;&amp;lt;\/span&amp;gt;/g,
							'&lt;span class="wikiEditor-tab"&gt;&lt;\/span&gt;'
						)
						.replace( /&amp;esc;esc;/g, '&amp;esc;' );
					context.$content.html( html );
					
					// Reflect direction of parent frame into child
					if ( $( 'body' ).is( '.rtl' ) ) {
						context.$content.addClass( 'rtl' ).attr( 'dir', 'rtl' );
					}
					// Activate the iframe, encoding the content of the textarea and copying it to the content of iframe
					context.$textarea.attr( 'disabled', true );
					context.$textarea.hide();
					context.$iframe.show();
					// Let modules know we're ready to start working with the content
					context.fn.trigger( 'ready' );
					// Only save HTML now: ready handlers may have modified it
					context.oldHTML = context.oldDelayedHTML = context.$content.html();
					//remove our temporary loading
					/* Disaling our loading div for now
					$( '.wikiEditor-ui-loading' ).fadeOut( 'fast', function() {
						$( this ).remove();
					} );
					*/
					// Setup event handling on the iframe
					$( context.$iframe[0].contentWindow.document )
						.bind( 'keydown', function( event ) {
							event.jQueryNode = context.fn.getElementAtCursor();
							return context.fn.trigger( 'keydown', event );
							
						} )
						.bind( 'keyup', function( event ) {
							event.jQueryNode = context.fn.getElementAtCursor();
							return context.fn.trigger( 'keyup', event );
						} )
						.bind( 'keypress', function( event ) {
							event.jQueryNode = context.fn.getElementAtCursor();
							return context.fn.trigger( 'keypress', event );
						} )
						.bind( 'paste', function( event ) {
							return context.fn.trigger( 'paste', event );
						} )
						.bind( 'cut', function( event ) {
							return context.fn.trigger( 'cut', event );
						} )
						.bind( 'keyup paste mouseup cut encapsulateSelection', function( event ) {
							return context.fn.trigger( 'change', event );
						} )
						.delayedBind( 250, 'keyup paste mouseup cut encapsulateSelection', function( event ) {
							context.fn.trigger( 'delayedChange', event );
						} );
				} );
			// Attach a submit handler to the form so that when the form is submitted the content of the iframe gets
			// decoded and copied over to the textarea
			context.$textarea.closest( 'form' ).submit( function() {
				context.$textarea.attr( 'disabled', false );
				context.$textarea.val( context.$textarea.textSelection( 'getContents' ) );
			} );
			/* FIXME: This was taken from EditWarning.js - maybe we could do a jquery plugin for this? */
			// Attach our own handler for onbeforeunload which respects the current one
			context.fallbackWindowOnBeforeUnload = window.onbeforeunload;
			window.onbeforeunload = function() {
				context.$textarea.val( context.$textarea.textSelection( 'getContents' ) );
				if ( context.fallbackWindowOnBeforeUnload ) {
					return context.fallbackWindowOnBeforeUnload();
				}
			};
		},
		
		/*
		 * Compatibility with the $.textSelection jQuery plug-in. When the iframe is in use, these functions provide
		 * equivilant functionality to the otherwise textarea-based functionality.
		 */
		
		'getElementAtCursor': function() {
			if ( context.$iframe[0].contentWindow.getSelection ) {
				// Firefox and Opera
				var selection = context.$iframe[0].contentWindow.getSelection();
				if ( selection.rangeCount == 0 ) {
					// We don't know where the cursor is
					return $( [] );
				}
				var sc = selection.getRangeAt( 0 ).startContainer;
				if ( sc.nodeName == "#text" ) sc = sc.parentNode;
				return $( sc );
			} else if ( context.$iframe[0].contentWindow.document.selection ) { // should come last; Opera!
				// IE
				var selection = context.$iframe[0].contentWindow.document.selection.createRange();
				return $( selection.parentElement() );
			}
		},
		
		/**
		 * Gets the complete contents of the iframe (in plain text, not HTML)
		 */
		'getContents': function() {
			// For <p></p>, .html() returns <p>&nbsp;</p> in IE
			// This seems to convince IE while not affecting display
			var html;
			if ( $.browser.msie ) {
				// Don't manipulate the iframe DOM itself, causes cursor jumping issues
				var $c = $( context.$content.get( 0 ).cloneNode( true ) );
				$c.find( 'p' ).each( function() {
					if ( $(this).html() == '' ) {
						$(this).replaceWith( '<p></p>' );
					}
				} );
				html = $c.html();
			} else {
				html = context.$content.html();
			}
			return context.fn.htmlToText( html );
		},
		/**
		 * Gets the currently selected text in the content
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 */
		'getSelection': function() {
			var retval;
			if ( context.$iframe[0].contentWindow.getSelection ) {
				// Firefox and Opera
				retval = context.$iframe[0].contentWindow.getSelection();
				if ( $.browser.opera ) {
					// Opera strips newlines in getSelection(), so we need something more sophisticated
					if ( retval.rangeCount > 0 ) {
						retval = context.fn.htmlToText( $( '<pre />' )
								.append( retval.getRangeAt( 0 ).cloneContents() )
								.html()
						);
					} else {
						retval = '';
					}
				}
			} else if ( context.$iframe[0].contentWindow.document.selection ) { // should come last; Opera!
				// IE
				retval = context.$iframe[0].contentWindow.document.selection.createRange();
			}
			if ( typeof retval.text != 'undefined' ) {
				// In IE8, retval.text is stripped of newlines, so we need to process retval.htmlText
				// to get a reliable answer. IE7 does get this right though
				// Run this fix for all IE versions anyway, it doesn't hurt
				retval = context.fn.htmlToText( retval.htmlText );
			} else if ( typeof retval.toString != 'undefined' ) {
				retval = retval.toString();
			}
			return retval;
		},
		/**
		 * Inserts text at the begining and end of a text selection, optionally inserting text at the caret when
		 * selection is empty.
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 */
		'encapsulateSelection': function( options ) {
			var selText = $(this).textSelection( 'getSelection' );
			var selTextArr;
			var collapseToEnd = false;
			var selectAfter = false;
			var setSelectionTo = null;
			var pre = options.pre, post = options.post;
			if ( !selText ) {
				selText = options.peri;
				selectAfter = true;
			} else if ( options.peri == selText.replace( /\s+$/, '' ) ) {
				// Probably a successive button press
				// strip any extra white space from selText
				selText = selText.replace( /\s+$/, '' );
				// set the collapseToEnd flag to ensure our selection is collapsed to the end before any insertion is done
				collapseToEnd = true;
				// set selectAfter to true since we know we'll be populating with our default text
				selectAfter = true;
			} else if ( options.replace ) {
				selText = options.peri;
			} else if ( selText.charAt( selText.length - 1 ) == ' ' ) {
				// Exclude ending space char
				// FIXME: Why?
				selText = selText.substring( 0, selText.length - 1 );
				post += ' ';
			}
			if ( options.splitlines ) {
				selTextArr = selText.split( /\n/ );
			}

			if ( context.$iframe[0].contentWindow.getSelection ) {
				// Firefox and Opera
				var range = context.$iframe[0].contentWindow.getSelection().getRangeAt( 0 );
				// if our test above indicated that this was a sucessive button press, we need to collapse the 
				// selection to the end to avoid replacing text 
				if ( collapseToEnd ) {
					// Make sure we're not collapsing ourselves into a BR tag
					if ( range.endContainer.nodeName == 'BR' ) {
						range.setEndBefore( range.endContainer );
					}
					range.collapse( false );
				}
				if ( options.ownline ) {
					// We need to figure out if the cursor is at the start or end of a line
					var atStart = false, atEnd = false;
					var body = context.$content.get( 0 );
					if ( range.startOffset == 0 ) {
						// Start of a line
						// FIXME: Not necessarily the case with syntax highlighting or
						// template collapsing
						atStart = true;
					} else if ( range.startContainer == body ) {
						// Look up the node just before the start of the selection
						// If it's a <BR>, we're at the start of a line that starts with a
						// block element; if not, we're at the end of a line
						var n = body.firstChild;
						for ( var i = 0; i < range.startOffset - 1 && n; i++ ) {
							n = n.nextSibling;
						}
						if ( n && n.nodeName == 'BR' ) {
							atStart = true;
						} else {
							atEnd = true;
						}
					}
					if ( ( range.endOffset == 0 && range.endContainer.nodeValue == null ) ||
							( range.endContainer.nodeName == '#text' &&
									range.endOffset == range.endContainer.nodeValue.length ) ||
							( range.endContainer.nodeName == 'P' && range.endContainer.nodeValue == null ) ) {
						atEnd = true;
					}
					if ( !atStart ) {
						pre  = "\n" + options.pre;
					}
					if ( !atEnd ) {
						post += "\n";
					}
				}
				var insertText = "";
				if ( options.splitlines ) {
					for( var j = 0; j < selTextArr.length; j++ ) {
						insertText = insertText + pre + selTextArr[j] + post;
						if( j != selTextArr.length - 1 ) {
							insertText += "\n";
						}
					}
				} else {
					insertText = pre + selText + post;
				}
				var insertLines = insertText.split( "\n" );
				range.extractContents();
				// Insert the contents one line at a time - insertNode() inserts at the beginning, so this has to happen
				// in reverse order
				// Track the first and last inserted node, and if we need to also track where the text we need to select
				// afterwards starts and ends
				var firstNode = null, lastNode = null;
				var selSC = null, selEC = null, selSO = null, selEO = null, offset = 0;
				for ( var i = insertLines.length - 1; i >= 0; i-- ) {
					firstNode = context.$iframe[0].contentWindow.document.createTextNode( insertLines[i] );
					range.insertNode( firstNode );
					lastNode = lastNode || firstNode;
					var newOffset = offset + insertLines[i].length;
					if ( !selEC && post.length <= newOffset ) {
						selEC = firstNode;
						selEO = selEC.nodeValue.length - ( post.length - offset );
					}
					if ( selEC && !selSC && pre.length >= insertText.length - newOffset ) {
						selSC = firstNode;
						selSO = pre.length - ( insertText.length - newOffset );
					}
					offset = newOffset;
					if ( i > 0 ) {
						firstNode = context.$iframe[0].contentWindow.document.createElement( 'br' );
						range.insertNode( firstNode );
						newOffset = offset + 1;
						if ( !selEC && post.length <= newOffset ) {
							selEC = firstNode;
							selEO = 1 - ( post.length - offset );
						}
						if ( selEC && !selSC && pre.length >= insertText.length - newOffset ) {
							selSC = firstNode;
							selSO = pre.length - ( insertText.length - newOffset );
						}
						offset = newOffset;
					}
				}
				if ( firstNode ) {
					context.fn.scrollToTop( $( firstNode.parentNode ) );
				}
				if ( selectAfter ) {
					setSelectionTo = {
						startContainer: selSC,
						endContainer: selEC,
						start: selSO,
						end: selEO
					};
				} else if  ( lastNode ) {
					setSelectionTo = {
						startContainer: lastNode,
						endContainer: lastNode,
						start: lastNode.nodeValue.length,
						end: lastNode.nodeValue.length
					};
				}
			} else if ( context.$iframe[0].contentWindow.document.selection ) {
				// IE
				context.$iframe[0].contentWindow.focus();
				var range = context.$iframe[0].contentWindow.document.selection.createRange();
				if ( options.ownline && range.moveStart ) {
					// Check if we're at the start of a line
					// If not, prepend a newline
					var range2 = context.$iframe[0].contentWindow.document.selection.createRange();
					range2.collapse();
					range2.moveStart( 'character', -1 );
					// FIXME: Which check is correct?
					if ( range2.text != "\r" && range2.text != "\n" && range2.text != "" ) {
						pre = "\n" + pre;
					}
					
					// Check if we're at the end of a line
					// If not, append a newline
					var range3 = context.$iframe[0].contentWindow.document.selection.createRange();
					range3.collapse( false );
					range3.moveEnd( 'character', 1 );
					if ( range3.text != "\r" && range3.text != "\n" && range3.text != "" ) {
						post += "\n";
					}
				}
				// if our test above indicated that this was a sucessive button press, we need to collapse the
				// selection to the end to avoid replacing text
				if ( collapseToEnd ) {
					range.collapse( false );
				}
				// TODO: Clean this up. Duplicate code due to the pre-existing browser specific structure of this
				// function
				var insertText = "";
				if ( options.splitlines ) {
					for( var j = 0; j < selTextArr.length; j++ ) {
						insertText = insertText + pre + selTextArr[j] + post;
						if( j != selTextArr.length - 1 ) {
							insertText += "\n"; 
						}
					}
				} else {
					insertText = pre + selText + post;
				}
				// TODO: Maybe find a more elegant way of doing this like the Firefox code above?
				range.pasteHTML( insertText
						.replace( /\</g, '&lt;' )
						.replace( />/g, '&gt;' )
						.replace( /\r?\n/g, '<br />' )
				);
				if ( selectAfter ) {
					range.moveStart( 'character', -post.length - selText.length );
					range.moveEnd( 'character', -post.length );
					range.select();
				}
			}
			
			if ( setSelectionTo ) {
				context.fn.setSelection( setSelectionTo );
			}
			// Trigger the encapsulateSelection event (this might need to get named something else/done differently)
			$( context.$iframe[0].contentWindow.document ).trigger(
				'encapsulateSelection', [ pre, options.peri, post, options.ownline, options.replace ]
			);
			return context.$textarea;
		},
		/**
		 * Gets the position (in resolution of bytes not nessecarily characters) in a textarea
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 */
		'getCaretPosition': function( options ) {
			var startPos = null, endPos = null;
			if ( context.$iframe[0].contentWindow.getSelection ) {
				var selection = context.$iframe[0].contentWindow.getSelection();
				if ( selection.rangeCount == 0 ) {
					// We don't know where the cursor is
					return [ 0, 0 ];
				}
				var sc = selection.getRangeAt( 0 ).startContainer, ec = selection.getRangeAt( 0 ).endContainer;
				var so = selection.getRangeAt( 0 ).startOffset, eo = selection.getRangeAt( 0 ).endOffset;
				if ( sc.nodeName == 'BODY' ) {
					// Grab the node just before the start of the selection
					var n = sc.firstChild;
					for ( var i = 0; i < so - 1 && n; i++ ) {
						n = n.nextSibling;
					}
					sc = n;
					so = 0;
				}
				if ( ec.nodeName == 'BODY' ) {
					var n = ec.firstChild;
					for ( var i = 0; i < eo - 1 && n; i++ ) {
						n = n.nextSibling;
					}
					ec = n;
					eo = 0;
				}
				
				// Make sure sc and ec are leaf nodes
				while ( sc.firstChild ) {
					sc = sc.firstChild;
				}
				while ( ec.firstChild ) {
					ec = ec.firstChild;
				}
				// Make sure the offsets are regenerated if necessary
				context.fn.getOffset( 0 );
				var o;
				for ( o in context.offsets ) {
					if ( startPos === null && context.offsets[o].node == sc ) {
						// For some wicked reason o is a string, even though
						// we put it in as an integer. Use ~~ to coerce it too an int
						startPos = ~~o + so - context.offsets[o].offset;
					}
					if ( startPos !== null && context.offsets[o].node == ec ) {
						endPos = ~~o + eo - context.offsets[o].offset;
						break;
					}
				}
			} else if ( context.$iframe[0].contentWindow.document.selection ) {
				// IE
				// FIXME: This is mostly copypasted from the textSelection plugin
				var d = context.$iframe[0].contentWindow.document;
				var postFinished = false;
				var periFinished = false;
				var postFinished = false;
				var preText, rawPreText, periText;
				var rawPeriText, postText, rawPostText;
				// Depending on the document state, and if the cursor has ever been manually placed within the document
				// the following call such as setEndPoint can result in nasty errors. These cases are always cases
				// in which the start and end points can safely be assumed to be 0, so we will just try our best to do
				// the full process but fall back to 0.
				try {
					// Create range containing text in the selection
					var periRange = d.selection.createRange().duplicate();
					// Create range containing text before the selection
					var preRange = d.body.createTextRange();
					// Move the end where we need it
					preRange.setEndPoint( "EndToStart", periRange );
					// Create range containing text after the selection
					var postRange = d.body.createTextRange();
					// Move the start where we need it
					postRange.setEndPoint( "StartToEnd", periRange );
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
					startPos = rawPreText.replace( /\r\n/g, "\n" ).length;
					endPos = startPos + rawPeriText.replace( /\r\n/g, "\n" ).length;
				} catch( e ) {
					startPos = endPos = 0;
				}
			}
			return [ startPos, endPos ];
		},
		/**
		 * Sets the selection of the content
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 *
		 * @param start Character offset of selection start
		 * @param end Character offset of selection end
		 * @param startContainer Element in iframe to start selection in. If not set, start is a character offset
		 * @param endContainer Element in iframe to end selection in. If not set, end is a character offset
		 */
		'setSelection': function( options ) {
			var sc = options.startContainer, ec = options.endContainer;
			sc = sc && sc.jquery ? sc[0] : sc;
			ec = ec && ec.jquery ? ec[0] : ec;
			if ( context.$iframe[0].contentWindow.getSelection ) {
				// Firefox and Opera
				var start = options.start, end = options.end;
				if ( !sc || !ec ) {
					var s = context.fn.getOffset( start );
					var e = context.fn.getOffset( end );
					sc = s ? s.node : null;
					ec = e ? e.node : null;
					start = s ? s.offset : null;
					end = e ? e.offset : null;
					// Don't try to set the selection past the end of a node, causes errors
					// Just put the selection at the end of the node in this case
					if ( sc != null && sc.nodeName == '#text' && start > sc.nodeValue.length ) {
						start = sc.nodeValue.length - 1;
					}
					if ( ec != null && ec.nodeName == '#text' && end > ec.nodeValue.length ) {
						end = ec.nodeValue.length - 1;
					}
				}
				if ( !sc || !ec ) {
					// The requested offset isn't in the offsets array
					// Give up
					return context.$textarea;
				}
				
				var sel = context.$iframe[0].contentWindow.getSelection();
				while ( sc.firstChild && sc.nodeName != '#text' ) {
					sc = sc.firstChild;
				}
				while ( ec.firstChild && ec.nodeName != '#text' ) {
					ec = ec.firstChild;
				}
				var range = context.$iframe[0].contentWindow.document.createRange();
				range.setStart( sc, start );
				range.setEnd( ec, end );
				sel.removeAllRanges();
				sel.addRange( range );
				context.$iframe[0].contentWindow.focus();
			} else if ( context.$iframe[0].contentWindow.document.body.createTextRange ) {
				// IE
				var range = context.$iframe[0].contentWindow.document.body.createTextRange();
				if ( sc ) {
					range.moveToElementText( sc );
				}
				range.collapse();
				range.moveEnd( 'character', options.start );
				
				var range2 = context.$iframe[0].contentWindow.document.body.createTextRange();
				if ( ec ) {
					range2.moveToElementText( ec );
				}
				range2.collapse();
				range2.moveEnd( 'character', options.end );
				
				// IE does newline emulation for <p>s: <p>foo</p><p>bar</p> becomes foo\nbar just fine
				// but <p>foo</p><br><br><p>bar</p> becomes foo\n\n\n\nbar , one \n too many
				// Correct for this
				var matches, counted = 0;
				// while ( matches = range.htmlText.match( regex ) && matches.length <= counted ) doesn't work
				// because the assignment side effect hasn't happened yet when the second term is evaluated
				while ( matches = range.htmlText.match( /\<\/p\>(\<br[^\>]*\>)+\<p\>/gi ) ) {
					if ( matches.length <= counted )
						break;
					range.moveEnd( 'character', matches.length );
					counted += matches.length;
				}
				range2.moveEnd( 'character', counted );
				while ( matches = range2.htmlText.match( /\<\/p\>(\<br[^\>]*\>)+\<p\>/gi ) ) {
					if ( matches.length <= counted )
						break;
					range2.moveEnd( 'character', matches.length );
					counted += matches.length;
				}

				range2.setEndPoint( 'StartToEnd', range );
				range2.select();
			}
			return context.$textarea;
		},
		/**
		 * Scroll a textarea to the current cursor position. You can set the cursor position with setSelection()
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 */
		'scrollToCaretPosition': function( options ) {
			context.fn.scrollToTop( context.fn.getElementAtCursor(), true );
		},
		/**
		 * Scroll an element to the top of the iframe
		 * DO NOT CALL THIS DIRECTLY, use $.textSelection( 'functionname', options ) instead
		 *
		 * @param $element jQuery object containing an element in the iframe
		 * @param force If true, scroll the element even if it's already visible
		 */
		'scrollToTop': function( $element, force ) {
			var html = context.$content.closest( 'html' ),
				body = context.$content.closest( 'body' ),
				parentHtml = $( 'html' ),
				parentBody = $( 'body' );
			var y = $element.offset().top;
			if ( !$.browser.msie && ! $element.is( 'body' ) ) {
				y = parentHtml.scrollTop() > 0 ? y + html.scrollTop() - parentHtml.scrollTop() : y;
				y = parentBody.scrollTop() > 0 ? y + body.scrollTop() - parentBody.scrollTop() : y;
			}
			var topBound = html.scrollTop() > body.scrollTop() ? html.scrollTop() : body.scrollTop(),
				bottomBound = topBound + context.$iframe.height();
			if ( force || y < topBound || y > bottomBound ) {
					html.scrollTop( y );
					body.scrollTop( y );
				}
			$element.trigger( 'scrollToTop' );
		},
		/**
		 * Save scrollTop and cursor position for IE.
		 */
		'saveStuffForIE': function() {
			// Only need this for IE in textarea mode
			if ( !$.browser.msie || context.$iframe )
				return;
			var IHateIE = {
				'scrollTop' : context.$textarea.scrollTop(),
				'pos': context.$textarea.textSelection( 'getCaretPosition', { startAndEnd: true } )
			};
			context.$textarea.data( 'IHateIE', IHateIE );
		},
		/**
		 * Restore scrollTo and cursor position for IE.
		 */
		'restoreStuffForIE': function() {
			// Only need this for IE in textarea mode
			if ( !$.browser.msie || context.$iframe )
				return;
			var IHateIE = context.$textarea.data( 'IHateIE' );
			if ( !IHateIE )
				return;
			context.$textarea.scrollTop( IHateIE.scrollTop );
			context.$textarea.textSelection( 'setSelection', { start: IHateIE.pos[0], end: IHateIE.pos[1] } );
			context.$textarea.data( 'IHateIE', null );
		}
	};
	
	/*
	 * Base UI Construction
	 * 
	 * The UI is built from several containers, the outer-most being a div classed as "wikiEditor-ui". These containers
	 * provide a certain amount of "free" layout, but in some situations procedural layout is needed, which is performed
	 * as a response to the "resize" event.
	 */
	
	// Assemble a temporary div to place over the wikiEditor while it's being constructed
	/* Disabling our loading div for now
	var $loader = $( '<div></div>' )
		.addClass( 'wikiEditor-ui-loading' )
		.append( $( '<span>' + mw.usability.getMsg( 'wikieditor-loading' ) + '</span>' )
			.css( 'marginTop', context.$textarea.height() / 2 ) );
	*/
	// Encapsulate the textarea with some containers for layout
	context.$textarea
	/* Disabling our loading div for now
		.after( $loader )
		.add( $loader )
	*/
		.wrapAll( $( '<div></div>' ).addClass( 'wikiEditor-ui' ) )
		.wrapAll( $( '<div></div>' ).addClass( 'wikiEditor-ui-view wikiEditor-ui-view-wikitext' ) )
		.wrapAll( $( '<div></div>' ).addClass( 'wikiEditor-ui-left' ) )
		.wrapAll( $( '<div></div>' ).addClass( 'wikiEditor-ui-bottom' ) )
		.wrapAll( $( '<div></div>' ).addClass( 'wikiEditor-ui-text' ) );
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
	$( window ).resize( function( event ) { context.fn.trigger( 'resize', event ); } );
}

/* API Execution */

// Since javascript gives arguments as an object, we need to convert them so they can be used more easily
var args = $.makeArray( arguments );

// Dynamically setup the Iframe when needed when adding modules
if ( typeof context.$iframe === 'undefined' && args[0] == 'addModule' && typeof args[1] != 'undefined' ) {
	var modules = args[1];
	if ( typeof modules != "object" ) {
		modules = {};
		modules[args[1]] = '';
	}
	for ( module in modules ) {
		// Only allow modules which are supported (and thus actually being turned on) affect this decision
		if ( module in $.wikiEditor.modules && $.wikiEditor.isSupported( $.wikiEditor.modules[module] ) &&
				$.wikiEditor.isRequired( $.wikiEditor.modules[module], 'iframe' ) ) {
			context.fn.setupIframe();
			break;
		}
	}
}

// There would need to be some arguments if the API is being called
if ( args.length > 0 ) {
	// Handle API calls
	var call = args.shift();
	if ( call in context.api ) {
		context.api[call]( context, typeof args[0] == 'undefined' ? {} : args[0] );
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
 * Compatability map
 */
'browsers': {
	// Left-to-right languages
	'ltr': {
		'msie': [['>=', 7]],
		// jQuery UI appears to be broken in FF 2.0 - 2.0.0.4
		'firefox': [
			['>=', 2], ['!=', '2.0'], ['!=', '2.0.0.1'], ['!=', '2.0.0.2'], ['!=', '2.0.0.3'], ['!=', '2.0.0.4']
		],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 3]],
		'chrome': [['>=', 3]]
	},
	// Right-to-left languages
	'rtl': {
		'msie': [['>=', 7]],
		// jQuery UI appears to be broken in FF 2.0 - 2.0.0.4
		'firefox': [
			['>=', 2], ['!=', '2.0'], ['!=', '2.0.0.1'], ['!=', '2.0.0.2'], ['!=', '2.0.0.3'], ['!=', '2.0.0.4']
		],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 3]],
		'chrome': [['>=', 3]]
	}
},
/**
 * API accessible functions
 */
api: {
	addDialog: function( context, data ) {
		$.wikiEditor.modules.dialogs.fn.create( context, data )
	},
	openDialog: function( context, module ) {
		mw.usability.load( [ '$j.ui', '$j.ui.dialog', '$j.ui.draggable', '$j.ui.resizable' ], function() {
			if ( module in $.wikiEditor.modules.dialogs.modules ) {
				var mod = $.wikiEditor.modules.dialogs.modules[module];
				var $dialog = $( '#' + mod.id );
				if ( $dialog.length == 0 ) {
					$.wikiEditor.modules.dialogs.fn.reallyCreate( context, mod );
					$dialog = $( '#' + mod.id );
				}
				
				// Workaround for bug in jQuery UI: close button in top right retains focus
				$dialog.closest( '.ui-dialog' )
					.find( '.ui-dialog-titlebar-close' )
					.removeClass( 'ui-state-focus' );
				
				$dialog.dialog( 'open' );
			}
		} );
	},
	closeDialog: function( context, module ) {
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
		// Defer building of modules, but do check whether they need the iframe rightaway
		for ( mod in config ) {
			var module = config[mod];
			// Only create the dialog if it's supported, isn't filtered and doesn't exist yet
			var filtered = false;
			if ( typeof module.filters != 'undefined' ) {
				for ( var i = 0; i < module.filters.length; i++ ) {
					if ( $( module.filters[i] ).length == 0 ) {
						filtered = true;
						break;
					}
				}
			}
			if ( !filtered && $.wikiEditor.isSupported( module ) && $( '#' + module.id ).size() == 0 ) {
				$.wikiEditor.modules.dialogs.modules[mod] = module;
				// If this dialog requires the iframe, set it up
				if ( typeof context.$iframe == 'undefined' && $.wikiEditor.isRequired( module, 'iframe' ) ) {
					context.fn.setupIframe();
				}
				context.$textarea.trigger( 'wikiEditor-dialogs-setup-' + mod );
			}
		}
	},
	/**
	 * Build the actual dialog. This done on-demand rather than in create()
	 * @param {Object} context Context object of editor dialog belongs to
	 * @param {Object} module Dialog module object
	 */
	reallyCreate: function( context, module ) {
		var configuration = module.dialog;
		// Add some stuff to configuration
		configuration.bgiframe = true;
		configuration.autoOpen = false;
		configuration.modal = true;
		configuration.title = $.wikiEditor.autoMsg( module, 'title' );
		// Transform messages in keys
		// Stupid JS won't let us do stuff like
		// foo = { mw.usability.getMsg( 'bar' ): baz }
		configuration.newButtons = {};
		for ( msg in configuration.buttons )
			configuration.newButtons[mw.usability.getMsg( msg )] = configuration.buttons[msg];
		configuration.buttons = configuration.newButtons;
		// Create the dialog <div>
		var dialogDiv = $( '<div />' )
			.attr( 'id', module.id )
			.html( module.html )
			.data( 'context', context )
			.appendTo( $( 'body' ) )
			.each( module.init )
			.dialog( configuration );
		// Set tabindexes on buttons added by .dialog()
		$.wikiEditor.modules.dialogs.fn.setTabindexes( dialogDiv.closest( '.ui-dialog' )
			.find( 'button' ).not( '[tabindex]' ) );
		if ( !( 'resizeme' in module ) || module.resizeme ) {
			dialogDiv
				.bind( 'dialogopen', $.wikiEditor.modules.dialogs.fn.resize )
				.find( '.ui-tabs' ).bind( 'tabsshow', function() {
					$(this).closest( '.ui-dialog-content' ).each(
						$.wikiEditor.modules.dialogs.fn.resize );
				});
		}
		dialogDiv.bind( 'dialogclose', function() {
			context.fn.restoreSelection();
		} );
		
		// Let the outside world know we set up this dialog
		context.$textarea.trigger( 'wikiEditor-dialogs-loaded-' + mod );
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
	},
	/**
	 * Set the right tabindexes on elements in a dialog
	 * @param $elements Elements to set tabindexes on. If they already have tabindexes, this function can behave a bit weird
	 */
	setTabindexes: function( $elements ) {
		// Get the highest tab index
		var tabIndex = mw.usability.getMaxTabIndex() + 1;
		$elements.each( function() {
			$j(this).attr( 'tabindex', tabIndex++ );
		} );
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

}; } ) ( jQuery );
/* Highlight module for wikiEditor */

( function( $ ) { $.wikiEditor.modules.highlight = {

/**
 * Core Requirements
 */
'req': [ 'iframe' ],
/**
 * Configuration
 */
'cfg': {
	'styleVersion': 3
},
/**
 * Internally used event handlers
 */
'evt': {
	'delayedChange': function( context, event ) {
		if ( event.data.scope == 'realchange' ) {
			$.wikiEditor.modules.highlight.fn.scan( context );
			$.wikiEditor.modules.highlight.fn.mark( context, event.data.scope );
		}
	},
	'ready': function( context, event ) {
		$.wikiEditor.modules.highlight.fn.scan( context );
		$.wikiEditor.modules.highlight.fn.mark( context, 'ready' );
	}
},
/**
 * Internally used functions
 */
'fn': {
	/**
	 * Creates a highlight module within a wikiEditor
	 * 
	 * @param config Configuration object to create module from
	 */
	'create': function( context, config ) {
		context.modules.highlight.markersStr = '';
	},
	/**
	 * Scans text division for tokens
	 * 
	 * @param division
	 */
	'scan': function( context, division ) {
		// Remove all existing tokens
		var tokenArray = context.modules.highlight.tokenArray = [];
		// Scan text for new tokens
		var text = context.fn.getContents();
		// Perform a scan for each module which provides any expressions to scan for
		// FIXME: This traverses the entire string once for every regex. Investigate
		// whether |-concatenating regexes then traversing once is faster.
		for ( var module in context.modules ) {
			if ( module in $.wikiEditor.modules && 'exp' in $.wikiEditor.modules[module] ) {
				for ( var exp in $.wikiEditor.modules[module].exp ) {
					// Prepare configuration
					var regex = $.wikiEditor.modules[module].exp[exp].regex;
					var label = $.wikiEditor.modules[module].exp[exp].label;
					var markAfter = $.wikiEditor.modules[module].exp[exp].markAfter || false;
					// Search for tokens
					var offset = 0, left, right, match;
					while ( ( match = text.substr( offset ).match( regex ) ) != null ) {
						right = ( left = offset + match.index ) + match[0].length;
						tokenArray[tokenArray.length] = {
							'offset': markAfter ? right : left,
							'label': label,
							'tokenStart': left,
							'match': match
						};
						// Move to the right of this match
						offset = right;
					}
				}
			}
		}
		// Sort by start
		tokenArray.sort( function( a, b ) { return a.tokenStart - b.tokenStart; } );
		// Let the world know, a scan just happened!
		context.fn.trigger( 'scan' );
	},
	/**
	 * Marks up text with HTML
	 * 
	 * @param division
	 * @param tokens
	 */
	// FIXME: What do division and tokens do?
	// TODO: Document the scan() and mark() APIs somewhere
	'mark': function( context, division, tokens ) {
		// Reset markers
		var markers = [];
		
		// Recycle markers that will be skipped in this run
		if ( context.modules.highlight.markers && division != '' ) {
			for ( var i = 0; i < context.modules.highlight.markers.length; i++ ) {
				if ( context.modules.highlight.markers[i].skipDivision == division ) {
					markers.push( context.modules.highlight.markers[i] );
				}
			}
		}
		context.modules.highlight.markers = markers;
		
		// Get all markers
		context.fn.trigger( 'mark' );
		markers.sort( function( a, b ) { return a.start - b.start || a.end - b.end; } );
		
		// Serialize the markers array to a string and compare it with the one stored in the previous run - if they're
		// equal, there's no markers to change
		var markersStr = '';
		for ( var i = 0; i < markers.length; i++ ) {
			markersStr += markers[i].start + ',' + markers[i].end + ',' + markers[i].type + ',';
		}
		if ( context.modules.highlight.markersStr == markersStr ) {
			// No change, bail out
			return;
		}
		context.modules.highlight.markersStr = markersStr;
		
		// Traverse the iframe DOM, inserting markers where they're needed - store visited markers here so we know which
		// markers should be removed
		var visited = [], v = 0;
		for ( var i = 0; i < markers.length; i++ ) {
			if ( typeof markers[i].skipDivision !== 'undefined' && ( division == markers[i].skipDivision ) ) { 
				continue;
			}
			
			// We want to isolate each marker, so we may need to split textNodes if a marker starts or ends halfway one.
			var start = markers[i].start;
			var s = context.fn.getOffset( start );
			if ( !s ) {
				// This shouldn't happen
				continue;
			}
			var startNode = s.node;
			
			// Don't wrap leading BRs, produces undesirable results
			// FIXME: It's also possible that the offset is a bit high because getOffset() has incremented .length to
			// fake the newline caused by startNode being in a P. In this case, prevent the textnode splitting below
			// from making startNode an empty textnode, IE barfs on that
			while ( startNode.nodeName == 'BR' || s.offset == startNode.nodeValue.length ) {
				start++;
				s = context.fn.getOffset( start );
				startNode = s.node;
			}
			
			// The next marker starts somewhere in this textNode or at this BR
			if ( s.offset > 0 && s.node.nodeName == '#text' ) {
				// Split off the prefix - this leaves the prefix in the current node and puts the rest in a new node
				// which is our start node
				var newStartNode = startNode.splitText( s.offset < s.node.nodeValue.length ?
					s.offset : s.node.nodeValue.length - 1
				);
				var oldStartNode = startNode;
				startNode = newStartNode;
				// Update offset objects. We don't need purgeOffsets(), simply manipulating the existing offset objects
				// will suffice
				// FIXME: This manipulates context.offsets directly, which is ugly, but the performance improvement vs.
				// purgeOffsets() is worth it - this code doesn't set lastTextNode to newStartNode for offset objects
				// with lastTextNode == oldStartNode, but that doesn't really matter
				var subtracted = s.offset;
				var oldLength = s.length;

				var j, o;
				// Update offset objects referring to oldStartNode
				for ( j = start - subtracted; j < start; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = oldStartNode;
						o.length = subtracted;
					}
				}
				// Update offset objects referring to newStartNode
				for ( j = start; j < start - subtracted + oldLength; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = newStartNode;
						o.offset -= subtracted;
						o.length -= subtracted;
						o.lastTextNode = oldStartNode;
					}
				}
			}
			var end = markers[i].end;
			// To avoid ending up at the first char of the next node, we grab the offset for end - 1 and add one to the
			// offset
			var e = context.fn.getOffset( end - 1 );
			if ( !e ) {
				// This shouldn't happen
				continue;
			}
			var endNode = e.node;
			if ( e.offset + 1 < e.length - 1 && endNode.nodeName == '#text' ) {
				// Split off the suffix. This puts the suffix in a new node and leaves the rest in endNode
				var oldEndNode = endNode;
				var newEndNode = endNode.splitText( e.offset + 1 );
				// Update offset objects
				var subtracted = e.offset + 1;
				var oldLength = e.length;
				var j, o;
				// Update offset objects referring to oldEndNode
				for ( j = end - subtracted; j < end; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = oldEndNode;
						o.length = subtracted;
					}
				}
				// We have to insert this one, as it might not exist: we didn't call getOffset( end )
				context.offsets[end] = {
					'node': newEndNode,
					'offset': 0,
					'length': oldLength - subtracted,
					'lastTextNode': oldEndNode
				};
				// Update offset objects referring to newEndNode
				for ( j = end + 1; j < end - subtracted + oldLength; j++ ) {
					if ( j in context.offsets ) {
						o = context.offsets[j];
						o.node = newEndNode;
						o.offset -= subtracted;
						o.length -= subtracted;
						o.lastTextNode = oldEndNode;
					}
				}
			}
			// Don't wrap trailing BRs, doing that causes weird issues
			if ( endNode.nodeName == 'BR' ) {
				endNode = e.lastTextNode;
			}
			// If startNode and endNode have different parents, we need to pull endNode and all textnodes in between
			// into startNode's parent and replace </p><p> with <br>
			if ( startNode.parentNode != endNode.parentNode ) {
				var startP = $( startNode ).closest( 'p' ).get( 0 );
				var t = new context.fn.rawTraverser( startNode, startP, context.$content.get( 0 ), false );
				var afterStart = startNode.nextSibling;
				var lastP = startP;
				var nextT = t.next();
				while ( nextT && t.node != endNode ) {
					t = nextT;
					nextT = t.next();
					// If t.node has a different parent, merge t.node.parentNode with startNode.parentNode
					if ( t.node.parentNode != startNode.parentNode ) {
						var oldParent = t.node.parentNode;
						if ( afterStart ) {
							if ( lastP != t.inP ) {
								// We're entering a new <p>, insert a <br>
								startNode.parentNode.insertBefore(
									startNode.ownerDocument.createElement( 'br' ),
									afterStart
								);
							}
							// A <p> with just a <br> in it is an empty line, so let's not bother with unwrapping it
							if ( !( oldParent.childNodes.length == 1 && oldParent.firstChild.nodeName == 'BR' ) ) {
								// Move all children of oldParent into startNode's parent
								while ( oldParent.firstChild ) {
									startNode.parentNode.insertBefore( oldParent.firstChild, afterStart );
								}
							}
						} else {
							if ( lastP != t.inP ) {
								// We're entering a new <p>, insert a <br>
								startNode.parentNode.appendChild(
									startNode.ownerDocument.createElement( 'br' )
								);
							}
							// A <p> with just a <br> in it is an empty line, so let's not bother with unwrapping it
							if ( !( oldParent.childNodes.length == 1 && oldParent.firstChild.nodeName == 'BR' ) ) {
								// Move all children of oldParent into startNode's parent
								while ( oldParent.firstChild ) {
									startNode.parentNode.appendChild( oldParent.firstChild );
								}
							}
						}
						// Remove oldParent, which is now empty
						oldParent.parentNode.removeChild( oldParent );
					}
					lastP = t.inP;
				}
				// Moving nodes around like this invalidates offset objects
				// TODO: Update offset objects ourselves for performance. Requires rewriting this code block to be
				// offset-based rather than traverser-based
			}
			// Now wrap everything between startNode and endNode (may be equal).
			var ca1 = startNode, ca2 = endNode;
			if ( ca1 && ca2 && ca1.parentNode ) {
				var anchor = markers[i].getAnchor( ca1, ca2 );
				if ( !anchor ) {
					var commonAncestor = ca1.parentNode;
					if ( markers[i].anchor == 'wrap') {
						// We have to store things like .parentNode and .nextSibling because appendChild() changes these
						var newNode = ca1.ownerDocument.createElement( 'span' );
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
						anchor = newNode;
					} else if ( markers[i].anchor == 'tag' ) {
						anchor = commonAncestor;
					}
					$( anchor ).data( 'marker', markers[i] ).addClass( 'wikiEditor-highlight' );
					// Allow the module adding this marker to manipulate it
					markers[i].afterWrap( anchor, markers[i] );

				} else {
					// Update the marker object
					$( anchor ).data( 'marker', markers[i] );
					if ( typeof markers[i].onSkip == 'function' ) {
						markers[i].onSkip( anchor );
					}
				}
				visited[v++] = anchor;
			}
		}
		// Remove markers that were previously inserted but weren't passed to this function - visited[] contains the
		// visited elements in order and find() and each() preserve order
		var j = 0;
		context.$content.find( '.wikiEditor-highlight' ).each( function() {
			if ( visited[j] == this ) {
				// This marker is legit, leave it in
				j++;
				return true;
			}
			// Remove this marker
			var marker = $(this).data( 'marker' );
			if ( marker && typeof marker.skipDivision != 'undefined' && ( division == marker.skipDivision ) ) {
				// Don't remove these either
				return true;
			}
			if ( marker && typeof marker.beforeUnwrap == 'function' )
				marker.beforeUnwrap( this );
			if ( ( marker && marker.anchor == 'tag' ) || $(this).is( 'p' ) ) {
				// Remove all classes
				$(this).removeAttr( 'class' );
			} else {
				// Assume anchor == 'wrap'
				$(this).replaceWith( this.childNodes );
			}
			context.fn.purgeOffsets();
		});
		
	}
}

}; })( jQuery );

/* Preview module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.preview = {

/**
 * Compatability map
 */
'browsers': {
	// Left-to-right languages
	'ltr': {
		'msie': [['>=', 7]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 4]]
	},
	// Right-to-left languages
	'rtl': {
		'msie': [['>=', 8]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 4]]
	}
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
				
				// Call the API. First PST the input, then diff it
				var postdata = {
					'action': 'parse',
					'onlypst': '',
					'text': wikitext,
					'format': 'json'
				};
				
				$.post( wgScriptPath + '/api.php', postdata, function( data ) {
					try {
						var postdata2 = {
							'action': 'query',
							'indexpageids': '',
							'prop': 'revisions',
							'titles': wgPageName,
							'rvdifftotext': data.parse.text['*'],
							'rvprop': '',
							'format': 'json'
						};
						var section = $( '[name=wpSection]' ).val();
						if ( section != '' )
							postdata['rvsection'] = section;
						
						$.post( wgScriptPath + '/api.php', postdata2, function( data ) {
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
								} catch ( e ) { } // "blah is undefined" error, ignore
							}, 'json'
						);
					} catch( e ) { } // "blah is undefined" error, ignore
				}, 'json' );
			}
		} );
		
		var loadingMsg = mw.usability.getMsg( 'wikieditor-preview-loading' );
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

}; } )( jQuery );
/* Publish module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.publish = {

/**
 * Compatability map
 */
'browsers': {
	// Left-to-right languages
	'ltr': {
		'msie': [['>=', 7]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 4]]
	},
	// Right-to-left languages
	'rtl': {
		'msie': [['>=', 8]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 9.6]],
		'safari': [['>=', 4]]
	}
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
		// Build the dialog behind the Publish button
		var dialogID = 'wikiEditor-' + context.instance + '-dialog';
		$.wikiEditor.modules.dialogs.fn.create(
			context,
			{
				previewsave: {
					id: dialogID,
					titleMsg: 'wikieditor-publish-dialog-title',
					html: '\
						<div class="wikiEditor-publish-dialog-copywarn"></div>\
						<div class="wikiEditor-publish-dialog-editoptions">\
							<form id="wikieditor-' + context.instance + '-publish-dialog-form">\
								<div class="wikiEditor-publish-dialog-summary">\
									<label for="wikiEditor-' + context.instance + '-dialog-summary"\
										rel="wikieditor-publish-dialog-summary"></label>\
									<br />\
									<input type="text" id="wikiEditor-' + context.instance + '-dialog-summary"\
										style="width: 100%;" />\
								</div>\
								<div class="wikiEditor-publish-dialog-options">\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-dialog-minor" />\
									<label for="wikiEditor-' + context.instance + '-dialog-minor"\
										rel="wikieditor-publish-dialog-minor"></label>\
									<input type="checkbox"\
										id="wikiEditor-' + context.instance + '-dialog-watch" />\
									<label for="wikiEditor-' + context.instance + '-dialog-watch"\
										rel="wikieditor-publish-dialog-watch"></label>\
								</div>\
							</form>\
						</div>',
					init: function() {
						$(this).find( '[rel]' ).each( function() {
							$(this).text( mw.usability.getMsg( $(this).attr( 'rel' ) ) );
						});
						
						/* REALLY DIRTY HACK! */
						// Reformat the copyright warning stuff
						var copyWarnHTML = $( '#editpage-copywarn p' ).html();
						// TODO: internationalize by splitting on other characters that end statements
						var copyWarnStatements = copyWarnHTML.split( '. ' );
						var newCopyWarnHTML = '<ul>';
						for ( var i = 0; i < copyWarnStatements.length; i++ ) {
							if ( copyWarnStatements[i] != '' ) {
								var copyWarnStatement = $j.trim( copyWarnStatements[i] ).replace( /\.*$/, '' );
								newCopyWarnHTML += '<li>' + copyWarnStatement + '.</li>';
							}
						}
						newCopyWarnHTML += '</ul>';
						// No list if there's only one element
						$(this).find( '.wikiEditor-publish-dialog-copywarn' ).html( 
								copyWarnStatements.length > 1 ? newCopyWarnHTML : copyWarnHTML
						);
						/* END OF REALLY DIRTY HACK */
						
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

}; } )( jQuery );
/* TemplateEditor module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.templateEditor = {
/**
 * Name mappings, dirty hack which will be removed once "TemplateInfo" extension is more fully supported
 */
'nameMappings': { //keep these all lowercase to navigate web of redirects
   "infobox skyscraper": "building_name",
   "infobox settlement": "official_name"
},		

		
/**
 * Compatability map
 */
'browsers': {
	// Left-to-right languages
	'ltr': {
		'msie': [['>=', 8]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 10]],
		'safari': [['>=', 4]]
	},
	// Right-to-left languages
	'rtl': {
		'msie': false,
		'firefox': [['>=', 3]],
		'opera': [['>=', 10]],
		'safari': [['>=', 4]]
	}
},
/**
 * Core Requirements
 */
'req': [ 'iframe' ],
/**
 * Event handlers
 */
evt: {
	
	mark: function( context, event ) {
		// The markers returned by this function are skipped on realchange, so don't regenerate them in that case
		if ( context.modules.highlight.currentScope == 'realchange' ) {
			return;
		}
		
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
				while ( tokenIndex < tokenArray.length - 1 && endIndex == -1 ) {
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
						type: 'template',
						anchor: 'wrap',
						afterWrap: function( node ) {
							// Generate model
							var model = $.wikiEditor.modules.templateEditor.fn.updateModel( $( node ) );
							if ( model.isCollapsible() ) {
								$.wikiEditor.modules.templateEditor.fn.wrapTemplate( $( node ) );
								$.wikiEditor.modules.templateEditor.fn.bindTemplateEvents( $( node ) );
							} else {
								$( node ).addClass( 'wikiEditor-template-text' );
							}
						},
						beforeUnwrap: function( node ) {
							if ( $( node ).parent().hasClass( 'wikiEditor-template' ) ) {
								$.wikiEditor.modules.templateEditor.fn.unwrapTemplate( $( node ) );
							}
						},
						onSkip: function( node ) {
							if ( $( node ).html() == $( node ).data( 'oldHTML' ) ) {
								// No change
								return;
							}
							
							// Text changed, regenerate model
							var model = $.wikiEditor.modules.templateEditor.fn.updateModel( $( node ) );
							
							// Update template name if needed
							if ( $( node ).parent().hasClass( 'wikiEditor-template' ) ) {
								var $label = $( node ).parent().find( '.wikiEditor-template-label' );
								var displayName = $.wikiEditor.modules.templateEditor.fn.getTemplateDisplayName( model );
								if ( $label.text() != displayName ) {
									$label.text( displayName );
								}
							}
							
							// Wrap or unwrap the template if needed
							if ( $( node ).parent().hasClass( 'wikiEditor-template' ) &&
									!model.isCollapsible() ) {
								$.wikiEditor.modules.templateEditor.fn.unwrapTemplate( $( node ) );
							} else if ( !$( node ).parent().hasClass( 'wikiEditor-template' ) &&
									model.isCollapsible() ) {
								$.wikiEditor.modules.templateEditor.fn.wrapTemplate( $( node ) );
								$.wikiEditor.modules.templateEditor.fn.bindTemplateEvents( $( node ) );
							}
						},
						getAnchor: function( ca1, ca2 ) {
							return $( ca1.parentNode ).is( 'span.wikiEditor-template-text' ) ?
								ca1.parentNode : null;
						},
						context: context,
						skipDivision: 'realchange'
					} );
				} else { //else this was an unmatched opening
					tokenArray[beginIndex].label = 'TEMPLATE_FALSE_BEGIN';
					tokenIndex = beginIndex;
				}
			}//if opentemplates
		}
	}, //mark
	
	keydown: function( context, event ) {
		// Reset our ignoreKeypress variable if it's set to true
		if ( context.$iframe.data( 'ignoreKeypress' ) ) {
			context.$iframe.data( 'ignoreKeypress', false );
		}
		var $evtElem = event.jQueryNode;
		if ( $evtElem.hasClass( 'wikiEditor-template-label' ) ) {
			// Allow anything if the command or control key are depressed
			if ( event.ctrlKey || event.metaKey ) return true;
			switch ( event.which ) {
				case 13: // Enter
					$evtElem.click();
					event.preventDefault();
					return false;
				case 32: // Space
					$evtElem.parent().siblings( '.wikiEditor-template-expand' ).click();
					event.preventDefault();
					return false;
				case 37:// Left
				case 38:// Up
				case 39:// Right
				case 40: //Down
					return true; 
				default:
					// Set the ignroreKeypress variable so we don't allow typing if the key is held
					context.$iframe.data( 'ignoreKeypress', true );
					// Can't type in a template name
					event.preventDefault();
					return false;
			}
		} else if ( $evtElem.hasClass( 'wikiEditor-template-text' ) ) {
			switch ( event.which ) {
				case 13: // Enter
					// Ensure that the user can't break this by holding in the enter key
					context.$iframe.data( 'ignoreKeypress', true );
					// FIXME: May be a more elegant way to do this, but this works too
					context.fn.encapsulateSelection( { 'pre': '\n', 'peri': '', 'post': '' } );
					event.preventDefault();
					return false;
				default: return true;
			}
		}
	},
	keyup: function( context, event ) {
		// Rest our ignoreKeypress variable if it's set to true
		if ( context.$iframe.data( 'ignoreKeypress' ) ) {
			context.$iframe.data( 'ignoreKeypress', false );
		}
		return true;
	},
	keypress: function( context, event ) {
		// If this event is from a keydown event which we want to block, ignore it
		return ( context.$iframe.data( 'ignoreKeypress' ) ? false : true );
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
 * Configuration 
 */
cfg: {
},
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
	/**
	 * Turns a simple template wrapper (really just a <span>) into a complex one
	 * @param $wrapper Wrapping <span>
	 */
	wrapTemplate: function( $wrapper ) {
		var model = $wrapper.data( 'model' );
		var context = $wrapper.data( 'marker' ).context;
		var $template = $wrapper
			.wrap( '<span class="wikiEditor-template"></span>' )
			.addClass( 'wikiEditor-template-text wikiEditor-template-text-shrunken' )
			.parent()
			.addClass( 'wikiEditor-template-collapsed' )
			.prepend(
				'<span class="wikiEditor-template-expand wikiEditor-noinclude"></span>' +
				'<span class="wikiEditor-template-name wikiEditor-noinclude">' +
					'<span class="wikiEditor-template-label wikiEditor-noinclude">' + 
					$.wikiEditor.modules.templateEditor.fn.getTemplateDisplayName( model ) + '</span>' +
					'<span class="wikiEditor-template-dialog wikiEditor-noinclude"></span>' +
				'</span>'
			);
	},
	/**
	 * Turn a complex template wrapper back into a simple one
	 * @param $wrapper Wrapping <span>
	 */
	unwrapTemplate: function( $wrapper ) {
		$wrapper.parent().replaceWith( $wrapper );
	},
	/**
	 * Bind events to a template
	 * @param $wrapper Original wrapper for the template to bind events to
	 */
	bindTemplateEvents: function( $wrapper ) {
		var $template = $wrapper.parent( '.wikiEditor-template' );

		if ( typeof ( opera ) == "undefined" ) {
			$template.parent().attr('contentEditable', 'false');
		}
		
		$template.click( function(event) {event.preventDefault(); return false;} )
		
		$template.find( '.wikiEditor-template-name' )
			.click( function( event ) { 
				$.wikiEditor.modules.templateEditor.fn.createDialog( $wrapper ); 
				event.stopPropagation(); 
				return false; 
				} )
			.mousedown( function( event ) { event.stopPropagation(); return false; } );
		$template.find( '.wikiEditor-template-expand' )
			.click( function( event ) { 
				$.wikiEditor.modules.templateEditor.fn.toggleWikiTextEditor( $wrapper ); 
				event.stopPropagation();
				return false; 
				} )
			.mousedown( function( event ) { event.stopPropagation(); return false; } );
	},
	/**
	 * Toggle the visisbilty of the wikitext for a given template
	 * @param $wrapper The origianl wrapper we want expand/collapse
	 */
	 toggleWikiTextEditor: function( $wrapper ) {
		var context = $wrapper.data( 'marker' ).context;
		var $template = $wrapper.parent( '.wikiEditor-template' );
		context.fn.purgeOffsets();
		$template
			.toggleClass( 'wikiEditor-template-expanded' )
			.toggleClass( 'wikiEditor-template-collapsed' ) ;
		
		var $templateText = $template.find( '.wikiEditor-template-text' );		
		$templateText.toggleClass( 'wikiEditor-template-text-shrunken' );
		$templateText.toggleClass( 'wikiEditor-template-text-visible' );
		if( $templateText.hasClass('wikiEditor-template-text-shrunken') ){
			//we just closed the template
		
			// Update the model if we need to
			if ( $templateText.html() != $templateText.data( 'oldHTML' ) ) {
				var templateModel = $.wikiEditor.modules.templateEditor.fn.updateModel( $templateText );
				
				//this is the only place the template name can be changed; keep the template name in sync
				var $tLabel = $template.find( '.wikiEditor-template-label' );
				$tLabel.text( $.wikiEditor.modules.templateEditor.fn.getTemplateDisplayName( templateModel ) );
			}
			
		}
	},
	/**
	 * Create a dialog for editing a given template and open it
	 * @param $wrapper The origianl wrapper for which to create the dialog
	*/
	createDialog: function( $wrapper ) {
		var context = $wrapper.data( 'marker' ).context;
		var $template = $wrapper.parent( '.wikiEditor-template' );
		var dialog = {
			'titleMsg': 'wikieditor-template-editor-dialog-title',
			'id': 'wikiEditor-template-dialog',
			'html': '\
				<fieldset>\
					<div class="wikiEditor-template-dialog-title" />\
					<div class="wikiEditor-template-dialog-fields" />\
				</fieldset>',
			init: function() {
				$(this).find( '[rel]' ).each( function() {
					$(this).text( mw.usability.getMsg( $(this).attr( 'rel' ) ) );
				} );
			},
			dialog: {
				width: 600,
				height: 400,
				dialogClass: 'wikiEditor-toolbar-dialog',
				buttons: {
					'wikieditor-template-editor-dialog-submit': function() {
						// More user feedback
						var $templateDiv = $( this ).data( 'templateDiv' );
						context.fn.highlightLine( $templateDiv );

						var $templateText = $templateDiv.children( '.wikiEditor-template-text' );
						var templateModel = $templateText.data( 'model' );
						$( this ).find( '.wikiEditor-template-dialog-field-wrapper textarea' ).each( function() {
							// Update the value
							templateModel.setValue( $( this ).data( 'name' ), $( this ).val() );
						});
						//keep text consistent
						$.wikiEditor.modules.templateEditor.fn.updateModel( $templateText, templateModel );

						$( this ).dialog( 'close' );
					},
					'wikieditor-template-editor-dialog-cancel': function() {
						$(this).dialog( 'close' );
					}
				},
				open: function() {
					var $templateDiv = $( this ).data( 'templateDiv' );
					var $templateText = $templateDiv.children( '.wikiEditor-template-text' );
					var templateModel = $templateText.data( 'model' );
					// Update the model if we need to
					if ( $templateText.html() != $templateText.data( 'oldHTML' ) ) {
						templateModel = $.wikiEditor.modules.templateEditor.fn.updateModel( $templateText );
					}

					// Build the table
					// TODO: Be smart and recycle existing table
					var params = templateModel.getAllInitialParams();
					var $fields = $( this ).find( '.wikiEditor-template-dialog-fields' );
					// Do some bookkeeping so we can recycle existing rows
					var $rows = $fields.find( '.wikiEditor-template-dialog-field-wrapper' );
					for ( var paramIndex in params ) {
						var param = params[paramIndex];
						if ( typeof param.name == 'undefined' ) {
							// param is the template name, skip it
							continue;
						}
						var paramText = typeof param == 'string' ?
							param.name.replace( /[\_\-]/g, ' ' ) :
							param.name;
						var paramVal = templateModel.getValue( param.name );
						if ( $rows.length > 0 ) {
							// We have another row to recycle
							var $row = $rows.eq( 0 );
							$row.children( 'label' ).text( paramText );
							$row.children( 'textarea' )
								.data( 'name', param.name )
								.val( paramVal )
								.each( function() {
									$(this).css( 'height', $(this).val().length > 24 ? '4.5em' : '1.5em' );
								} )
							$rows = $rows.not( $row );
						} else {
							// Create a new row
							var $paramRow = $( '<div />' )
								.addClass( 'wikiEditor-template-dialog-field-wrapper' );
							$( '<label />' )
								.text( paramText )
								.appendTo( $paramRow );
							$( '<textarea />' )
								.data( 'name', param.name )
								.val( paramVal )
								.each( function() {
									$(this).css( 'height', $(this).val().length > 24 ? '4.5em' : '1.5em' );
								} )
								.data( 'expanded', false )
								.bind( 'cut paste keypress click change', function( e ) {
									// If this was fired by a tab keypress, let it go
									if ( e.keyCode == '9' ) return true;
									var $this = $( this );
									setTimeout( function() {
										var expanded = $this.data( 'expanded' );
										if ( $this.val().indexOf( '\n' ) != -1 || $this.val().length > 24 ) {
											if ( !expanded ) {
												$this.animate( { 'height': '4.5em' }, 'fast' );
												$this.data( 'expanded', true );
											}
										} else {
											if ( expanded ) {
												$this.animate( { 'height': '1.5em' }, 'fast' );
												$this.data( 'expanded', false );
											}
										}
									}, 0 );
								} )
								.appendTo( $paramRow );
							$paramRow
								.append( '<div style="clear:both"></div>' )
								.appendTo( $fields );
						}
					}

					// Remove any leftover rows
					$rows.remove();
					$fields.find( 'label' ).autoEllipsis();
					// Ensure our close button doesn't recieve the ui-state-focus class 
					$( this ).parent( '.ui-dialog' ).find( '.ui-dialog-titlebar-close' )
						.removeClass( 'ui-state-focus' );
					
					// Set tabindexes on form fields if needed
					// First unset the tabindexes on the buttons and existing form fields
					// so the order doesn't get messed up
					var $needTabindex = $( this ).closest( '.ui-dialog' ).find( 'button, textarea' );
					if ( $needTabindex.not( '[tabindex]' ).length ) {
						// Only do this if there actually are elements missing a tabindex
						$needTabindex.removeAttr( 'tabindex' );
						$.wikiEditor.modules.dialogs.fn.setTabindexes( $needTabindex );
					}
				}
			}
		};
		// Lazy-create the dialog at this time
		context.$textarea.wikiEditor( 'addDialog', { 'templateEditor': dialog } );
		$( '#' + dialog.id )
			.data( 'templateDiv', $template )
			.dialog( 'open' );
	},
	/**
	 * Update a template's model and HTML
	 * @param $templateText Wrapper <span> containing the template text
	 * @param model Template model to use, will be generated if not set
	 * @return model object
	 */
	updateModel: function( $templateText, model ) {
		var context = $templateText.data( 'marker' ).context;
		var text;
		if ( typeof model == 'undefined' ) {
			text = context.fn.htmlToText( $templateText.html() );
		} else {
			text = model.getText();
		}
		// To keep stuff simple but not break it, we need to do encode newlines as <br>s
		$templateText.text( text );
		$templateText.html( $templateText.html().replace( /\n/g, '<br />' ) );
		$templateText.data( 'oldHTML', $templateText.html() );
		if ( typeof model == 'undefined' ) {
			model = new $.wikiEditor.modules.templateEditor.fn.model( text );
			$templateText.data( 'model', model );
		}
		return model;
	},
	
	/**
	 * Gets template display name
	 */
	getTemplateDisplayName: function ( model ) {
		var tName = model.getName();
		if( model.getValue( 'name' ) != '' ) {
			return tName + ': ' + model.getValue( 'name' );
		} else if( model.getValue( 'Name' ) != '' ) {
			return tName + ': ' + model.getValue( 'Name' );
		} else if( tName.toLowerCase() in $.wikiEditor.modules.templateEditor.nameMappings ) {
			return tName + ': ' + model.getValue( $.wikiEditor.modules.templateEditor.nameMappings[tName.toLowerCase()] );
		}
		return tName;
	},
	
	/**
	 * Builds a template model from given wikitext representation, allowing object-oriented manipulation of the contents
	 * of the template while preserving whitespace and formatting.
	 * 
	 * @param wikitext String of wikitext content
	 */
	model: function( wikitext ) {
		
		/* Private members */
		
		var collapsible = true;
		
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
		
		this.isCollapsible = function() {
			return collapsible;
		}
		
		/**
		 *  Update ranges if there's been a change in one or more 'segments' of the template.
		 *  Removes adjustment function so adjustment is only made once ever.
		 */

		this.updateRanges = function() {
			var adjustment = 0;
			for (var i = 0 ; i < ranges.length; i++ ) {
				ranges[i].begin += adjustment;
				if( typeof ranges[i].adjust != 'undefined' ) {
					adjustment += ranges[i].adjust();
					// NOTE: adjust should be a function that has the information necessary to calculate the length of
					// this 'segment'
					delete ranges[i].adjust;
				}
				ranges[i].end += adjustment;
			}
		};
		
		// Whitespace* {{ whitespace* nonwhitespace:
		if ( wikitext.match( /\s*{{\s*[^\s|]*:/ ) ) {
			collapsible = false; // is a parser function
		}
		/*
		 * Take all template-specific characters that are not particular to the template we're looking at, namely {|=},
		 * and convert them into something harmless, in this case 'X'
		 */
		// Get rid of first {{ with whitespace
		var sanatizedStr = wikitext.replace( /{{/, "  " );
		// Replace end
		endBraces = sanatizedStr.match( /}}\s*$/ );
		if ( endBraces ) {
			sanatizedStr = sanatizedStr.substring( 0, endBraces.index ) + "  " +
				sanatizedStr.substring( endBraces.index + 2 );
		}
		
		
		//treat HTML comments like whitespace
		while ( sanatizedStr.indexOf( '<!' ) != -1 ) {
			startIndex = sanatizedStr.indexOf( '<!' );
			endIndex = sanatizedStr.indexOf('-->') + 3;
			if( endIndex < 3 ){
				break;
			}
			sanatizedSegment = sanatizedStr.substring( startIndex,endIndex ).replace( /\S/g , ' ' );
			sanatizedStr =
				sanatizedStr.substring( 0, startIndex ) + sanatizedSegment + sanatizedStr.substring( endIndex );
		}
		
		// Match the open braces we just found with equivalent closing braces note, works for any level of braces
		while ( sanatizedStr.indexOf( '{{' ) != -1 ) {
			startIndex = sanatizedStr.indexOf( '{{' ) + 1;
			openBraces = 2;
			endIndex = startIndex;
			while ( (openBraces > 0)  && (endIndex < sanatizedStr.length) ) {
				var brace = sanatizedStr[++endIndex];
				openBraces += brace == '}' ? -1 : brace == '{' ? 1 : 0;
			}
			sanatizedSegment = sanatizedStr.substring( startIndex,endIndex ).replace( /[{}|=]/g , 'X' );
			sanatizedStr =
				sanatizedStr.substring( 0, startIndex ) + sanatizedSegment + sanatizedStr.substring( endIndex );
		}
		//links, images, etc, which also can nest
		while ( sanatizedStr.indexOf( '[[' ) != -1 ) {
			startIndex = sanatizedStr.indexOf( '[[' ) + 1;
			openBraces = 2;
			endIndex = startIndex;
			while ( (openBraces > 0)  && (endIndex < sanatizedStr.length) ) {
				var brace = sanatizedStr[++endIndex];
				openBraces += brace == ']' ? -1 : brace == '[' ? 1 : 0;
			}
			sanatizedSegment = sanatizedStr.substring( startIndex,endIndex ).replace( /[\[\]|=]/g , 'X' );
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
			collapsible = false; //zero params
		}
		nameMatch = sanatizedStr.substring( 0, divider ).match( /[^\s]/ );
		if ( nameMatch != null ) {
			ranges.push( new Range( 0 ,nameMatch.index ) ); //whitespace and squiggles upto the name
			nameEndMatch = sanatizedStr.substring( 0 , divider ).match( /[^\s]\s*$/ ); //last nonwhitespace character
			templateNameIndex = ranges.push( new Range( nameMatch.index,
				nameEndMatch.index + 1 ) );
			templateNameIndex--; //push returns 1 less than the array
			ranges[templateNameIndex].old = wikitext.substring( ranges[templateNameIndex].begin,
				ranges[templateNameIndex].end );
		} else {
			ranges.push(new Range(0,0));
			ranges[templateNameIndex].old = "";
		}
		params.push( ranges[templateNameIndex].old ); //put something in params (0)
		/*
		 * Start looping over params
		 */
		var currentParamNumber = 0;
		var valueEndIndex = ranges[templateNameIndex].end;
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
				
				//default values, since we'll allow empty values
				valueBeginIndex = oldDivider + 1;
				valueEndIndex = oldDivider + 1;
				
				valueBegin = currentField.match( /\S+/ ); //first nonwhitespace character
				if( valueBegin != null ){
					valueBeginIndex = valueBegin.index + oldDivider+1;
					valueEnd = currentField.match( /[^\s]\s*$/ ); //last nonwhitespace character
					if( valueEnd == null ){ //ie
						continue;
					}
					valueEndIndex = valueEnd.index + oldDivider + 2;
				}
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
					currentParamNumber--;
					continue;
				}
				nameBeginIndex = nameBegin.index + oldDivider + 1;
				// Last nonwhitespace and non } character
				nameEnd = currentName.match( /[^\s]\s*$/ );
				if( nameEnd == null ){ //ie
					continue;
				}
				nameEndIndex = nameEnd.index + oldDivider + 2;
				// All the chars upto now 
				ranges.push( new Range( ranges[ranges.length-1].end, nameBeginIndex ) );
				nameIndex = ranges.push( new Range( nameBeginIndex, nameEndIndex ) ) - 1;
				currentValue = currentField.substring( currentField.indexOf( '=' ) + 1);
				oldDivider += currentField.indexOf( '=' ) + 1;
				
				//default values, since we'll allow empty values
				valueBeginIndex = oldDivider + 1;
				valueEndIndex = oldDivider + 1;
				
				// First nonwhitespace character
				valueBegin = currentValue.match( /\S+/ );
				if( valueBegin != null ){
					valueBeginIndex = valueBegin.index + oldDivider + 1;
					// Last nonwhitespace and non } character
					valueEnd = currentValue.match( /[^\s]\s*$/ );
					if( valueEnd == null ){ //ie
						continue;
					}
					valueEndIndex = valueEnd.index + oldDivider + 2;
				}
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
	} //model
} 
}; } )( jQuery );
/* TOC Module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.toc = {

/**
 * Compatability map
 */
'browsers': {
	// Left-to-right languages
	'ltr': {
		'msie': [['>=', 7]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 10]],
		'safari': [['>=', 4]],
		'chrome': [['>=', 4]]
	},
	// Right-to-left languages
	'rtl': {
		'msie': [['>=', 8]],
		'firefox': [['>=', 3]],
		'opera': [['>=', 10]],
		'safari': [['>=', 4]],
		'chrome': [['>=', 4]]
	}
},
/**
 * Core Requirements
 */
'req': [ 'iframe' ],
/**
 * Configuration
 */
cfg: {
	// Default width of table of contents
	defaultWidth: '166px',
	// Minimum width to allow resizing to before collapsing the table of contents - used when resizing and collapsing
	minimumWidth: '70px',
	// Minimum width of the wikiText area
	textMinimumWidth: '450px',
	// The style property to be used for positioning the flexible module in regular mode
	flexProperty: 'marginRight',
	// Boolean var indicating text direction
	rtl: false
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
	change: function( context, event ) {
		$.wikiEditor.modules.toc.fn.update( context );
	},
	ready: function( context, event ) {
		// Add the TOC to the document
		$.wikiEditor.modules.toc.fn.build( context );
		context.$content.parent()
			.blur( function() {
				var context = event.data.context;
				$.wikiEditor.modules.toc.fn.unhighlight( context );
			});
		$.wikiEditor.modules.toc.fn.improveUI();
		$.wikiEditor.modules.toc.evt.resize( context );
	},
	resize: function( context, event ) {
		var availableWidth = context.$wikitext.width() - parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth ),
			totalMinWidth = parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) + 
				parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth );
		context.$ui.find( '.wikiEditor-ui-right' )
			.resizable( 'option', 'maxWidth', availableWidth );
		if ( context.modules.toc.$toc.data( 'positionMode' ) != 'disabled' && 
			context.$wikitext.width() < totalMinWidth ) {
				$.wikiEditor.modules.toc.fn.disable( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'disabled'  &&
			context.$wikitext.width() >  totalMinWidth ) {
				$.wikiEditor.modules.toc.fn.enable( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'regular'  &&
			context.$ui.find( '.wikiEditor-ui-right' ).width() > availableWidth ) {
			//switch mode
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy'  &&
			context.modules.toc.$toc.data( 'previousWidth' ) < context.$wikitext.width() ) {
			//switch mode
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		}
		if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			context.modules.toc.$toc.find( 'div' ).autoEllipsis(
				{ 'position': 'right', 'tooltip': true, 'restoreText': true }
			);
		}
		// reset the height of the TOC
		if ( !context.modules.toc.$toc.data( 'collapsed' ) ){
			context.modules.toc.$toc.height(
				context.$ui.find( '.wikiEditor-ui-left' ).height() - 
				context.$ui.find( '.tab-toc' ).outerHeight()
			);
		}

		// store the width of the view for comparison on next resize
		context.modules.toc.$toc.data( 'previousWidth', context.$wikitext.width() );
	},
	mark: function( context, event ) {
		var hash = '';
		var markers = context.modules.highlight.markers;
		var tokenArray = context.modules.highlight.tokenArray;
		var outline = context.data.outline = [];
		var h = 0;
		for ( var i = 0; i < tokenArray.length; i++ ) {
			if ( tokenArray[i].label != 'TOC_HEADER' ) {
				continue;
			}
			h++;
			markers.push( {
				index: h,
				start: tokenArray[i].tokenStart,
				end: tokenArray[i].offset,
				type: 'toc',
				anchor: 'tag',
				afterWrap: function( node ) {
					var marker = $( node ).data( 'marker' );
					$( node ).addClass( 'wikiEditor-toc-header' )
						.addClass( 'wikiEditor-toc-section-' + marker.index )
						.data( 'section', marker.index );
				},
				beforeUnwrap: function( node ) {
					$( node ).removeClass( 'wikiEditor-toc-header' )
						.removeClass( 'wikiEditor-toc-section-' + $( node ).data( 'section' ) );
				},
				onSkip: function( node ) {
					var marker = $( node ).data( 'marker' );
					if ( $( node ).data( 'section' ) != marker.index ) {
						$( node )
							.removeClass( 'wikiEditor-toc-section-' + $( node ).data( 'section' ) )
							.addClass( 'wikiEditor-toc-section-' + marker.index )
							.data( 'section', marker.index );
					}
				},
				getAnchor: function( ca1, ca2 ) {
					return $( ca1.parentNode ).is( '.wikiEditor-toc-header' ) ?
						ca1.parentNode : null;
				}
			} );
			hash += tokenArray[i].match[2] + '\n';
			outline.push ( {
				'text': tokenArray[i].match[2],
				'level': tokenArray[i].match[1].length,
				'index': h
			} );
		}
		// Only update the TOC if it's been changed - we do this by comparing a hash of the headings this time to last
		if ( typeof context.modules.toc.lastHash == 'undefined' || context.modules.toc.lastHash !== hash ) {
			$.wikiEditor.modules.toc.fn.build( context );
			$.wikiEditor.modules.toc.fn.update( context );
			// Remember the changed version
			context.modules.toc.lastHash = hash;
		}
	}
},
exp: [
	{ 'regex': /^(={1,6})([^\r\n]+?)\1\s*$/m, 'label': 'TOC_HEADER', 'markAfter': true }
],
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
		$.wikiEditor.modules.toc.cfg.rtl = config.rtl;
		$.wikiEditor.modules.toc.cfg.flexProperty = config.rtl ? 'marginLeft' : 'marginRight';
		var height = context.$ui.find( '.wikiEditor-ui-left' ).height();
		context.modules.toc.$toc = $( '<div />' )
			.addClass( 'wikiEditor-ui-toc' )
			.data( 'context', context )
			.data( 'positionMode', 'regular' )
			.data( 'collapsed', false );
			context.$ui.find( '.wikiEditor-ui-right' )
				.append( context.modules.toc.$toc );
			context.modules.toc.$toc.height(
				context.$ui.find( '.wikiEditor-ui-left' ).height()
			);
			$.wikiEditor.modules.toc.fn.redraw( context, $.wikiEditor.modules.toc.cfg.defaultWidth );
		},
		
	
	redraw: function( context, fixedWidth ) {
		var fixedWidth = parseFloat( fixedWidth );
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'regular' ) {
			context.$ui.find( '.wikiEditor-ui-right' )
			.css( 'width', fixedWidth + 'px' );
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, ( -1 * fixedWidth ) + 'px' )
				.children()
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, fixedWidth + 'px' );
		} else if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( 'width', fixedWidth );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( $.wikiEditor.modules.toc.cfg.rtl ? 'right': 'left', fixedWidth );
			context.$wikitext.css( 'height', context.$ui.find( '.wikiEditor-ui-right' ).height() );
		}
	},
	switchLayout: function( context ) {
		var width,
			height = context.$ui.find( '.wikiEditor-ui-right' ).height();
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'regular'
		 	&& !context.modules.toc.$toc.data( 'collapsed' ) ) {
			// store position mode
			context.modules.toc.$toc.data( 'positionMode', 'goofy' );
			// store the width of the TOC, to ensure we dont allow it to be larger than this when switching back
			context.modules.toc.$toc.data( 'positionModeChangeAt', 
				context.$ui.find( '.wikiEditor-ui-right' ).width() );
			width = $.wikiEditor.modules.toc.cfg.textMinimumWidth;
			// set our styles for goofy mode
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, '')
				.css( { 'position': 'absolute', 'float': 'none',
					'left': $.wikiEditor.modules.toc.cfg.rtl ? 'auto': 0, 
					'right' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto' } )
				.children()
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, '' );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( { 'width': 'auto', 'position': 'absolute', 'float': 'none',
				'right': $.wikiEditor.modules.toc.cfg.rtl ? 'auto': 0, 
				'left' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto' } );
			context.$wikitext
				.css( 'position', 'relative' );
		} else if ( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			// store position mode
			context.modules.toc.$toc.data( 'positionMode', 'regular' );
			// set width
			width = context.$wikitext.width() - context.$ui.find( '.wikiEditor-ui-left' ).width();
			if ( width > context.modules.toc.$toc.data( 'positionModeChangeAt' ) ) {
				width = context.modules.toc.$toc.data( 'positionModeChangeAt' );
			}
			// set our styles for regular mode
			context.$wikitext
				.css( { 'position': '', 'height': '' } );
			context.$ui.find( '.wikiEditor-ui-right' )
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, '' )
				.css( { 'position': '', 'left': '', 'right': '', 'float': '', 'top': '', 'height': '' } );
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( { 'width': '', 'position': '', 'left': '', 'float': '', 'right': '' } );
		}
		$.wikiEditor.modules.toc.fn.redraw( context, width );
	},
	disable: function( context ) {
		if ( context.modules.toc.$toc.data( 'collapsed' ) ) {
			context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		} else {
			if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
				$.wikiEditor.modules.toc.fn.switchLayout( context );
			}
			context.$ui.find( '.wikiEditor-ui-right' ).hide();
			context.$ui.find( '.wikiEditor-ui-left' )
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, '' )
				.children()
				.css( $.wikiEditor.modules.toc.cfg.flexProperty, '' );
		}
		context.modules.toc.$toc.data( 'positionMode', 'disabled' );
	},
	enable: function( context ) {
		context.modules.toc.$toc.data( 'positionMode', 'regular' );
		if ( context.modules.toc.$toc.data( 'collapsed' ) ) {
			context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).show();
		} else {
			context.$ui.find( '.wikiEditor-ui-right' ).show();
			$.wikiEditor.modules.toc.fn.redraw( context, $.wikiEditor.modules.toc.cfg.minimumWidth );
			context.modules.toc.$toc.find( 'div' ).autoEllipsis(
				{ 'position': 'right', 'tooltip': true, 'restoreText': true }
			);
		}
	},
	unhighlight: function( context ) {
		// FIXME: For some reason, IE calls this function twice, the first time with context undefined
		// Investigate this when you have time please! In the meantime, the user interaction is working just
		// fine because the second call is valid
		if ( context ) {
			context.modules.toc.$toc.find( 'div' ).removeClass( 'current' );
		}
	},
	/**
	 * Highlight the section the cursor is currently within
	 *
	 * @param {Object} context
	 */
	update: function( context ) {
		var div = context.fn.beforeSelection( 'wikiEditor-toc-header' );
		if ( div === null ) {
			// beforeSelection couldn't figure it out, keep the old highlight state
			return;
		}
		
		$.wikiEditor.modules.toc.fn.unhighlight( context );
		var section = div.data( 'section' ) || 0;
		if ( context.data.outline.length > 0 ) {
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
		var $this = $( this ), 
			context = $this.data( 'context' );
		if( context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
			$.wikiEditor.modules.toc.fn.switchLayout( context );
		}
		var pT = $this.parent().position().top - 1;
		context.modules.toc.$toc.data( 'collapsed', true );
		var leftParam = {}, leftChildParam = {};
		leftParam[ $.wikiEditor.modules.toc.cfg.flexProperty ] = '-1px';
		leftChildParam[ $.wikiEditor.modules.toc.cfg.flexProperty ] = '1px';
		context.$ui.find( '.wikiEditor-ui-left' )
			.animate( leftParam, 'fast', function() {
				$( this ).css( $.wikiEditor.modules.toc.cfg.flexProperty, 0 );
			} )
			.children()
			.animate( leftChildParam, 'fast',  function() { 
				$( this ).css( $.wikiEditor.modules.toc.cfg.flexProperty, 0 ); 
			} );
		context.$ui.find( '.wikiEditor-ui-right' )
			.css( { 
				'marginTop' : '1px', 
				'position' : 'absolute', 
				'left' : $.wikiEditor.modules.toc.cfg.rtl ? 0 : 'auto', 
				'right' : $.wikiEditor.modules.toc.cfg.rtl ? 'auto' : 0, 
				'top' : pT } )
			.fadeOut( 'fast', function() {
				$( this ).hide()
				.css( { 'marginTop': '0', 'width': '1px' } );
				context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).fadeIn( 'fast' );
				// Let the UI know things have moved around
				context.fn.trigger( 'tocCollapse' );
				context.fn.trigger( 'resize' );
			 } );
			
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
			openWidth = parseFloat( context.modules.toc.$toc.data( 'openWidth' ) ),
			availableSpace = context.$wikitext.width() - parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth );
		if ( availableSpace < $.wikiEditor.modules.toc.cfg.textMinmumWidth ) return false;
		context.modules.toc.$toc.data( 'collapsed', false );
		// check if we've got enough room to open to our stored width
		if ( availableSpace < openWidth ) openWidth = availableSpace;
		context.$ui.find( '.wikiEditor-ui-toc-expandControl' ).hide();
		var leftParam = {}, leftChildParam = {};
		leftParam[ $.wikiEditor.modules.toc.cfg.flexProperty ] = parseFloat( openWidth ) * -1;
		leftChildParam[ $.wikiEditor.modules.toc.cfg.flexProperty ] = openWidth;
		context.$ui.find( '.wikiEditor-ui-left' )
			.animate( leftParam, 'fast' )
			.children()
			.animate( leftChildParam, 'fast' );
		context.$ui.find( '.wikiEditor-ui-right' )
			.show()
			.css( 'marginTop', '1px' )
			.animate( { 'width' : openWidth }, 'fast', function() {
				context.$content.trigger( 'mouseup' );
				$( this ).css( {
					'marginTop' : '0',
					'position' : 'relative',
					'right' : 'auto',
					'left' : 'auto',
					'top': 'auto' } );
					context.fn.trigger( 'tocExpand' );
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
		 * Builds unordered list HTML object from structured outline
		 *
		 * @param {Object} structure Structured outline
		 */
		function buildList( structure ) {
			var list = $( '<ul />' );
			for ( i in structure ) {
				var div = $( '<div />' )
					.addClass( 'section-' + structure[i].index )
					.data( 'index', structure[i].index )
					.mousedown( function() {
						// No dragging!
						return false;
					} )
					.click( function( event ) {
						var wrapper = context.$content.find(
							'.wikiEditor-toc-section-' + $( this ).data( 'index' ) );
						if ( wrapper.size() == 0 )
							wrapper = context.$content;
						context.fn.scrollToTop( wrapper, true );
						context.$textarea.textSelection( 'setSelection', {
							'start': 0,
							'startContainer': wrapper
						} );
						// Bring user's eyes to the point we've now jumped to
						context.fn.highlightLine( $( wrapper ) );
						// Highlight the clicked link
						$.wikiEditor.modules.toc.fn.unhighlight( context );
						$( this ).addClass( 'current' );
						
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
				.mousedown( function( e ) {
					// No dragging!
					e.preventDefault();
					return false;
				} )
				.bind( 'click.wikiEditor-toc', function( e ) {
					context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc' );
					// No dragging!
					e.preventDefault();
					return false;
				} )
				.find( 'a' )
				.text( mw.usability.getMsg( 'wikieditor-toc-hide' ) );
			$expandControl
				.addClass( 'wikiEditor-ui-toc-expandControl' )
				.append( '<a href="#" />' )
				.mousedown( function( e ) {
					// No dragging!
					e.preventDefault();
					return false;
				} )
				.bind( 'click.wikiEditor-toc', function( e ) {
					context.modules.toc.$toc.trigger( 'expand.wikiEditor-toc' );
					// No dragging!
					e.preventDefault();
					return false;
				} )
				.hide()
				.find( 'a' )
				.text( mw.usability.getMsg( 'wikieditor-toc-show' ) );
			$collapseControl.insertBefore( context.modules.toc.$toc );
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
				.resizable( { handles: 'w,e', preventPositionLeftChange: true, 
					minWidth: parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ),
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
						$this.resizable( 'option', 'maxWidth', $this.parent().width() - 
							parseFloat( $.wikiEditor.modules.toc.cfg.textMinimumWidth ) );
						if(context.modules.toc.$toc.data( 'positionMode' ) == 'goofy' ) {
							$.wikiEditor.modules.toc.fn.switchLayout( context );
						}
					},
					resize: function( e, ui ) {
						// for some odd reason, ui.size.width seems a step ahead of what the *actual* width of
						// the resizable is
						$( this ).css( { 'width': ui.size.width, 'top': 'auto', 'height': 'auto' } )
							.data( 'wikiEditor-ui-left' )
								.css( $.wikiEditor.modules.toc.cfg.flexProperty, ( -1 * ui.size.width ) )
							.children().css( $.wikiEditor.modules.toc.cfg.flexProperty, ui.size.width );
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					},
					stop: function ( e, ui ) {
						context.$ui.find( '.wikiEditor-ui-resize-mask' ).remove();
						context.$content.trigger( 'mouseup' );
						if( ui.size.width <= parseFloat( $.wikiEditor.modules.toc.cfg.minimumWidth ) ) {
							context.modules.toc.$toc.trigger( 'collapse.wikiEditor-toc' );
						} else {
							context.modules.toc.$toc.find( 'div' ).autoEllipsis(
								{ 'position': 'right', 'tooltip': true, 'restoreText': true }
							);
							context.modules.toc.$toc.data( 'openWidth', ui.size.width );
							$.cookie( 'wikiEditor-' + context.instance + '-toc-width', ui.size.width );
						}
						// Let the UI know things have moved around
						context.fn.trigger( 'resize' );
					}
				});
			// Convert our east resize handle into a secondary west resize handle
			var handle = $.wikiEditor.modules.toc.cfg.rtl ? 'w' : 'e';
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
				$.wikiEditor.modules.toc.fn.redraw( context, initialWidth );
			}
		}
		
		// Normalize heading levels for list creation
		// This is based on Linker::generateTOC(), so it should behave like the
		// TOC on rendered articles does - which is considdered to be correct
		// at this point in time.
		if ( context.data.outline ) {
			var outline = context.data.outline;
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
				structure.unshift( { 'text': wgPageName.replace( /_/g, ' ' ), 'level': 1, 'index': 0 } );
			}
			context.modules.toc.$toc.html( buildList( structure ) );
			
			if ( wgNavigableTOCResizable && !context.$ui.data( 'resizableDone' ) ) {
				buildResizeControls();
				buildCollapseControls();
			}
			context.modules.toc.$toc.find( 'div' ).autoEllipsis(
				{ 'position': 'right', 'tooltip': true, 'restoreText': true }
			);
		}
	},
	improveUI: function() {
		/*
		 * Extending resizable to allow west resizing without altering the left position attribute
		 */
		$.ui.plugin.add( "resizable", "preventPositionLeftChange", {
			resize: function( event, ui ) {
				$( this ).data( "resizable" ).position.left = 0;
			}
		} );
	}
}

};

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
					var $sections = context.modules.toolbar.$toolbar.find( 'div.sections' );
					var $tabs = context.modules.toolbar.$toolbar.find( 'div.tabs' );
					for ( section in data[type] ) {
						if ( section == 'main' ) {
							// Section
							context.modules.toolbar.$toolbar.prepend(
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
					var $section = context.modules.toolbar.$toolbar.find( 'div[rel=' + data.section + '].section' );
					for ( group in data[type] ) {
						// Group
						$section.append(
							$.wikiEditor.modules.toolbar.fn.buildGroup( context, group, data[type][group] )
						);
					}
					break;
				case 'tools':
					if ( ! ( 'section' in data && 'group' in data ) ) {
						continue;
					}
					var $group = context.modules.toolbar.$toolbar.find(
						'div[rel=' + data.section + '].section ' + 'div[rel=' + data.group + '].group'
					);
					for ( tool in data[type] ) {
						// Tool
						$group.append( $.wikiEditor.modules.toolbar.fn.buildTool( context, tool,data[type][tool] ) );
					}
					if ( $group.children().length ) {
						$group.show();
					}
					break;
				case 'pages':
					if ( ! ( 'section' in data ) ) {
						continue;
					}
					var $pages = context.modules.toolbar.$toolbar.find(
						'div[rel=' + data.section + '].section .pages'
					);
					var $index = context.modules.toolbar.$toolbar.find(
						'div[rel=' + data.section + '].section .index'
					);
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
								.mousedown( function( e ) {
									context.fn.saveStuffForIE();
									// No dragging!
									e.preventDefault();
									return false;
								} )
								.click( function( e ) {
									$.wikiEditor.modules.toolbar.fn.doAction( $(this).parent().data( 'context' ),
										$(this).parent().data( 'actions' )[$(this).attr( 'rel' )] );
									e.preventDefault();
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
			var group = null;
			if ( typeof data.group == 'string' ) {
				// Toolbar group
				target += ' div[rel=' + data.group + '].group';
				if ( typeof data.tool == 'string' ) {
					// Save for later checking if empty
					group = target;
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
			context.modules.toolbar.$toolbar.find( target ).remove();
			// Hide empty groups
			if ( group ) {
				$group = context.modules.toolbar.$toolbar.find( group );
				if ( $group.children().length == 0 ) {
					$group.hide();
				}
			}
		}
	}
},
/**
 * Event handlers
 */
evt: {
	resize: function( context, event ) {
		context.$ui.find( '.sections' ).height( context.$ui.find( '.sections .section-visible' ).outerHeight() );
	},
	tocCollapse: function( context, event ) {
		$.wikiEditor.modules.toolbar.evt.resize( context, event );
	},
	tocExpand: function( context, event ) {
		$.wikiEditor.modules.toolbar.evt.resize( context, event );
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
		if ( $.trackAction != undefined && source.closest( '.wikiEditor-ui-toolbar' ).size() ) {
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
			$.trackAction( id );
		}
		switch ( action.type ) {
			case 'replace':
			case 'encapsulate':
				var parts = {
					'pre' : $.wikiEditor.autoMsg( action.options, 'pre' ),
					'peri' : $.wikiEditor.autoMsg( action.options, 'peri' ),
					'post' : $.wikiEditor.autoMsg( action.options, 'post' )
				};
				var replace = action.type == 'replace';
				if ( 'regex' in action.options && 'regexReplace' in action.options ) {
					var selection = context.$textarea.textSelection( 'getSelection' );
					if ( selection != '' && selection.match( action.options.regex ) ) {
						parts.peri = selection.replace( action.options.regex,
							action.options.regexReplace );
						parts.pre = parts.post = '';
						replace = true;
					}
				}
				context.$textarea.textSelection(
					'encapsulateSelection',
					$.extend( {}, action.options, parts, { 'replace': replace } )
				);
				if ( typeof context.$iframe !== 'undefined' ) {
					context.$iframe[0].contentWindow.focus();
				}
				break;
			case 'callback':
				if ( typeof action.execute == 'function' ) {
					action.execute( context );
				}
				break;
			case 'dialog':
				context.fn.saveSelection();
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
					// Consider a group with only hidden tools empty as well
					// .is( ':visible' ) always returns false because tool is not attached to the DOM yet
					empty = empty && tool.css( 'display' ) == 'none';
					$group.append( tool );
				}
			}
		}
		if ( empty ) {
			$group.hide();
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
				var src = $.wikiEditor.autoIcon( tool.icon, $.wikiEditor.imgPath + 'toolbar/' );
				var $button = null;
				if ( 'offset' in tool ) {
					var offsetOrIcon = $.wikiEditor.autoIconOrOffset( tool.icon, tool.offset,
						$.wikiEditor.imgPath + 'toolbar/'
					);
					if ( typeof offsetOrIcon == 'object' ) {
						$button = $( '<span />' )
							.attr( {
								'alt' : label,
								'title' : label,
								'rel' : id,
								'class' : 'wikiEditor-toolbar-spritedButton'
							} )
							.text( label )
							.css( 'backgroundPosition', offsetOrIcon[0] + 'px ' + offsetOrIcon[1] + 'px' );
					}
				}
				if ( !$button ) {
					$button = $( '<img />' )
						.attr( {
							'src' : src,
							'width' : 22,
							'height' : 22,
							'alt' : label,
							'title' : label,
							'rel' : id,
							'class' : 'tool tool-button'
						} );
				}
				if ( 'action' in tool ) {
					$button
						.data( 'action', tool.action )
						.data( 'context', context )
						.mousedown( function( e ) {
							context.fn.saveStuffForIE();
							// No dragging!
							e.preventDefault();
							return false;
						} )
						.click( function( e ) {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).data( 'context' ), $(this).data( 'action' ), $(this)
							);
							e.preventDefault();
							return false;
						} );
					// If the action is a dialog that hasn't been set up yet, hide the button
					// until the dialog is loaded
					if ( tool.action.type == 'dialog' &&
							!( tool.action.module in $.wikiEditor.modules.dialogs.modules ) ) {
						$button.hide();
						// JavaScript won't propagate the $button variable itself, it needs help
						context.$textarea.bind( 'wikiEditor-dialogs-setup-' + tool.action.module,
							{ button: $button }, function( event ) {
								event.data.button.show().parent().show();
						} );
					}
				}
				return $button;
			case 'select':
				var $select = $( '<div />' )
					.attr( { 'rel' : id, 'class' : 'tool tool-select' } );
				var $options = $( '<div />' ).addClass( 'options' );
				if ( 'list' in tool ) {
					for ( option in tool.list ) {
						var optionLabel = $.wikiEditor.autoMsg( tool.list[option], 'label' );
						$options.append(
							$( '<a />' )
								.data( 'action', tool.list[option].action )
								.data( 'context', context )
								.mousedown( function( e ) {
									context.fn.saveStuffForIE();
									// No dragging!
									e.preventDefault();
									return false;
								} )
								.click( function( e ) {
									$.wikiEditor.modules.toolbar.fn.doAction(
										$(this).data( 'context' ), $(this).data( 'action' ), $(this)
									);
									// Hide the dropdown
									// Sanity check: if this somehow gets called while the dropdown
									// is hidden, don't show it
									if ( $(this).parent().is( ':visible' ) ) {
										$(this).parent().animate( { 'opacity': 'toggle' }, 'fast' );
									}
									e.preventDefault();
									return false;
								} )
								.text( optionLabel )
								.addClass( 'option' )
								.attr( { 'rel': option, 'href': '#' } )
						);
					}
				}
				$select.append( $( '<div />' ).addClass( 'menu' ).append( $options ) );
				$select.append( $( '<a />' )
						.addClass( 'label' )
						.text( label )
						.data( 'options', $options )
						.attr( 'href', '#' )
						.mousedown( function( e ) {
							// No dragging!
							e.preventDefault();
							return false;
						} )
						.click( function( e ) {
							$(this).data( 'options' ).animate( { 'opacity': 'toggle' }, 'fast' );
							e.preventDefault();
							return false;
						} )
				);
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
			.mousedown( function( e ) {
				// No dragging!
				e.preventDefault();
				return false;
			} )
			.click( function( event ) {
				$(this).parent().parent().find( '.page' ).hide();
				$(this).parent().parent().find( '.page-' + $(this).attr( 'rel' ) ).show();
				$(this).siblings().removeClass( 'current' );
				$(this).addClass( 'current' );
				var section = $(this).parent().parent().attr( 'rel' );
				$.cookie(
					'wikiEditor-' + $(this).data( 'context' ).instance + '-booklet-' + section + '-page',
					$(this).attr( 'rel' ),
					{ expires: 30, path: '/' }
				);
				// Click tracking
				if($.trackAction != undefined){
					$.trackAction(section + '.' + $(this).attr('rel'));
				}
				// No dragging!
				event.preventDefault();
				return false;
			} )
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
						.mousedown( function( e ) {
							context.fn.saveStuffForIE();
							// No dragging!
							e.preventDefault();
							return false;
						} )
						.click( function( e ) {
							$.wikiEditor.modules.toolbar.fn.doAction(
								$(this).parent().data( 'context' ),
								$(this).parent().data( 'actions' )[$(this).attr( 'rel' )],
								$(this)
							);
							e.preventDefault();
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
					'type' : 'replace',
					'options' : {
						'peri' : character,
						'selectPeri': false
					}
				}
			};
		} else if ( 0 in character && 1 in character ) {
			character = {
				'label' : character[0],
				'action' : {
					'type' : 'replace',
					'options' : {
						'peri' : character[1],
						'selectPeri': false
					}
				}
			};
		}
		if ( 'action' in character && 'label' in character ) {
			actions[character.label] = character.action;
			return '<span rel="' + character.label + '">' + character.label + '</span>';
		}
	},
	buildTab : function( context, id, section ) {
		var selected = $.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		// Re-save cookie
		if ( selected != null ) {
			$.cookie( 'wikiEditor-' + context.instance + '-toolbar-section', selected, { expires: 30, path: '/' } );
		}
		return $( '<span />' )
			.attr( { 'class' : 'tab tab-' + id, 'rel' : id } )
			.append(
				$( '<a />' )
					.addClass( selected == id ? 'current' : null )
					.attr( 'href', '#' )
					.text( $.wikiEditor.autoMsg( section, 'label' ) )
					.data( 'context', context )
					.mouseup( function( e ) {
						$(this).blur();
					} )
					.mousedown( function( e ) {
						// No dragging!
						e.preventDefault();
						return false;
					} )
					.click( function( e ) {
						var $sections = $(this).data( 'context' ).$ui.find( '.sections' );
						var $section =
							$(this).data( 'context' ).$ui.find( '.section-' + $(this).parent().attr( 'rel' ) );
						var show = $section.css( 'display' ) == 'none';
						$previousSections = $section.parent().find( '.section-visible' );
						$previousSections.css( 'position', 'absolute' );
						$previousSections.removeClass( 'section-visible' );
						$previousSections.fadeOut( 'fast', function() { $(this).css( 'position', 'relative' ); } );
						$(this).parent().parent().find( 'a' ).removeClass( 'current' );
						$sections.css( 'overflow', 'hidden' );
						function animate( $this ) {
							$sections
							.css( 'display', 'block' )
							.animate( { 'height': $section.outerHeight() }, $section.outerHeight() * 2, function() {
								$( this ).css( 'overflow', 'visible' ).css( 'height', 'auto' );
								context.fn.trigger( 'resize' );
							} );
						}
						if ( show ) {
							$section.addClass( 'section-visible' );
							$section.fadeIn( 'fast' );
							if ( $section.hasClass( 'loading' ) ) {
								// Loading of this section was deferred, load it now
								$this = $(this);
								$this.addClass( 'current loading' );
								setTimeout( function() {
									$section.trigger( 'loadSection' );
									animate( $(this) );
									$this.removeClass( 'loading' );
								}, 1000 );
							} else {
								animate( $(this) );
								$(this).addClass( 'current' );
							}
						} else {
							$sections
								.css( 'height', $section.outerHeight() )
								.animate( { 'height': 'hide' }, $section.outerHeight() * 2, function() {
									$(this).css( { 'overflow': 'visible', 'height': 0 } );
									context.fn.trigger( 'resize' );
								} );
						}
						// Click tracking
						if ( $.trackAction != undefined ) {
							$.trackAction( $section.attr('rel') + '.' + ( show ? 'show': 'hide' )  );
						}
						// Save the currently visible section
						$.cookie(
							'wikiEditor-' + $(this).data( 'context' ).instance + '-toolbar-section',
							show ? $section.attr( 'rel' ) : null,
							{ expires: 30, path: '/' }
						);
						e.preventDefault();
						return false;
					} )
			);
	},
	buildSection: function( context, id, section ) {
		var $section = $( '<div />' ).attr( { 'class': section.type + ' section section-' + id, 'rel': id } );
		var selected = $.cookie( 'wikiEditor-' + context.instance + '-toolbar-section' );
		var show = selected == id;
		
		if ( typeof section.deferLoad != 'undefined' && section.deferLoad && id !== 'main' && !show ) {
			// This class shows the spinner and serves as a marker for the click handler in buildTab()
			$section.addClass( 'loading' ).append( $( '<div />' ).addClass( 'spinner' ) );
			$section.bind( 'loadSection', function() {
				$.wikiEditor.modules.toolbar.fn.reallyBuildSection( context, section, $section );
				$section.removeClass( 'loading' );
			} );
		} else {
			$.wikiEditor.modules.toolbar.fn.reallyBuildSection( context, section, $section );
		}
		
		// Show or hide section
		if ( id !== 'main' ) {
			$section.css( 'display', show ? 'block' : 'none' );
			if ( show )
				$section.addClass( 'section-visible' );
		}
		return $section;
	},
	reallyBuildSection : function( context, section, $section ) {
		context.$textarea.trigger( 'wikiEditor-toolbar-buildSection-' + $section.attr( 'rel' ), [section] );
		switch ( section.type ) {
			case 'toolbar':
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
				$section.append( $index ).append( $pages );
				$.wikiEditor.modules.toolbar.fn.updateBookletSelection( context, page, $pages, $index );
				break;
		}
	},
	updateBookletSelection : function( context, id, $pages, $index ) {
		var cookie = 'wikiEditor-' + context.instance + '-booklet-' + id + '-page';
		var selected = $.cookie( cookie );
		// Re-save cookie
		if ( selected != null ) {
			$.cookie( cookie, selected, { expires: 30, path: '/' } );
		}
		var $selectedIndex = $index.find( '*[rel=' + selected + ']' );
		if ( $selectedIndex.size() == 0 ) {
			selected = $index.children().eq( 0 ).attr( 'rel' );
			$.cookie( cookie, selected, { expires: 30, path: '/' } );
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
				var oldValue = $( 'body' ).css( 'position' );
				$( 'body' ).css( 'position', 'static' );
				$( 'body' ).css( 'position', oldValue );
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
// THIS FILE HAS BEEN MODIFIED for use with the mediawiki wikiEditor
// It no longer requires etherpad.collab.ace.easysync2.Changeset
// THIS FILE WAS ORIGINALLY AN APPJET MODULE: etherpad.collab.ace.contentcollector

/**
 * Copyright 2009 Google Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS-IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

var _MAX_LIST_LEVEL = 8;

function sanitizeUnicode(s) {
	return s.replace(/[\uffff\ufffe\ufeff\ufdd0-\ufdef\ud800-\udfff]/g, '?');
}

function makeContentCollector( browser, domInterface ) {
	browser = browser || {};

	var dom = domInterface || {
		isNodeText : function(n) {
			return (n.nodeType == 3);
		},
		nodeTagName : function(n) {
			return n.tagName;
		},
		nodeValue : function(n) {
			return n.nodeValue;
		},
		nodeNumChildren : function(n) {
			return n.childNodes.length;
		},
		nodeChild : function(n, i) {
			return n.childNodes.item(i);
		},
		nodeProp : function(n, p) {
			return n[p];
		},
		nodeAttr : function(n, a) {
			return n.getAttribute(a);
		},
		optNodeInnerHTML : function(n) {
			return n.innerHTML;
		}
	};

	var _blockElems = {
		"div" : 1,
		"p" : 1,
		"pre" : 1,
		"li" : 1
	};
	function isBlockElement(n) {
		return !!_blockElems[(dom.nodeTagName(n) || "").toLowerCase()];
	}
	function textify(str) {
		return sanitizeUnicode(str.replace(/[\n\r ]/g, ' ').replace(/\xa0/g,
				' ').replace(/\t/g, '        '));
	}
	function getAssoc(node, name) {
		return dom.nodeProp(node, "_magicdom_" + name);
	}

	var lines = (function() {
		var textArray = [];
		var self = {
			length : function() {
				return textArray.length;
			},
			atColumnZero : function() {
				return textArray[textArray.length - 1] === "";
			},
			startNew : function() {
				textArray.push("");
				self.flush(true);
			},
			textOfLine : function(i) {
				return textArray[i];
			},
			appendText : function(txt, attrString) {
				textArray[textArray.length - 1] += txt;
				// dmesg(txt+" / "+attrString);
		},
		textLines : function() {
			return textArray.slice();
		},
		// call flush only when you're done
			flush : function(withNewline) {
				
			}
		};
		self.startNew();
		return self;
	}());
	var cc = {};
	function _ensureColumnZero(state) {
		if (!lines.atColumnZero()) {
			_startNewLine(state);
		}
	}
	var selection, startPoint, endPoint;
	var selStart = [ -1, -1 ], selEnd = [ -1, -1 ];
	var blockElems = {
		"div" : 1,
		"p" : 1,
		"pre" : 1
	};
	function _isEmpty(node, state) {
		// consider clean blank lines pasted in IE to be empty
		if (dom.nodeNumChildren(node) == 0)
			return true;
		if (dom.nodeNumChildren(node) == 1 && getAssoc(node, "shouldBeEmpty")
				&& dom.optNodeInnerHTML(node) == "&nbsp;"
				&& !getAssoc(node, "unpasted")) {
			if (state) {
				var child = dom.nodeChild(node, 0);
				_reachPoint(child, 0, state);
				_reachPoint(child, 1, state);
			}
			return true;
		}
		return false;
	}
	function _pointHere(charsAfter, state) {
		var ln = lines.length() - 1;
		var chr = lines.textOfLine(ln).length;
		if (chr == 0 && state.listType && state.listType != 'none') {
			chr += 1; // listMarker
		}
		chr += charsAfter;
		return [ ln, chr ];
	}
	function _reachBlockPoint(nd, idx, state) {
		if (!dom.isNodeText(nd))
			_reachPoint(nd, idx, state);
	}
	function _reachPoint(nd, idx, state) {
		if (startPoint && nd == startPoint.node && startPoint.index == idx) {
			selStart = _pointHere(0, state);
		}
		if (endPoint && nd == endPoint.node && endPoint.index == idx) {
			selEnd = _pointHere(0, state);
		}
	}
	function _incrementFlag(state, flagName) {
		state.flags[flagName] = (state.flags[flagName] || 0) + 1;
	}
	function _decrementFlag(state, flagName) {
		state.flags[flagName]--;
	}
	function _enterList(state, listType) {
		var oldListType = state.listType;
		state.listLevel = (state.listLevel || 0) + 1;
		if (listType != 'none') {
			state.listNesting = (state.listNesting || 0) + 1;
		}
		state.listType = listType;
		return oldListType;
	}
	function _exitList(state, oldListType) {
		state.listLevel--;
		if (state.listType != 'none') {
			state.listNesting--;
		}
		state.listType = oldListType;
	}
	function _produceListMarker(state) {
		
	}
	function _startNewLine(state) {
		if (state) {
			var atBeginningOfLine = lines.textOfLine(lines.length() - 1).length == 0;
			if (atBeginningOfLine && state.listType && state.listType != 'none') {
				_produceListMarker(state);
			}
		}
		lines.startNew();
	}
	cc.notifySelection = function(sel) {
		if (sel) {
			selection = sel;
			startPoint = selection.startPoint;
			endPoint = selection.endPoint;
		}
	};
	cc.collectContent = function(node, state) {
		if (!state) {
			state = {
				flags : {/* name -> nesting counter */}
			};
		}
		var isBlock = isBlockElement(node);
		var isEmpty = _isEmpty(node, state);
		if (isBlock)
			_ensureColumnZero(state);
		var startLine = lines.length() - 1;
		_reachBlockPoint(node, 0, state);
		if (dom.isNodeText(node)) {
			var txt = dom.nodeValue(node);
			var rest = '';
			var x = 0; // offset into original text
			if (txt.length == 0) {
				if (startPoint && node == startPoint.node) {
					selStart = _pointHere(0, state);
				}
				if (endPoint && node == endPoint.node) {
					selEnd = _pointHere(0, state);
				}
			}
			while (txt.length > 0) {
				var consumed = 0;
				if (!browser.firefox || state.flags.preMode) {
					var firstLine = txt.split('\n', 1)[0];
					consumed = firstLine.length + 1;
					rest = txt.substring(consumed);
					txt = firstLine;
				} else { /* will only run this loop body once */
				}
				if (startPoint && node == startPoint.node
						&& startPoint.index - x <= txt.length) {
					selStart = _pointHere(startPoint.index - x, state);
				}
				if (endPoint && node == endPoint.node
						&& endPoint.index - x <= txt.length) {
					selEnd = _pointHere(endPoint.index - x, state);
				}
				var txt2 = txt;
				if ((!state.flags.preMode) && /^[\r\n]*$/.exec(txt)) {
					// prevents textnodes containing just "\n" from being
					// significant
					// in safari when pasting text, now that we convert them to
					// spaces instead of removing them, because in other cases
					// removing "\n" from pasted HTML will collapse words
					// together.
					txt2 = "";
				}
				var atBeginningOfLine = lines.textOfLine(lines.length() - 1).length == 0;
				if (atBeginningOfLine) {
					// newlines in the source mustn't become spaces at beginning
					// of line box
					txt2 = txt2.replace(/^\n*/, '');
				}
				if (atBeginningOfLine && state.listType
						&& state.listType != 'none') {
					_produceListMarker(state);
				}
				lines.appendText(textify(txt2));
				
				x += consumed;
				txt = rest;
				if (txt.length > 0) {
					_startNewLine(state);
				}
			}
			
		} else {
			var tname = (dom.nodeTagName(node) || "").toLowerCase();
			if (tname == "br") {
				_startNewLine(state);
			} else if (tname == "script" || tname == "style") {
				// ignore
			} else if (!isEmpty) {
				var styl = dom.nodeAttr(node, "style");
				var cls = dom.nodeProp(node, "className");

				var isPre = (tname == "pre");
				if ((!isPre) && browser.safari) {
					isPre = (styl && /\bwhite-space:\s*pre\b/i.exec(styl));
				}
				if (isPre)
					_incrementFlag(state, 'preMode');
				var oldListTypeOrNull = null;

				var nc = dom.nodeNumChildren(node);
				for ( var i = 0; i < nc; i++) {
					var c = dom.nodeChild(node, i);
					cc.collectContent(c, state);
				}

				if (isPre)
					_decrementFlag(state, 'preMode');
				
				if (oldListTypeOrNull) {
					_exitList(state, oldListTypeOrNull);
				}
			}
		}
		if (!browser.msie) {
			_reachBlockPoint(node, 1, state);
		}
		if (isBlock) {
			if (lines.length() - 1 == startLine) {
				_startNewLine(state);
			} else {
				_ensureColumnZero(state);
			}
		}

		if (browser.msie) {
			// in IE, a point immediately after a DIV appears on the next line
			//_reachBlockPoint(node, 1, state);
		}
	};
	// can pass a falsy value for end of doc
	cc.notifyNextNode = function(node) {
		// an "empty block" won't end a line; this addresses an issue in IE with
		// typing into a blank line at the end of the document. typed text
		// goes into the body, and the empty line div still looks clean.
		// it is incorporated as dirty by the rule that a dirty region has
		// to end a line.
		if ((!node) || (isBlockElement(node) && !_isEmpty(node))) {
			_ensureColumnZero(null);
		}
	};
	// each returns [line, char] or [-1,-1]
	var getSelectionStart = function() {
		return selStart;
	};
	var getSelectionEnd = function() {
		return selEnd;
	};

	// returns array of strings for lines found, last entry will be "" if
	// last line is complete (i.e. if a following span should be on a new line).
	// can be called at any point
	cc.getLines = function() {
		return lines.textLines();
	};

	// cc.applyHints = function(hints) {
	// if (hints.pastedLines) {
	//
	// }
	// }

	cc.finish = function() {
		lines.flush();
		var lineStrings = cc.getLines();

		lineStrings.length--;

		var ss = getSelectionStart();
		var se = getSelectionEnd();

		function fixLongLines() {
			// design mode does not deal with with really long lines!
			var lineLimit = 2000; // chars
			var buffer = 10; // chars allowed over before wrapping
			var linesWrapped = 0;
			var numLinesAfter = 0;
			for ( var i = lineStrings.length - 1; i >= 0; i--) {
				var oldString = lineStrings[i];
				if (oldString.length > lineLimit + buffer) {
					var newStrings = [];
					while (oldString.length > lineLimit) {
						// var semiloc = oldString.lastIndexOf(';',
						// lineLimit-1);
						// var lengthToTake = (semiloc >= 0 ? (semiloc+1) :
						// lineLimit);
						lengthToTake = lineLimit;
						newStrings.push(oldString.substring(0, lengthToTake));
						oldString = oldString.substring(lengthToTake);
						
					}
					if (oldString.length > 0) {
						newStrings.push(oldString);
					}
					function fixLineNumber(lineChar) {
						if (lineChar[0] < 0)
							return;
						var n = lineChar[0];
						var c = lineChar[1];
						if (n > i) {
							n += (newStrings.length - 1);
						} else if (n == i) {
							var a = 0;
							while (c > newStrings[a].length) {
								c -= newStrings[a].length;
								a++;
							}
							n += a;
						}
						lineChar[0] = n;
						lineChar[1] = c;
					}
					fixLineNumber(ss);
					fixLineNumber(se);
					linesWrapped++;
					numLinesAfter += newStrings.length;

					newStrings.unshift(i, 1);
					lineStrings.splice.apply(lineStrings, newStrings);
					
				}
			}
			return {
				linesWrapped : linesWrapped,
				numLinesAfter : numLinesAfter
			};
		}
		var wrapData = fixLongLines();

		return {
			selStart : ss,
			selEnd : se,
			linesWrapped : wrapData.linesWrapped,
			numLinesAfter : wrapData.numLinesAfter,
			lines : lineStrings
		};
	}

	return cc;
}
