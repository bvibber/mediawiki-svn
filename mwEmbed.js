/*
 * ~mwEmbed ~
 * For details see: http://www.mediawiki.org/wiki/MwEmbed
 *
 * All MediaWiki code is released under the GPL2.
 * For more information visit http://metavid.org/wiki/Code
 *
 * @author Michael Dale
 *
 * mwEmbed uses the following libraries: 
 *
 * jQuery: 
 * http://jquery.com/ 
 *
 * mw.parseUri:
 * http://stevenlevithan.com/demo/parseuri/js/
 *
 */

// The global scope: will be depreciated once we get everything into mw
var _global = this;

/*
* setup the empty global mw object
* will ensure all our functions and variables are properly namespaced
* reducing chance of conflicts
*/
if ( !window['mw'] ) {
	window['mw'] = { }
}


/**
* Default global config values. Configuration values are set via mw.setConfig
* Configuration values should generaly be set prior to dom-ready 
*/  
var mwDefaultConf = {
	// Default skin name
	'skin_name' : 'mvpcf',
	
	// Default jquery ui skin name
	'jui_skin' : 'redmond',
	
	// What tags will be re-written to video player by default
	// set to empty string or null to avoid automatic rewrites
	'rewritePlayerTags' : 'video,audio,playlist',
	
	/**
	* If jQuery / mwEmbed should always be loaded.
	*
	* mwEmbedSetup ignores this flag and is run if:  
	*  If your js calls mw.addOnloadHook ( callback_function )
	*  If your page includes any tags set in config.rewritePlayerTags 
	*
	* This flag increases page performance on pages that do not use mwEmbed 
	* and don't already load jQuery 
	*
	* For examle when including the mwEmbed.js in your blog template 
	* mwEmbed will only load extra js on blog posts that include the video tag.
	*
	* NOTE: Future architecture will probably do away with this flag and refactor it into 
	* a smaller "remotePageMwEmbed.js" script similar to remoteMwEmbed.js in the js2 folder
	*/ 
	'alwaysSetupMwEmbed' : false,
	
	// Default video size ( if no size provided )
	'video_size' : '400x300',
	
	// If the k-skin video player should attribute kaltura
	'k_attribution' : true,
	
	// The path of mvEmbed in mediaWiki folder 
	'mediaWikiPath' : 'js2/mwEmbed/',
	
	//If we are in debug mode ( results in fresh debugg javascript includes )
	'debug' : false
}


// @@todo move these into mw
var global_req_cb = new Array(); // The global request callback array

/**
* The global mw object:
*
* Any global functions/classes that are not jQuery plugins should make
* there way into the mw namespace
*/
( function( $ ) {	
	// list valid skins here:
	$.valid_skins = [ 'mvpcf', 'kskin' ];
	// the version of mwEmbed
	$.version = '1.1';
	
	// special case of commons api url 
	// (used for default subtitles server for media with a "wikiTitleKey" atm)
	// (@@todo eventually we should have  wikiTitleKey be namespaced with interwiki ns
	$.commons_api_url = 'http://commons.wikimedia.org/w/api.php';
	
	/**
	* Some global containers flags 
	*/
	$.skin_list = new Array();
	$.init_done = false;
	$.cb_count = 0;
	$.player_list = new Array(), // The global player list per page
	$.req_cb = new Array() // The global request callback array	

	/**
	* Configuration System: 
	* 
	* Simple system of inherit defauts, with getter setter functions: 	
	*/	
	
	// Local scope configuration var:
	mwConfig = { };
	
	for(var i in mwDefaultConf){
		if( typeof mwConfig[ i ] == 'undefined' )
			mwConfig[ i ] = mwDefaultConf[ i ];
	}
	
	/**
	* Setter for configuration values
	*
	* @param {String} name Name of configuration value
	* @param {String} value Value of configuration name 
	*/
	$.setConfig = function ( name, value ){
		mwConfig[ name ] = value;
	}
	
	/**
	* Getter for configuration values
	*
	* @param {String} name of configuration value to get
	* @return {Mixed} value of configuration key
	* 	returns "false" if key not found
	*/
	$.getConfig = function ( name ){
		if(mwConfig[ name ] )
			return mwConfig[ name ];
		return false;
	}



	/**
	* Language classes mw.lang
	*
	* Localized Language support attempts to mirror some of the functionality of Language.php in MediaWiki
	* It contains methods for loading and transforming msg text
	*
	*/
	$.lang = { };
	
	/**
	* Setup the lang object
	*/
	var messageCache = { };
	var gRuleSet = { };

	/**
	* mw.addMessages function
	* Loads a set of json messages into the messegeCache object.
	*
	* @param json msgSet The set of msgs to be loaded
	*/
	$.addMessages = function( msgSet ) {
		for ( var i in msgSet ) {
			messageCache[ i ] = msgSet[i];
		}
	}

	/**
	* loadRS function
	* Loads a ruleset by given template key ie PLURAL : { //ruleSetObj }
	*
	* @param json ruleSet The ruleset object ( extends  gRuleSet )
	*/
	$.lang.loadRS = function( ruleSet ) {
		for ( var i in ruleSet ) {
			gRuleSet[ i ] = ruleSet[ i ];
		}
	}

	/**
	 * Returns a transformed msg string
	 *
	 * it take a msg key and array of replacement values of form
	 * $1, $2 and does relevant msgkey transformation returning
	 * the user msg.
	 *
	 * @param {String} key The msg key as set by mw.addMessages
	 * @param [mixed] args  An array of replacement strings
	 * @return string
	 */
	$.lang.gM = function( key , args ) {
	
		// Check for missing message key
		if ( ! messageCache[ key ] )
			return '&lt;' + key + '&gt;';

		// swap in the arg values
		var ms =  $.lang.gMsgSwap( key, args );				
		
		// a quick check to see if we need to send the msg via the 'parser'
		// (we can add more detailed check once we support more wiki syntax)
		if ( ms.indexOf( '{{' ) === -1 && ms.indexOf( '[' ) === -1 ) {
			return ms;
		}

		// make sure we have the lagMagic setup:
		// @@todo move to init
		$.lang.magicSetup();
		// send the msg key through the parser
		var pObj = $.parser.pNew( ms );
		// return the transformed msg
		return pObj.getHTML();
	}
	/**
	* gMsgSwap
	*
	* @param string key The msg key as set by mw.addMessages
	* @param [mixed] args  An array or string to be replaced
	* @return string
	*/
	$.lang.gMsgSwap = function( key , args ) {
		if ( ! messageCache[ key ] )
			return '&lt;' + key + '&gt;';// Missing key placeholder
		// get the message string:
		var ms = messageCache[ key ];
		
		// replace values
		if ( typeof args == 'object' || typeof args == 'array' ) {
			for ( var v in args ) {
				// Message test replace arguments start at 1 instead of zero:
				var rep = new RegExp( '\\$' + ( parseInt( v ) + 1 ), 'g' );
				ms = ms.replace( rep, args[v] );
			}
		} else if ( typeof args == 'string' || typeof args == 'number' ) {
			ms = ms.replace( /\$1/g, args );
		}
		return ms;
	}

	/**
	* gMsgNoTrans
	*
	* @returns string The msg key without transforming it
	*/
	$.lang.gMsgNoTrans = function( key ) {
		if ( messageCache[ key ] )
			return messageCache[ key ]

		// Missing key placeholder
		return '&lt;' + key + '&gt;';
	}
	
	/**
	* Add Supported Magic Words to parser
	*/
	// Set the setupflag to false:
	$.lang.doneSetup = false;
	$.lang.magicSetup = function() {
		if ( !$.lang.doneSetup ) {
			$.parser.addTemplateTransform ( {
				'PLURAL' : $.lang.procPLURAL
			} )

			$.lang.doneSetup = true;
		}

	}
	
	/**
	* Process the PLURAL special language template key:
	*/
	$.lang.procPLURAL = function( tObj ) {
		// setup shortcuts
		// (gRuleSet is loaded from script-loader to contains local ruleset)
		var rs = gRuleSet['PLURAL'];

		/*
		 * Plural matchRuleTest
		 */
		function matchRuleTest( cRule, val ) {
			js_log("matchRuleTest:: " + typeof cRule + ' ' + cRule + ' == ' + val );
			function checkValue( compare, val ) {
				if ( typeof compare == 'string' ) {
					range = compare.split( '-' );
					if ( range.length >= 1 ) {
						if ( val >= range[0] &&  val <= range[1] )
							return true;
					}
				}
				// else do a direct compare
				if ( compare == val ) {
					return true;
				}
				return false;
			}
			// check for simple cRule type:
			if ( typeof cRule == 'number' ) {
				return ( parseInt( val ) == parseInt( cRule ) );
			} else if ( typeof cRule == 'object' ) {
				var cmatch = { };
				// if a list we need to match all for rule match
				for ( var i in  cRule ) {
					var cr = cRule[i];
					// set cr type
					var crType =  '';
					for ( var j in cr ) {
						if ( j == 'mod' )
							crType = 'mod'
					}
					switch( crType ) {
						case 'mod':
							if ( cr ['is'] ) {
								if ( checkValue( val % cr['mod'], cr ['is'] ) )
									cmatch[i] = true;
							} else if ( cr['not'] ) {
								if ( ! checkValue( val % cr['mod'], cr ['not'] ) )
									cmatch[i] = true;
							}
						break;
					}
				}
				// check all the matches (taking into consideration "or" order)
				for ( var i in cRule ) {
					if ( ! cmatch[i] )
						return false;
				}
				return true;

			}
		}
		/**
		 * Maps a given rule Index to template params:
		 *
		 * if index is out of range return last param
		 * @param
		 */
		function getTempParamFromRuleInx( tObj, ruleInx ) {
			// js_log('getTempParamFromRuleInx: ruleInx: ' + ruleInx + ' tempParamLength ' + tObj.param.length );
			if ( ruleInx	>= tObj.param.length )
				return  tObj.param[  tObj.param.length - 1 ];
			// else return the requested index:
			return tObj.param[ ruleInx ];
		}
		var rCount = 0
		// run the actual rule lookup:
		for ( var ruleInx in rs ) {
			cRule = rs[ruleInx];
			if ( matchRuleTest( cRule, tObj.arg ) ) {
				js_log("matched rule: " + ruleInx );
				return getTempParamFromRuleInx( tObj, rCount );
			}
			rCount ++;
		}
		js_log('no match found for: ' + tObj.arg + ' using last/other : ' +  tObj.param [ tObj.param.length -1 ] );
		//debugger;
		// return the last /"other" template param
		return tObj.param [ tObj.param.length - 1 ];
	}

	/**
	 * gMsgLoadRemote loads remote msg strings
	 *
	 * @param mixed msgSet the set of msg to load remotely
	 * @param function callback  the callback to issue once string is ready
	 */
	$.lang.gMsgLoadRemote = function( msgSet, callback ) {
		var ammessages = '';
		if ( typeof msgSet == 'object' ) {
			for ( var i in msgSet ) {
				ammessages += msgSet[i] + '|';
			}
		} else if ( typeof msgSet == 'string' ) {
			ammessages += msgSet;
		}
		if ( ammessages == '' ) {
			js_log( 'gMsgLoadRemote: no message set requested' );
			return false;
		}
		do_api_req( {
			'data': {
				'meta': 'allmessages',
				'ammessages': ammessages
			}
		}, function( data ) {
			if ( data.query.allmessages ) {
				var msgs = data.query.allmessages;
				for ( var i in msgs ) {
					var ld = { };
					ld[ msgs[i]['name'] ] = msgs[i]['*'];
					mw.addMessages( ld );
				}
			}
			callback();
		} );
	}
	
	/**
	 * Format a size in bytes for output, using an appropriate
	 * unit (B, KB, MB or GB) according to the magnitude in question
	 *
	 * @param size Size to format
	 * @return string Plain text (not HTML)
	 */
	$.lang.formatSize = function ( size ) {
		// For small sizes no decimal places are necessary
		var round = 0;
		var msg = '';
		if ( size > 1024 ) {
			size = size / 1024;
			if ( size > 1024 ) {
				size = size / 1024;
				// For MB and bigger two decimal places are smarter
				round = 2;
				if ( size > 1024 ) {
					size = size / 1024;
					msg = 'mwe-size-gigabytes';
				} else {
					msg = 'mwe-size-megabytes';
				}
			} else {
				msg = 'mwe-size-kilobytes';
			}
		} else {
			msg = 'mwe-size-bytes';
		}
		// JavaScript does not let you choose the precision when rounding
		var p = Math.pow( 10, round );
		var size = Math.round( size * p ) / p;
		return gM( msg , size );
	};
	
	$.lang.formatNumber = function( num ) {
		/*
		*	addSeparatorsNF
		* @param Str: The number to be formatted, as a string or number.		
		* @param outD: The decimal character for the output, such as ',' for the number 100,2
		* @param sep: The separator character for the output, such as ',' for the number 1,000.2
		*/
		function addSeparatorsNF( nStr, outD, sep ) {
			nStr += '';
			var dpos = nStr.indexOf( '.' );
			var nStrEnd = '';
			if ( dpos != -1 ) {
				nStrEnd = outD + nStr.substring( dpos + 1, nStr.length );
				nStr = nStr.substring( 0, dpos );
			}
			var rgx = /(\d+)(\d{3})/;
			while ( rgx.test( nStr ) ) {
				nStr = nStr.replace( rgx, '$1' + sep + '$2' );
			}
			return nStr + nStrEnd;
		}
		// @@todo read language code and give periods or comas: 
		return addSeparatorsNF( num, '.', ',' );
	}
	
	
	
	/**
	* MediaWiki wikitext "Parser"
	*
	* This is not feature complete but we need a way to get at template properties
	*
	*
	* @param {String} wikiText the wikitext to be parsed
	* @return {Object} parserObj returns a parser object that has methods for getting at
	* things you would want
	*/
	$.parser = { };
	var pMagicSet = { };
	
	/**
	 * parser addTemplateTransform
	 *
	 * Lets you add a set template key to be transformed by a callback function
	 *
	 * @param {Object} magicSet key:callback
	 */
	$.parser.addTemplateTransform = function( magicSet ) {
		for ( var i in magicSet )
			pMagicSet[ i ] = magicSet[i];
	}

	// Create a new parser Object
	$.parser.pNew = function( wikiText, opt ) {
		var parseObj = function( wikiText, opt ) {
			return this.init( wikiText, opt )
		}
		parseObj.prototype = {
			// the wikiText "DOM"... stores the parsed wikiText structure
			// wtDOM : {}, (not yet supported )

			pOut : '', // the parser output string container
			init  :function( wikiText ) {
				this.wikiText = wikiText;
			},
			updateText : function( wikiText ) {
				this.wikiText = wikiText;
				// invalidate the output (will force a re-parse )
				this.pOut = '';
			},
			parse : function() {
				/*
				 * quickly recursive / parse out templates:
				 */

				// ~ probably a better algorithm out there / should mirror php parser flow ~
				//	 (we are already running white-space issues ie php parse strips whitespace differently)
				// or at least expose something similar to: http://www.mediawiki.org/wiki/Extension:Page_Object_Model

				// ... but I am having fun with recursion so here it is...
				function rdpp ( txt , cn ) {
					var node = { };
					// inspect each char
					for ( var a = 0; a < txt.length; a++ ) {
						if ( txt[a] == '{' && txt[a + 1] == '{' ) {
							a = a + 2;
							node['p'] = node;
							if ( !node['c'] )
								node['c'] = new Array();

							node['c'].push( rdpp( txt.substr( a ), true ) );
						} else if ( txt[a] == '}' && txt[a + 1] == '}' ) {
							a = a + 2;
							if ( !node['p'] ) {
								return node;
							}
							node = node['p'];
						}
						if ( !node['t'] )
							node['t'] = '';
						// don't put closures into output:
						if ( txt[a] &&  txt[a] != '}' )
								node['t'] += txt[a];
								
					}
					return node;
				}
				/**
				 * parse template text as template name and named params
				 */
				function parseTmplTxt( ts ) {
					var tObj = { };
					// Get template name:
					tname = ts.split( '\|' ).shift() ;
					tname = tname.split( '\{' ).shift() ;
					tname = tname.replace( /^\s+|\s+$/g, "" ); //trim

					// check for arguments:
					if ( tname.split( ':' ).length == 1 ) {
						tObj["name"] = tname;
					} else {
						tObj["name"] = tname.split( ':' ).shift();
						tObj["arg"] = tname.split( ':' ).pop();
					}
										
					var pSet = ts.split( '\|' );
					pSet.splice( 0, 1 );
					if ( pSet.length ) {
						tObj.param = new Array();
						for ( var pInx in pSet ) {
							var tStr = pSet[ pInx ];
							// check for empty param
							if ( tStr == '' ) {
								tObj.param[ pInx ] = '';
								continue;
							}
							for ( var b = 0 ; b < tStr.length ; b++ ) {
								if ( tStr[b] == '=' && b > 0 && b < tStr.length && tStr[b - 1] != '\\' ) {
									// named param
									tObj.param[ tStr.split( '=' ).shift() ] =	tStr.split( '=' ).pop();
								} else {
									// indexed param
									tObj.param[ pInx ] = tStr;
								}
							}
						}
					}		
					return tObj;
				}
				function getMagicTxtFromTempNode( node ) {
					node.tObj = parseTmplTxt ( node.t );
					// do magic swap if template key found in pMagicSet
					if ( node.tObj.name in pMagicSet ) {
						var nt = pMagicSet[ node.tObj.name ]( node.tObj );
						return nt;
					} else {
						// don't swap just return text
						return node.t;
					}
				}
				/**
				 * recurse_magic_swap
				 *
				 * go last child first swap upward: (could probably be integrated above somehow)
				 */
				var pNode = null;
				function recurse_magic_swap( node ) {
					if ( !pNode )
						pNode = node;

					if ( node['c'] ) {
						// swap all the kids:
						for ( var i in node['c'] ) {
							var nt = recurse_magic_swap( node['c'][i] );
							// swap it into current
							if ( node.t ) {
								node.t = node.t.replace( node['c'][i].t, nt );
							}
							// swap into parent
							pNode.t  = pNode.t.replace( node['c'][i].t, nt );
						}
						// do the current node:
						var nt = getMagicTxtFromTempNode( node );
						pNode.t = pNode.t.replace( node.t , nt );
						// run the swap for the outer most node
						return node.t;
					} else {
						// node.t = getMagicFromTempObj( node.t )
						return getMagicTxtFromTempNode( node );
					}
				}
				// parse out the template node structure:
				this.pNode = rdpp ( this.wikiText );
				// strip out the parent from the root	
				this.pNode['p'] = null;
				
				// do the recursive magic swap text:
				this.pOut = recurse_magic_swap( this.pNode );
			},			
			/*
			 * parsed template api ~loosely based off of ~POM~
			 * http://www.mediawiki.org/wiki/Extension:Page_Object_Model
			 */
			
			/**
			 * templates
			 * 
			 * Get a requested template from the wikitext (if available)
			 *  
			 */
			templates: function( tname ) {
				this.parse();
				var tmplSet = new Array();
				function getMatchingTmpl( node ) {
					if ( node['c'] ) {
						for ( var i in node['c'] ) {
							getMatchingTmpl( node['c'] );
						}
					}
					if ( tname && node.tObj ) {
						if ( node.tObj['name'] == tname )
							tmplSet.push( node.tObj );
					} else if ( node.tObj ) {
						tmplSet.push( node.tObj );
					}
				}
				getMatchingTmpl( this.pNode );
				return tmplSet;
			},
			
			/**
			* getTemplateVars
			* returns a set of template values in a given wikitext page
			* 
			* NOTE: should be integrated with the parser
			*/
			getTemplateVars: function(){
				//js_log('matching against: ' + wikiText);
				templateVars = new Array();
				var tempVars = wikiText.match(/\{\{\{([^\}]*)\}\}\}/gi);
																
				// Clean up results:
				for(var i=0; i < tempVars.length; i++){
					//match 
					var tvar = tempVars[i].replace('{{{','').replace('}}}','');
					
					// Strip anything after a |
					if(tvar.indexOf('|') != -1){
						tvar = tvar.substr(0, tvar.indexOf('|'));
					}
					
					// Check for duplicates:
					var do_add=true;
					for(var j=0; j < templateVars.length; j++){
						if( templateVars[j] == tvar)
							do_add=false;
					}
					
					// Add the template vars to the output obj
					if(do_add)
						templateVars.push( tvar );
				}
				return templateVars;
			},
			
			/**
			 * Returns the transformed wikitext
			 * 
			 * Build output from swappable index 
			 * 		(all transforms must be expanded in parse stage and linearly rebuilt)  
			 * Alternatively we could build output using a place-holder & replace system 
			 * 		(this lets us be slightly more sloppy with ordering and indexes, but probably slower)
			 * 
			 * Ideal: we build a 'wiki DOM' 
			 * 		When editing you update the data structure directly
			 * 		Then in output time you just go DOM->html-ish output without re-parsing anything			   
			 */
			getHTML : function() {
				// wikiText updates should invalidate pOut
				if ( this.pOut == '' ) {
					this.parse();
				}
				return this.pOut;
			}
		};
		// return the parserObj
		return new parseObj( wikiText, opt ) ;
	};		
		
	
	/**
	* The loader prototype:
	*/
	$.loader = {
		/*
		* Javascript Module Loader functions 
		* @key Name of Module
		* @value function code to load module 
		*/
		moduleLoaders : { },
		
		/**
		* Javascript Class Paths
		* @key Name of class
		* @value Class file path 
		*/
		classPaths : { }, 	
		
		/**
		* Style sheet paths for aossicated classes
		* @key Name of the class
		* @value Style sheet path
		*/	
		stylePaths : { },						
		
		/**
		* Core load function: 
		* 
		* @param {Mixed} loadRequest:
		* 
		* 	{String} Name of a module to be loaded
		* 		Modules are added via addModuleLoader and can define custom
		* 		code needed to load the module dependencies
		*		Module loader function should accept a callback argument
		*
		*	{String} Name of a class to loaded. 
		*		Using defined class names avoids loading the same class
		*		twice by first checking if the "class variable" is defined
		*	
		*	{String} Absolute or relative to mwEmbed file path. 
		*		The same file won't be loaded twice
		*
		*	{Array} can be an array of any combination of the above strings.
		*		Will be loaded in-order or in a single 
		*		script-loader request if scriptLoader is enabled 
		*
		* 	{Array} {Array} Can be a set of Arrays for loading.		 
		*		Some browsers execute included scripts out of order. 
		* 		This lets you chain sets of request for thouse browers.
		*		If using the script-loader order is preserved in output and 
		*			a single request will be used.
		*
		* @param {Function} callback Function called once loading is complete
		*/				
		load: function( loadRequest, callback ){
			
			// Check if its a multi-part request: 
			if( typeof loadRequest == 'object' ){									
				this.loadMany ( loadRequest,  callback );
				return ;
			}   
			
			// Check for the module name loader function 
			if( this.moduleLoaders[ loadRequest ] && 
				typeof ( this.moduleLoaders[ loadRequest ] ) == 'function' 
			){
				//Run the module with the parent callback 
				this.moduleLoaders[ loadRequest ]( callback );	
				return ;
			}
			
			// Check for javascript class 
			if( this.classPaths[ loadRequest ] ){		
				this.loadClass( loadRequest, callback );																	
				return ;
			}
			
			// Try loading as a "file"
			if( loadRequest ) { 
				$.getScript( loadRequest, callback );
				return ;
			}
			
			//possible error? 
			js_log( "Error could not handle load request" );
		},
		
		
		/**
		* Load a set of scripts.
		* Will issue many load requests or package the request for the script-loader
		*
		* @param {Object} loadSet Set of scripts to be loaded
		* @param {Function} callback Function to call once all scripts are loaded.
		*/ 
		loadMany: function( loadSet, callback ){
		
			//Setup up the local "loadState"			
			var loadState = { };	
					
			// Check if its a dependency set ( nested objects ) 
			if( typeof loadSet [ 0 ] == 'object' ){				
				//Load sets of classes ( to preserver order for some browsers )
				this.loadDependencyChain( loadSet, callback );
				return ;
			}
			
			// Set the initial load state for every item in the loadSet
			for( var i in loadSet ){							
				var loadName = loadSet[ i ];				
				loadState[ loadName ] = 0;								
			}
			
			// Issue the load request check check loadState to see if we are "done"
			for( var i in loadSet ){			
				var loadName = loadSet[ i ];				
				$.load( loadName, function ( loadName ){								
					loadState[ loadName ] = 1;
					
					var loadDone = true;
					for( var j in loadState ){
						if( loadState[ j ] === 0 )
							loadDone = false;			
					}
					
					// Run the parent scope callback for "loadMany" 
					if( loadDone )
						callback();
				} );
			}
		},
		
		/**
		* Load a sets of scripts satisfy dependency order for browsers that execute out of order
		* 
		* @param {Object} loadChain A set of javascript arrays to be loaded. 
		*	Sets are requested in array order. 		   
		*/ 
		loadDependencyChain: function( loadChain, callback ){
			var _this = this;				
			// Load with dependency checks
			this.load( loadChain.shift(), function() {								
				if ( loadChain.length != 0 ) {
					_this.loadDependencyChain( loadChain, callback );
				} else {
					callback();
				}
			} );
		},
		
		
		/**
		* Loads javascript associated with a className
		*
		* @param {String} className Name of class to load
		* @param {Function} callback Function to run once class is loaded 
		*/
		loadClass: function( className , callback){		
													
			// Make sure the class is not already defined:
			if ( $.isset( className ) ){
				js_log( 'Class ( ' + className + ' ) already defined ' );
				callback( className );
				return ; 									
			}
			
			// Get the class url:
			var baseClassPath = this.classPaths[ className ];
						
			var url = null;
			
			// Add the mwEmbed path if not a root path or a full url
			if( baseClassPath.indexOf( '/' ) !== 0 && 
				baseClassPath.indexOf('://') === -1 ){
				url = $.getMwEmbedPath() + baseClassPath;
			}else{
				url = baseClassPath;
			}
			
			if( ! url ){
				js_log( "Could not get url for class " + className  );						
				return ;
			}	
								
			// Check for any associated style sheets that should be loaded 
			if( typeof this.stylePaths[ className ] != 'undefined' ){
				$.getStyleSheet( this.stylePaths[ className ] );
			}
			
			// Issue the request to load the class (include class name in result callback:					
			$.getScript( url, function( ) {
				callback( className );
			} );
												
		},
		
		/**
		* Adds a module to the mwLoader object 
		*
		* @param {String} name Name of module
		* @param {Function} moduleLoader Function that
		*	loads dependencies for a module
		*/
		addModuleLoader: function( name, moduleLoader ){		
			this.moduleLoaders [ name ] = moduleLoader;
		},
		
		/**
		* Adds file path key value pairs
		*
		* @param {Object} classSet JSON formated list of 
		*  class name file path pairs.
		*
		*  classSet must be strict JSON to allow the 
		*  php scriptLoader to parse the file paths.  
	 	*/
	 	addClassFilePaths: function( classSet ){
	 		for( var i in classSet ){
				this.classPaths[ i ] = classSet[ i ];
			}
	 	},
	 	
		/**
	 	* Add a style sheet to be loaded the same time as the requested class
	 	*
		* NOTE: In general style sheets should be loaded via a module loader function. 
		*  In some cases a single class has a single sheet that can use this function
	 	*/
	 	addClassStyleSheets: function( sheetSet ){
	 		for(var i in sheetSet ){
	 			this.stylePaths[ i ] = sheetSet[ i ];
	 		}
	 	}
	}
	
	/**
	* Shortcut entry points / convenience functions: 
	*
	* Lets you write mw.load() instead of mw.loader.load()
	*/
	
	/**
	* Load Object entry point: Loads a requested set of javascript 
	*/	
	$.load = function( loadRequest, callback ){
		return $.loader.load( loadRequest, callback );
	}
	
	/**
	* Add module entry point: Adds a module to the mwLoader object 
	*/
	$.addModuleLoader = function ( name, loaderFunction ){
		return $.loader.addModuleLoader( name, loaderFunction );		
	}
	
	/**
	* Add Class File Paths entry point:  
	*/
	$.addClassFilePaths = function ( classSet )	{	
		return $.loader.addClassFilePaths( classSet );
	}
			
	/**
	* Add Class Style Sheet entry point:  	 
	*/
	$.addClassStyleSheets = function( sheetSet ){
		return $.loader.addClassStyleSheets( sheetSet );
	}
	
	/**
	* Utility Functions
	*/		
	
	/**
	* Similar to php isset function checks if the variable exists.
	* Does a safe check of a descendent method or variable
	*
	* @param {String} objectPath
	* @return {Boolean}
	* 	true if objectPath exists
	*	false if objectPath is undefined
	*/	
	$.isset = function( objectPath ) {
		if ( !objectPath )
			return false;
		var pathSet = objectPath.split( '.' );
		var cur_path = '';
		for ( var p = 0; p < pathSet.length; p++ ) {
			cur_path = ( cur_path == '' ) ? cur_path + pathSet[p] : cur_path + '.' + pathSet[p];
			eval( 'var ptest = typeof ( ' + cur_path + ' ); ' );
			if ( ptest == 'undefined' ) {				
				return false;
			}
		}
		return true;
	}
		
	
	//Setup the local mwOnLoadFuncitons array: 
	var mwOnLoadFuncitons = new Array();
	
	//mw Ready flag ( set once mwEmbed is ready ) 
	var mwReadyFlag = false;
	
	/**
	* Enables load hooks to run once DOM is "ready" 
	* Will ensure jQuery is available, is in the $j namespace 
	* and mw interfaces and configuration has been loaded and applied
	*
	* @param {Function} callback Function to run once DOM and jQuery are ready
	*/
	$.addOnloadHook = function( callback ){					
		if( mwReadyFlag == false ){
			mwOnLoadFuncitons.push ( callback );
			
			// If the dom is ready but we have not run mwSetup::  
			// Have a rare case where a dynamically included script is calling mw.addOnloadHook ::	
			if ( mwDomReadyFlag && !mwSetupFlag  ){
				$.setupMwEmbed();
			} 
			return ;
		}
		// If mwReadyFlag is already "ready" call the callback directly:
		callback();
	}	
	
	/**
	* Runs all the queued functions
	*/ 
	$.runLoadHooks = function ( ){
		// Sets mwReadyFlag to true so that future addOnLoadHook calls 
		//  know to call the callback directly
		mwReadyFlag = true;
		
		// Run all the queued functions: 
		while( mwOnLoadFuncitons.length )
			mwOnLoadFuncitons.pop()();
	}
	
	/**
	* One time "setup" for mwEmbed 
	* run onDomReady ( so calls to setConfg apply to setup )
	*/
	
	// Flag to ensure setup is only run once:
	var mwSetupFlag = false;
	
	$.setupMwEmbed = function ( ) {	
		// Only run the setup once: 
		if( mwSetupFlag )
			return ;			  
		mwSetupFlag = true;
		
		js_log( 'mw:setupMwEmbed' );
				
		// Make sure jQuery is loaded:
		$.load( 'window.jQuery', function(){			
			if ( !_global['$j'] ) {
				_global['$j'] = jQuery.noConflict();
			}
			
			// Set up the skin paths configuration
			$.setConfig( 'jquery_skin_path', mw.getMwEmbedPath() + 'jquery/jquery.ui/themes/' + mw.getConfig( 'jui_skin' ) + '/' );
			$.setConfig( 'skin_img_path', mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skin_name' ) + '/images/' ); 
			$.setConfig( 'default_video_thumb', mw.getConfig( 'skin_img_path' ) + 'vid_default_thumb.jpg' );

			// Make Core skin/style sheets are always available:
			mw.getStyleSheet( mw.getConfig( 'jquery_skin_path' ) + 'jquery-ui-1.7.1.custom.css' );
			mw.getStyleSheet( mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skin_name' ) + '/styles.css' );

			// Set up AJAX to not send dynamic URLs for loading scripts
			$j.ajaxSetup( {
				cache: true
			} );
			
			// Set up mvEmbed jQuery bindings
			mwDojQueryBindings();
				
			// Check for tag-rewrites ( sometimes checked twice but ensures fresh dom check )  
			if( $.documentHasPlayerTags() ){
				// Load the embedPlayer module ( then run queued hooks )
				mw.load( 'player', function ( ) {
					// Rewrite the rewritePlayerTags with the 
					$j( $.getConfig( 'rewritePlayerTags' ) ).embedPlayer()
					// Run mw hooks:
					mw.runLoadHooks();
				} );
			}else{			
				// Set ready state and run the callback
				mw.runLoadHooks();
			}
		} ); 			
	}
	
	//Flag to register the domReady has been called
	var mwDomReadyFlag = false;
	
	/**
 	* This will get called when the DOM is ready 
 	* Will check configuration and issue a mw.setupMwEmbed call if needed
	*/
	$.domReady = function ( ) {
		if( mwDomReadyFlag )
			return ;
		js_log( 'run:domReady' );
		// Set the onDomReady Flag
		mwDomReadyFlag = true;
		
		// Check for the force setup flag:
		if ( $.getConfig( 'alwaysSetupMwEmbed' ) ){
			$.setupMwEmbed();
			return ;
		}
		
		// Check for rewrite tags: 		
		if ( $.documentHasPlayerTags() ) {
			$.setupMwEmbed();
			return ;
		}		
		
		// Check for queued functions that use mw interfaces: 
		if ( mwOnLoadFuncitons.length ){
			$.setupMwEmbed();
			return ;
		}
		
		// If we have any queued functions we need to run setup as well: 
		
		/*	
			// Load libs and process videos
			mvJsLoader.embedPlayerCheck( function() {
				// Convert all supported elements into embed players:
				$j.embedPlayers( function(){
					// Run any queued global events:			
					mvJsLoader.runQueuedFunctions();
				});
			} );
		} else {
			mvJsLoader.runQueuedFunctions();
		}
		*/
	}
	
	/**
	* Check the current DOM for any tags in "rewritePlayerTags"
	*/
	$.documentHasPlayerTags = function(){		
		var tagElm = $.getPlayerTagElements( true );
		if( tagElm && tagElm.length )
			return true;
		return false;
	}
	/**
	* Get page elements that match the rewritePlayerTags config
	*
	* @param {Boolean} getOne Flag to retive only one tag ( faster for simple has tag checks )  
	*/
	$.getPlayerTagElements = function( getOne ){
		var tagString = $.getConfig( 'rewritePlayerTags' );
		if( ! tagString || tagString == '' )
			return false;
			
		// Tags should be separated by "," 
		var tags = tagString.split(',');
		var tagsInDOM = [ ];
		// Check for tags: 
		for( var i in tags ){
			var tagElements = document.getElementsByTagName( tags[ i ] );			
			for(var j = 0; j < tagElements.length; j++ ){								
				tagsInDOM.push( tagElements[ j ] );
				if( getOne )
					return tagsInDOM;
			}
		}		
		return tagsInDOM;	
	}
	
	/**
	* Wrapper for jQuery getScript
	* 
	* If jQuery is not ready load it.
	*
	* @param {String} url Url of script
	* @param {Function} callback Function to call once script is loaded   
	*/
	$.getScript = function( url, callback ){
		// Add on the request paramaters to the url:
		url += ( url.indexOf( '?' ) === -1 )? '?' : '&';
		
		// Get url Param also updates the "debug" var 
		url += $.getUrlParam();		
		
		js_log( 'mw.getScript: ' + url );		
		
		// If jQuery is available and debug is off get the scirpt j 
		if( $.isset( 'window.jQuery' ) && $.getConfig( 'debug' ) === false ) {
			$j.getScript( url, callback ); 
			return ;
		}
		
		/**
		* No jQuery 
		*  OR 
		* In debug mode inject the script instead of doing an ajax request and eval
		*/
			
		// Load and bind manually:  ( copied from jQuery ajax function )
		var head = document.getElementsByTagName("head")[0];
		var script = document.createElement("script");
		script.setAttribute( 'src', url );		
				
		// Attach handlers for all browsers ( might not work in safari < version 2.0 ) 
		var done = false; 
		script.onload = script.onreadystatechange = function(){
			if ( !done && (!this.readyState ||
					this.readyState == "loaded" || this.readyState == "complete") ) {
				done = true;
				callback();
			}
		};	
		
		// Append the script to the DOM:
		head.appendChild( script );	
	}
	
	/**
	* "Get" a style sheet.
	*
	* Appends a style sheet to the DOM is called "getStyleSheet" to mirror wraping of jqueries getScript
	*
	* @param {Mixed}
	*	{Array} url List of urls to be loaded
	*	{String} url Url of the style sheet to be loaded
	*/
	$.getStyleSheet = function( url ) {	
		if ( typeof url == 'object' ) {
			for ( var i in url ) {
				$.getStyleSheet( url[i] );
			}
			return ;
		}
		
		// Add URL params ( if not already included )
		if ( url.indexOf( '?' ) == -1 ) {
			url += '?' + mw.getUrlParam();
		}
		
		// Check if style sheet is already included:
		var foundSheet = false; 
		$j( 'link' ).each( function(){		
			if( $j( this) .attr( 'href' ) == url )				 
				foundSheet = true;
		} );					
		if( foundSheet ){
			js_log( 'sheet: ' + url + ' already included ' );
			return ;
		}
		
		js_log( ' add css: ' + url );		
		$j( 'head' ).append( 
			$j('<link>').attr( {
				'rel' : 'stylesheet',
				'type' : 'text/css',
				'href' : url
			} )
		);			
	}
	
	/** 
	* Get Api URL from mediaWiki output page defined variables
	*/
	$.getLocalApiUrl = function() {
		if ( typeof wgServer != 'undefined' && typeof wgScriptPath  != 'undefined' ) {
			return wgServer + wgScriptPath + '/api.php';
		}
		return false;
	}	
	
	// Local mwEmbedPath variable ( for cache of $.getMwEmbedPath )
	var mwEmbedPath = null;
	
	/**
	* Gets the path to the mwEmbed folder
	*/
	$.getMwEmbedPath = function() {
		if ( mwEmbedPath )
			return mwEmbedPath;	
			
		// Get mwEmbed src:
		var src = $.getMwEmbedSrc();		
		var mwpath = null;
		// Check for direct include of the mwEmbed.js
		if ( src.indexOf( 'mwEmbed.js' ) !== -1 ) {
			mwpath =  src.substr( 0, src.indexOf( 'mwEmbed.js' ) );			
		}
		
		// Check for scriptLoader include of mwEmbed: 
		if ( src.indexOf( 'mwScriptLoader.php' ) !== -1 ) {
			// Script loader is in the root of MediaWiki, Include the default mwEmbed extension path:
			mwpath =  src.substr( 0, src.indexOf( 'mwScriptLoader.php' ) ) + $.getConfig( 'mediaWikiPath' );						
		}
		
		// Script-loader has jsScriptLoader name when local:
		if( src.indexOf( 'jsScriptLoader.php' ) !== -1 ){
			mwpath = src.substr( 0, src.indexOf( 'jsScriptLoader.php' ) );			
		}	
		
		// Error out if we could not get the path:
		if( ! mwpath ){
			js_log( "Error could not get mwEmbed path " );
			return ;
		}
					
		// Update the cached var with the absolute path: 
		mwEmbedPath = $.absoluteUrl( mwpath )	;			
		return mwEmbedPath;
	}
	
	// Local mwEmbedSrc variable ( for cache of $.getMwEmbedSrc )
	var mwEmbedSrc = null; 
	
	/**
	* Gets the mwEmbed script src attribute
	*/
	$.getMwEmbedSrc = function() {
		if ( mwEmbedSrc )
			return mwEmbedSrc;
			
		// Get all the javascript includes:
		var js_elements = document.getElementsByTagName( "script" );
		for ( var i = 0; i < js_elements.length; i++ ) {
			// Check for mwEmbed.js and/or script loader
			var src = js_elements[i].getAttribute( "src" );
			if ( src ) {
				if ( src.indexOf( 'mwEmbed.js' ) !== -1 || (
					( src.indexOf( 'mwScriptLoader.php' ) !== -1 || src.indexOf( 'jsScriptLoader.php' ) !== -1 )
					&& src.indexOf( 'mwEmbed' ) !== -1 ) ) // (check for class=mwEmbed script_loader call)
				{
					mwEmbedSrc = src;
					return mwEmbedSrc;
				}
			}
		}
		js_log( 'Error: getMwEmbedScriptURL failed to get script path' );
		return false;
	}	
	
	// Local mwUrlParam variable ( for cache of $.getUrlParam )
	var mwUrlParam = null;
	
	/**
	* Get URL Parameters per parameters in the host script include
	*/
	$.getUrlParam = function() {
		if ( mwUrlParam )
			return mwUrlParam;
			
		var mwEmbedSrc = $.getMwEmbedSrc();		
		var req_param = '';
		
		// If we already have a URI, add it to the param request:
		var urid = mw.parseUri( mwEmbedSrc ).queryKey['urid']
		
		// If we're in debug mode, get a fresh unique request key and pass on "debug" param
		if ( mw.parseUri( mwEmbedSrc ).queryKey['debug'] == 'true' ) {		
			$.setConfig( 'debug', true );
			var d = new Date();
			req_param += 'urid=' + d.getTime() + '&debug=true';			
					
		} else if ( urid ) { // Just pass on the existing urid:			
					
			req_param += 'urid=' + urid;			
		} else {
			// Otherwise, Use the mwEmbed version
			req_param += 'urid=' + mw.version;
		}
		
		// Add the language param if present:
		var langKey = mw.parseUri( mwEmbedSrc ).queryKey['uselang'];
		if ( langKey )
			req_param += '&uselang=' + langKey;
		
		// Update the local cache and return the value	
		mwUrlParam = req_param;			
		return mwUrlParam;
	}
	
	/**
	* parseUri 1.2.2
	* (c) Steven Levithan <stevenlevithan.com>
	*  MIT License
	*/		
	$.parseUri = function (str) {
		var	o   = $.parseUri.options,
			m   = o.parser[o.strictMode ? "strict" : "loose"].exec(str),
			uri = {},
			i   = 14;
	
		while (i--) uri[o.key[i]] = m[i] || "";
	
		uri[o.q.name] = {};
		uri[o.key[12]].replace(o.q.parser, function ($0, $1, $2) {
			if ($1) uri[o.q.name][$1] = $2;
		});
	
		return uri;
	};
	
	/**
	* Parse URI function
	*
	* For documentation on its usage see: 
	* http://stevenlevithan.com/demo/parseuri/js/
	*/
	$.parseUri.options = {
		strictMode: false,
		key: ["source", "protocol", "authority", "userInfo", "user","password","host","port","relative","path","directory","file","query","anchor"],
		q:   {
			name:   "queryKey",
			parser: /(?:^|&)([^&=]*)=?([^&]*)/g
		},
		parser: {
			strict: /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,
			loose:  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/
		}
	};	
	
	/**
	* getAbsoluteUrl takes a src and returns the absolute location given the document.URL
	*
	* @param {String} src path or url
	* @return {String} absolute url
	*/
	$.absoluteUrl = function( src, contextUrl ){
		var pSrc =  mw.parseUri( src );
		if( pSrc.protocol != '')
			return src;				
		
		// Get parent Url location the context URL	
		if( contextUrl){	
			var pUrl = mw.parseUri( contextUrl );			
		} else {
			var pUrl = mw.parseUri( document.URL );
		}
		// If a leading slash:  
		if( src.indexOf( '/' ) == 1 ){
			return pUrl.protocol + '://' + pUrl.authority + src;
		}else{
			return pUrl.protocol + '://' + pUrl.authority + pUrl.directory + src;
		}
	};
	
	/**
	* Takes in a string returns an xml dom object 
	*
	* @param {String} str String of XML content to be parsed
	* @return 
	* 	{Object} XML
	*	false If XML could not be parsed 
	*
	*/
	$.parseXML = function ( str ){
		if ( $j.browser.msie ) {
			// Attempt to parse as XML for IE
			var xmldata = new ActiveXObject( "Microsoft.XMLDOM" );
			xmldata.async = "false";
			try{
				xmldata.loadXML( str );
				return xmldata;
			} catch (e){
				js_log( 'XML parse ERROR: ' + e.message );
				return false;
			}
		}
		
		// For others (Firefox, Safari etc, older browsers 
		// Some don't have native DOMParser either fallback defined bellow.
		try {
			var xmldata = ( new DOMParser() ).parseFromString( str, "text/xml" );
		} catch ( e ) {
			js_log( 'XML parse ERROR: ' + e.message );
			return false;
		}		
		return xmldata;
	}	
} )( window.mw );

// load in js2 stopgap into proper location: 
if ( typeof gMsg != 'undefined' ) {
	mw.addMessages( gMsg )
}

var gM = mw.lang.gM;
// setup legacy global shortcuts:
var loadRS = mw.lang.loadRS;

/**
 * --  Load Class Paths --
 *
 * MUST BE VALID JSON (NOT JS)
 * This is used by the script loader to auto-load classes (so we only define
 * this once for PHP & JavaScript)
 *
 * Right now the PHP AutoLoader only reads this mwEmbed.js file.
 * In the future we could have multiple "loader" files
 * 
 */
 

mw.addClassFilePaths( {
	"mwEmbed"			: "mwEmbed.js",
	"window.jQuery"		: "jquery/jquery-1.3.2.js",
	"$j.fn.pngFix"		: "jquery/plugins/jquery.pngFix.js",
	"$j.fn.autocomplete": "jquery/plugins/jquery.autocomplete.js",
	"$j.fn.hoverIntent"	: "jquery/plugins/jquery.hoverIntent.js",
	"$j.fn.datePicker"	: "jquery/plugins/jquery.datePicker.js",
	"$j.ui"				: "jquery/jquery.ui/ui/ui.core.js",
	"$j.fn.ColorPicker"	: "libClipEdit/colorpicker/js/colorpicker.js",
	"$j.Jcrop"			: "libClipEdit/Jcrop/js/jquery.Jcrop.js",
	"$j.fn.simpleUploadForm" : "libAddMedia/simpleUploadForm.js",
	
	"mw.proxy"		: "libMwApi/mw.proxy.js",
	
	"mw.testLang"	:  "tests/testLang.js",
	
	"ctrlBuilder"	: "skins/ctrlBuilder.js",
	"kskinConfig"	: "skins/kskin/kskin.js",
	"mvpcfConfig"	: "skins/mvpcf/mvpcf.js",

	"JSON"				: "libMwApi/json2.js",
	"$j.cookie"			: "jquery/plugins/jquery.cookie.js",
	"$j.contextMenu"	: "jquery/plugins/jquery.contextMenu.js",
	"$j.fn.suggestions"	: "jquery/plugins/jquery.suggestions.js",
	"$j.fn.textSelection"	: "jquery/plugins/jquery.textSelection.js",

	"$j.effects.blind"		: "jquery/jquery.ui/ui/effects.blind.js",
	"$j.effects.drop"		: "jquery/jquery.ui/ui/effects.drop.js",
	"$j.effects.pulsate"	: "jquery/jquery.ui/ui/effects.pulsate.js",
	"$j.effects.transfer"	: "jquery/jquery.ui/ui/effects.transfer.js",
	"$j.ui.droppable"		: "jquery/jquery.ui/ui/ui.droppable.js",
	"$j.ui.slider"			: "jquery/jquery.ui/ui/ui.slider.js",
	"$j.effects.bounce"		: "jquery/jquery.ui/ui/effects.bounce.js",
	"$j.effects.explode"	: "jquery/jquery.ui/ui/effects.explode.js",
	"$j.effects.scale"		: "jquery/jquery.ui/ui/effects.scale.js",
	"$j.ui.datepicker"		: "jquery/jquery.ui/ui/ui.datepicker.js",
	"$j.ui.progressbar"		: "jquery/jquery.ui/ui/ui.progressbar.js",
	"$j.ui.sortable"		: "jquery/jquery.ui/ui/ui.sortable.js",
	"$j.effects.clip"		: "jquery/jquery.ui/ui/effects.clip.js",
	"$j.effects.fold"		: "jquery/jquery.ui/ui/effects.fold.js",
	"$j.effects.shake"		: "jquery/jquery.ui/ui/effects.shake.js",
	"$j.ui.dialog"			: "jquery/jquery.ui/ui/ui.dialog.js",
	"$j.ui.resizable"		: "jquery/jquery.ui/ui/ui.resizable.js",
	"$j.ui.tabs"			: "jquery/jquery.ui/ui/ui.tabs.js",
	"$j.effects.core"		: "jquery/jquery.ui/ui/effects.core.js",
	"$j.effects.highlight"	: "jquery/jquery.ui/ui/effects.highlight.js",
	"$j.effects.slide"		: "jquery/jquery.ui/ui/effects.slide.js",
	"$j.ui.accordion"		: "jquery/jquery.ui/ui/ui.accordion.js",
	"$j.ui.draggable"		: "jquery/jquery.ui/ui/ui.draggable.js",
	"$j.ui.selectable"		: "jquery/jquery.ui/ui/ui.selectable.js",

	"$j.fn.dragDropFile"		: "libAddMedia/dragDropFile.js",
	"mvFirefogg"			: "libAddMedia/mvFirefogg.js",
	"mvAdvFirefogg"			: "libAddMedia/mvAdvFirefogg.js",
	"mvBaseUploadInterface"	: "libAddMedia/mvBaseUploadInterface.js",
	"remoteSearchDriver"	: "libAddMedia/remoteSearchDriver.js",
	"seqRemoteSearchDriver" : "libSequencer/seqRemoteSearchDriver.js",

	"baseRemoteSearch"		: "libAddMedia/searchLibs/baseRemoteSearch.js",
	"mediaWikiSearch"		: "libAddMedia/searchLibs/mediaWikiSearch.js",
	"metavidSearch"			: "libAddMedia/searchLibs/metavidSearch.js",
	"archiveOrgSearch"		: "libAddMedia/searchLibs/archiveOrgSearch.js",
	"flickrSearch"			: "libAddMedia/searchLibs/flickrSearch.js",
	"baseRemoteSearch"		: "libAddMedia/searchLibs/baseRemoteSearch.js",

	"mvClipEdit"			: "libClipEdit/mvClipEdit.js",
	
	"mvPlayList"		: "libSequencer/mvPlayList.js",
	"mvSequencer"		: "libSequencer/mvSequencer.js",
	"mvFirefoggRender"	: "libSequencer/mvFirefoggRender.js",
	"mvTimedEffectsEdit": "libSequencer/mvTimedEffectsEdit.js",

	"mvTextInterface"	: "libTimedText/mvTextInterface.js",
	"mvTimeTextEdit"	: "libTimedText/mvTimeTextEdit.js"

} );

/*
* Adds style sheets to be loaded with particular classes   
*/
mw.addClassStyleSheets( {
	'$j.Jcrop'			: 'libClipEdit/Jcrop/css/jquery.Jcrop.css',
	'$j.fn.ColorPicker'	: 'libClipEdit/colorpicker/css/colorpicker.css'
})

/**
* libEmbedPlayer Dependency Module Loader:
*
* NOTE: this code block could eventually be put in: 
* "libEmbedPlayer/loader.js" 
* 
* Then it could be dynamically inserted into mwEmbed requests 
* at point of release or at runtime via the script-loader.
* 
* Per module loader enables a dynamic set of modules with only minimal
* loader code per module in the core mwEmbed included js 
*/
// Add class file paths: 
mw.addClassFilePaths( {
	"embedPlayer"		: "libEmbedPlayer/embedPlayer.js",
	"flowplayerEmbed"	: "libEmbedPlayer/flowplayerEmbed.js",
	"kplayerEmbed"		: "libEmbedPlayer/kplayerEmbed.js",
	"genericEmbed"		: "libEmbedPlayer/genericEmbed.js",
	"htmlEmbed"			: "libEmbedPlayer/htmlEmbed.js",
	"javaEmbed"			: "libEmbedPlayer/javaEmbed.js",
	"nativeEmbed"		: "libEmbedPlayer/nativeEmbed.js",
	"quicktimeEmbed"	: "libEmbedPlayer/quicktimeEmbed.js",
	"vlcEmbed"			: "libEmbedPlayer/vlcEmbed.js"

} );
// Add the module loader function:
mw.addModuleLoader( 'player', function( callback ){
	var _this = this;
	js_log( 'loadModule: player :' );
	
	// Set module specific class videonojs to loading:
	$j( '.videonojs' ).html( gM( 'mwe-loading_txt' ) );
	
	// Set up the embed video player class request: (include the skin js as well)
	var dependencyRequest = [
		[
			'$j.ui',
			'embedPlayer',
			'ctrlBuilder',
			'$j.cookie'
		],
		[
			'$j.ui.slider'
		]
	];
	
	// Get any other skins that we need to load 
	// That way skin js can be part of the single script-loader request:
	var playerElements = mw.getPlayerTagElements();
	$j.each( playerElements, function(na, playerElem ){
		var cName = $j( playerElem ).attr( 'class' );
		for( var n=0; n < mw.valid_skins.length ; n++ ){ 
			if( cName.indexOf( mw.valid_skins[ n ] ) !== -1){
				mw.skin_list.push( mw.valid_skins[n] );
			}
		}		
	} );	
	
	// Add any page specific requested skins js ( supports multiple skins per single page )
	if ( mw.skin_list ) {
		for ( var i in mw.skin_list  ) {
			dependencyRequest[0].push( mw.skin_list[i] + 'Config' );
		}
	}

	// Add PNG fix if needed:
	if ( $j.browser.msie || $j.browser.version < 7 )
		dependencyRequest[0].push( '$j.fn.pngFix' );

	// Load the video libs:
	mw.load( dependencyRequest, function() {
		
		// Detect what players are supported: 
		embedTypes.init();
		
		// Remove no video html elements:
		$j( '.videonojs' ).remove();
		
		//Run the callback
		callback();		
	} );
	
} ); // done with embedPlayer loader.js






// Add the core mvEmbed Messages ( will be localized by script server ) 
mw.addMessages( {
	"mwe-loading_txt" : "Loading ...",
	"mwe-size-gigabytes" : "$1 GB",
	"mwe-size-megabytes" : "$1 MB",
	"mwe-size-kilobytes" : "$1 K",
	"mwe-size-bytes" : "$1 B",
	"mwe-error_load_lib" : "Error: JavaScript $1 was not retrievable or does not define $2",
	"mwe-loading-add-media-wiz" : "Loading add media wizard",
	"mwe-apiproxy-setup" : "Setting up API proxy",
	"mwe-load-drag-item" : "Loading dragged item",
	"mwe-ok" : "OK",
	"mwe-cancel" : "Cancel"
} );




/**
* Set DOM-ready call 
* Does not use jQuery( document ).ready( ) because 
*  mwEmbed could have been included without jQuery. 
*/
// For Mozilla / modern browsers
if ( document.addEventListener ) {
	document.addEventListener( "DOMContentLoaded", mw.domReady, false );
}
// Preserve any existing window.onload binding: 
var mwOriginalOnLoad;
if ( window.onload && typeof  window.onload == 'function' ) {
    mwOriginalOnLoad = window.onload;
}
// Use the onload method as a backup:
window.onload = function () {
    if ( mwOriginalOnLoad )
        mwOriginalOnLoad();
	mw.domReady();
}






// Get the loading image
function mv_get_loading_img( style, class_attr ) {
	var style_txt = ( style ) ? style:'';
	var class_attr = ( class_attr ) ? 'class="' + class_attr + '"' : 'class="mv_loading_img"';
	return '<div ' + class_attr + ' style="' + style + '"></div>';
}

function mv_set_loading( target, load_id ) {
	var id_attr = ( load_id ) ? ' id="' + load_id + '" ':'';
	$j( target ).append( '<div ' + id_attr + ' style="position:absolute;top:0px;left:0px;height:100%;width:100%;' +
		'background-color:#FFF;">' +
			mv_get_loading_img( 'top:30px;left:30px' ) +
		'</div>' );
}

/**
* mvJsLoader class handles initialization and js file loads

var mvJsLoader = {
	libreq : { },
	libs : { },

	// Base lib flags
	onReadyEvents: new Array(),
	doneReadyEvents: false,
	jQuerySetupFlag: false,

	// To keep consistency across threads
	ptime: 0,
	ctime: 0,

	load_error: false, // Load error flag (false by default)
	load_time: 0,
	callbacks: new Array(),
	cur_path: null,
	missing_path : null,
	doLoad: function( loadLibs, callback ) {
		this.ctime++;
		if ( loadLibs && loadLibs.length != 0 ) {
			// js_log("doLoad setup::" + JSON.stringify( loadLibs ) );
			// Set up this.libs
			// First check if we already have this library loaded
			var all_libs_loaded = true;
			for ( var i = 0; i < loadLibs.length; i++ ) {
				// Check if the library is already loaded
				if ( ! this.checkObjPath( loadLibs[i] ) ) {
					all_libs_loaded = false;
				}
			}
				
			if ( all_libs_loaded ) {
				js_log( 'Libraries ( ' + loadLibs  +  ') already loaded... skipping load request' );
				callback();
				return;
			}
			
			// Do a check for any CSS we may need and get it
			for ( var i = 0; i < loadLibs.length; i++ ) {
				if ( typeof mvCssPaths[ loadLibs[i] ] != 'undefined' ) {
					mw.getStyleSheet( mw.getMwEmbedPath() + mvCssPaths[ loadLibs[i] ] );
				}
			}

			// Check if we should use the script loader to combine all the requests into one
			// ( the scriptloader defines the mwSlScript global )
			if ( typeof mwSlScript != 'undefined' ) {
				var class_set = '';
				var last_class = '';
				var coma = '';
				for ( var i = 0; i < loadLibs.length; i++ ) {
					var curLib = loadLibs[i];
					// Only add if not included yet:
					if ( ! this.checkObjPath( curLib ) ) {
						class_set += coma + curLib;
						last_class = curLib;
						coma = ',';
					}
				}
				// Build the url to the scriptServer striping its request parameters:
				var puri = mw.parseUri( getMwEmbedURL() );
				if ( ( getMwEmbedURL().indexOf( '://' ) != -1 )
					&& puri.host != mw.parseUri( document.URL ).host )
				{
					var scriptPath = puri.protocol + '://' + puri.authority + puri.path;
				} else {
					var scriptPath = puri.path;
				}
				// js_log('scriptServer Path is: ' + scriptPath + "\n host script path:" + getMwEmbedURL() );				
				this.libs[ last_class ] = scriptPath + '?class=' + class_set +
					'&' + mw.getUrlParam();

			} else {
				// Do many requests
				for ( var i = 0; i < loadLibs.length; i++ ) {
					var curLib = loadLibs[i];
					if ( curLib ) {
						var libLoc = mvGetClassPath( curLib );
						// Do a direct load of the file (pass along unique request id from
						// request or mwEmbed Version )
						var qmark = ( libLoc.indexOf( '?' ) !== true ) ? '?' : '&';
						this.libs[curLib] = mw.getMwEmbedPath() + libLoc + qmark + mw.getUrlParam();
					}
				}
			}
		}
		
		if ( callback ) {
			this.callbacks.push( callback );
		}
		if ( this.checkLoading() ) {
			// @@todo we should check the <script> Element .onLoad property to
			// make sure its just not a very slow connection
			// (even though the class is not loaded)
			if ( this.load_time++ > 4000 ) { // Time out after ~80 seconds
				js_log( gM( 'mwe-error_load_lib', [mvGetClassPath( this.missing_path ),  this.missing_path] ) );
				this.load_error = true;
			} else {
				setTimeout( 'mw.load()', 20 );
			}
		} else {
			// js_log('checkLoading passed. Running callbacks...');
			// Only do callbacks if we are in the same instance (weird concurrency issue)
			var cb_count = 0;
			for ( var i = 0; i < this.callbacks.length; i++ )
				cb_count++;
			// js_log('RESET LIBS: loading is: '+ loading + ' callback count: '+cb_count +
			//	' p:'+ this.ptime +' c:'+ this.ctime);

			// Reset the libs
			this.libs = { };
			// js_log('done loading, do call: ' + this.callbacks[0] );
			while ( this.callbacks.length != 0 ) {
				if ( this.ptime == this.ctime - 1 ) { // Enforce thread consistency
					this.callbacks.pop()();
					// func = this.callbacks.pop();
					// js_log(' run: '+this.ctime+ ' p: ' + this.ptime + ' ' +loading+ ' :'+ func);
					// func();
				} else {
					// Re-issue doLoad ( ptime will be set to ctime so we should catch up)
					setTimeout( 'mw.load()', 25 );
					break;
				}
			}
		}
		this.ptime = this.ctime;
	},
	doLoadDepMode: function( loadChain, callback ) {
		// Firefox executes JS in the order in which it is included, so just directly issue the request
		if ( $j.browser.firefox ) {
			var loadSet = [];
			for ( var i = 0; i < loadChain.length; i++ ) {
				for ( var j = 0; j < loadChain[i].length; j++ ) {
					loadSet.push( loadChain[i][j] );
				}
			}
			mw.load( loadSet, callback );
		} else {
			// Safari and IE tend to execute out of order so load with dependency checks
			mw.load( loadChain.shift(), function() {
				if ( loadChain.length != 0 ) {
					mw.load( loadChain, callback );
				} else {
					callback();
				}
			} );
		}
	},
	checkLoading: function() {
		var loading = 0;
		var i = null;
		for ( var i in this.libs ) { // for/in loop is OK on an object
			if ( !this.checkObjPath( i ) ) {
				if ( !this.libreq[i] ) {
					loadExternalJs( this.libs[i] );
				}
				this.libreq[i] = 1;
				// js_log("has not yet loaded: " + i);
				loading = 1;
			}
		}
		return loading;
	},
	checkObjPath: function( libVar ) {
		if ( !libVar )
			return false;
		var objPath = libVar.split( '.' )
		var cur_path = '';
		for ( var p = 0; p < objPath.length; p++ ) {
			cur_path = ( cur_path == '' ) ? cur_path + objPath[p] : cur_path + '.' + objPath[p];
			eval( 'var ptest = typeof ( ' + cur_path + ' ); ' );
			if ( ptest == 'undefined' ) {
				this.missing_path = cur_path;
				return false;
			}
		}
		this.cur_path = cur_path;
		return true;
	},
	//
	// checks for jQuery and adds the $j noConflict var
	//
	jQueryCheck: function( callback ) {
		// js_log( 'jQueryCheck::' + this.jQuerySetupFlag);
		var _this = this;
		if ( _global['$j'] && _this.jQuerySetupFlag ) {			
			callback(); // call the callback now			
		}
		// Load jQuery
		_this.doLoad( [
			'window.jQuery'
		], function() {
			
		} );
	},
	embedPlayerCheck:function( callback ) {
		var _this = this;
		js_log( 'embedPlayerCheck:' );
		// Make sure we have jQuery
		_this.jQueryCheck( function() {
			// set class videonojs to loading
			$j( '.videonojs' ).html( gM( 'mwe-loading_txt' ) );
			// Set up the embed video player class request: (include the skin js as well)
			var depReq = [
				[
					'$j.ui',
					'embedPlayer',
					'ctrlBuilder',
					'$j.cookie'
				],
				[
					'$j.ui.slider'
				]
			];
			
			// add any requested skins (supports multiple skins per single page)
			if ( mw.skin_list ) {
				for ( var i in mw.skin_list  ) {
					depReq[0].push( mw.skin_list[i] + 'Config' );
				}
			}

			// Add PNG fix if needed:
			if ( $j.browser.msie || $j.browser.version < 7 )
				depReq[0].push( '$j.fn.pngFix' );

			// load the video libs:
			_this.doLoadDepMode( depReq, function() {
				embedTypes.init();
				callback();
				$j( '.videonojs' ).remove();
			} );
		} );
	},
	addLoadEvent: function( fn ) {
		// js_log('add ready event: ' + fn );
		this.onReadyEvents.push( fn );
	},
	// Check the jQuery flag. This way, when remote embedding, we don't load jQuery
	// unless mw.addOnloadHook was used or there is video on the page.
	runQueuedFunctions: function() {
		js_log( "runQueuedFunctions" );
		var _this = this;
		this.jQueryCheck( function() {
			_this.runReadyEvents();
			_this.doneReadyEvents = true;
		} );
	},
	runReadyEvents: function() {
		js_log( "runReadyEvents" +  this.onReadyEvents.length );
		while ( this.onReadyEvents.length ) {
			var func = this.onReadyEvents.shift();
			// js_log('run onReady:: ' + func );
			func();
		}
	}
}
*/


/*
 * Store all the mwEmbed jQuery-specific bindings
 * (set up after jQuery is available).
 *
 * These functions are generally are loaders that do the dynamic mapping of
 * dependencies for a given component
 *
 * Should be repaced by "loader" calls
 * 
 *
 */
function mwDojQueryBindings() {
	js_log( 'mv_jqueryBindings' );
	( function( $ ) {
		/*
		* dragDrop file loader 
		*/
		$.fn.dragFileUpload = function ( conf ) {
			if ( this.selector ) {
				var _this = this;
				// load the dragger and "setup"
				mw.load( ['$j.fn.dragDropFile'], function() {
					$j( _this.selector ).dragDropFile();
				} );
			}
		}
		/*
		 * apiProxy Loader loader:
		 * 
		 * @param mode is either 'server' or 'client'
		 */
		$.apiProxy = function( mode, proxyConfig, callback ) {
			js_log( 'do apiProxy setup' );
			mw.load( [
				'mw.proxy',
				'JSON'
			], function() {				
				// do the proxy setup or 
				if ( mode == 'client' ) {
					// just do the setup (no callbcak for client setup) 
					mw.proxy.client( proxyConfig );
					if ( callback )
						callback();
				} else if ( mode == 'server' ) {
					// Do the request with the callback
					mw.proxy.server( proxyConfig , callback );
				}
			} );
		}
		
		// non selector based add-media-wizard direct invocation with loader
		$.addMediaWiz = function( options, callback ) {
			js_log( ".addMediaWiz call" );
			// check if already loaded:
			if ( _global['rsdMVRS'] ) {
				_global['rsdMVRS'].showDialog();
				if ( callback )
					callback( _global['rsdMVRS'] );
				return ;
			}
			// display a loader: 
			$.addLoaderDialog( gM( 'mwe-loading-add-media-wiz' ) );
			// load the addMedia wizard without a target: 
			$.fn.addMediaWiz ( options, function( amwObj ) {
				// close the dialog
				$.closeLoaderDialog();
				// do the add-media-wizard display
				amwObj.createUI();
				// call the parent callback:
				if ( callback )
					callback( _global['rsdMVRS'] );
			} );
		}
		
		$.fn.addMediaWiz = function( options, callback ) {
			if ( this.selector ) {
				// First set the cursor for the button to "loading"
				$j( this.selector ).css( 'cursor', 'wait' ).attr( 'title', gM( 'mwe-loading_txt' ) );
				// set the target: 
				options['target_invoke_button'] = this.selector;
			}

			// Load the mwEmbed_base skin:
			mw.getStyleSheet( mw.getConfig( 'jquery_skin_path' ) + 'jquery-ui-1.7.1.custom.css' );
			mw.getStyleSheet( mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skin_name' ) + '/styles.css' );
			// Load all the required libs:
			mw.load( [
				[	'remoteSearchDriver',
					'$j.cookie',
					'$j.fn.textSelection',
					'$j.ui'
				], [
					'$j.ui.resizable',
					'$j.ui.draggable',
					'$j.ui.dialog',
					'$j.ui.tabs',
					'$j.ui.sortable'
				]
			], function() {
				options['instance_name'] = 'rsdMVRS';
				if ( ! _global['rsdMVRS'] )
					_global['rsdMVRS'] = new remoteSearchDriver( options );
				if ( callback ) {
					callback( _global['rsdMVRS'] );
				}
			} );
		}
		/*
		* Sequencer loader
		*/
		$.fn.sequencer = function( options, callback ) {
			// Debugger
			options['target_sequence_container'] = this.selector;
			// Issue a request to get the CSS file (if not already included):
			mw.getStyleSheet( mw.getConfig( 'jquery_skin_path' ) + 'jquery-ui-1.7.1.custom.css' );
			mw.getStyleSheet( mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skin_name' ) + '/mv_sequence.css' );
			// Make sure we have the required mwEmbed libs (they are not loaded when no video
			// element is on the page)
			mvJsLoader.eembedPlayerheck( function() {
				// Load the playlist object and then the jQuery UI stuff:
				mw.load( [
					[
						'mvPlayList',
						'$j.ui',
						'$j.contextMenu',
						'JSON',
						'mvSequencer'
					],
					[
						'$j.ui.accordion',
						'$j.ui.dialog',
						'$j.ui.droppable',
						'$j.ui.draggable',
						'$j.ui.progressbar',
						'$j.ui.sortable',
						'$j.ui.resizable',
						'$j.ui.slider',
						'$j.ui.tabs'
					]
				], function() {
					js_log( 'calling new mvSequencer' );
					// Initialise the sequence object (it will take over from there)
					// No more than one mvSeq obj for now:
					if ( !_global['mvSeq'] ) {
						_global['mvSeq'] = new mvSequencer( options );
					} else {
						js_log( 'mvSeq already init' );
					}
				} );
			} );
		}
		/*
		 * The Firefogg jQuery function:
		 * @@note This Firefogg invocation could be made to work more like real jQuery plugins
		 */
		var queuedFirefoggConf = { };
		$.fn.firefogg = function( options, callback ) {
			if ( !options )
				options = { };
				
			// Add the base theme CSS:
			mw.getStyleSheet( mw.getConfig( 'jquery_skin_path' ) + 'jquery-ui-1.7.1.custom.css' );
			mw.getStyleSheet( mw.getMwEmbedPath() + 'skins/' + mw.getConfig( 'skin_name' ) + '/styles.css' );

			// Check if we already have Firefogg loaded (the call just updates the element's
			// properties)			
			var sElm = $j( this.selector ).get( 0 );
			if ( sElm['firefogg'] ) {
				if ( sElm['firefogg'] == 'loading' ) {
					js_log( "Queued firefogg operations ( firefogg " +
						"not done loading ) " );
					$j.extend( queuedFirefoggConf, options );
					return false;
				}
				// Update properties
				for ( var i in options ) {
					js_log( "firefogg::updated: " + i + ' to ' + options[i] );
					sElm['firefogg'][i] = options[i];
				}
				return sElm['firefogg'];
			} else {
				// Avoid concurrency
				sElm['firefogg'] = 'loading';
			}
			// Add the selector
			options['selector'] = this.selector;

			var loadSet = [
				[
					'mvBaseUploadInterface',
					'mvFirefogg',
					'$j.ui'
				],
				[
					'$j.ui.progressbar',
					'$j.ui.dialog',
					'$j.ui.draggable'
				]
			];
			if ( options.encoder_interface ) {
				loadSet.push( [
					'mvAdvFirefogg',
					'$j.cookie',
					'$j.ui.accordion',
					'$j.ui.slider',
					'$j.ui.datepicker'
				] );
			}
			// Make sure we have everything loaded that we need:
			mw.load( loadSet, function() {
					js_log( 'firefogg libs loaded. target select:' + options.selector );
					// Select interface provider based on whether we want to include the
					// encoder interface or not
					if ( options.encoder_interface ) {
						var myFogg = new mvAdvFirefogg( options );
					} else {
						var myFogg = new mvFirefogg( options );
					}
					if ( myFogg ) {
						myFogg.doRewrite( callback );
						var selectorElement = $j( options.selector ).get( 0 );
						selectorElement['firefogg'] = myFogg;
						
						js_log( 'pre:' + selectorElement['firefogg']['firefogg_form_action'] )
						if ( queuedFirefoggConf )
							$j.extend( selectorElement['firefogg'], queuedFirefoggConf );
						js_log( 'post:' + selectorElement['firefogg']['firefogg_form_action'] )
					}
			} );
		}
		// Take an input player as the selector and expose basic rendering controls
		$.fn.firefoggRender = function( options, callback ) {
			// Check if we already have render loaded then just pass on updates/actions
			var sElm = $j( this.selector ).get( 0 );
			//add a special attribute to the selector: 
			if ( sElm['fogg_render'] ) {
				if ( sElm['fogg_render'] == 'loading' ) {
					js_log( "Error: called firefoggRender while loading" );
					return false;
				}
				// Call or update the property:
			}
			sElm['fogg_render'] = 'loading';
			// Add the selector
			options['player_target'] = this.selector;
			mw.load( [
				'mvBaseUploadInterface',
				'mvFirefogg',
				'mvFirefoggRender'
			], function() {
				// Attach the firefoggRender obj to the selected elm: 
				sElm['fogg_render'] = new mvFirefoggRender( options );
				if ( callback && typeof callback == 'function' )
					callback( sElm['fogg_render'] );
			} );
		}

		$.fn.baseUploadInterface = function( options ) {
			mw.load( [
				[
					'mvBaseUploadInterface',
					'$j.ui',
				],
				[
					'$j.ui.progressbar',
					'$j.ui.dialog'
				]
			], function() {
				myUp = new mvBaseUploadInterface( options );
				myUp.setupForm();
			} );
		}

		// Shortcut to a themed button
		$.btnHtml = function( msg, className, iconId, opt ) {
			if ( !opt )
				opt = { };
			var href = ( opt.href ) ? opt.href : '#';
			var target_attr = ( opt.target ) ? ' target="' + opt.target + '" ' : '';
			var style_attr = ( opt.style ) ? ' style="' + opt.style + '" ' : '';
			return '<a href="' + href + '" ' + target_attr + style_attr +
				' class="ui-state-default ui-corner-all ui-icon_link ' +
				className + '"><span class="ui-icon ui-icon-' + iconId + '" ></span>' +
				'<span class="btnText">' + msg + '</span></a>';
		}
		// Shortcut to bind hover state
		$.fn.btnBind = function() {
			$j( this ).hover(
				function() {
					$j( this ).addClass( 'ui-state-hover' );
				},
				function() {
					$j( this ).removeClass( 'ui-state-hover' );
				}
			)
			return this;
		}
		/**
		* resize the dialog to fit the window
		*/
		$.fn.dialogFitWindow = function( opt ) {
			var opt_default = { 'hspace':50, 'vspace':50 };
			if ( !opt )
				var opt = { };
			$j.extend( opt, opt_default );
			$j( this.selector ).dialog( 'option', 'width', $j( window ).width() - opt.hspace );
			$j( this.selector ).dialog( 'option', 'height', $j( window ).height() - opt.vspace );
			$j( this.selector ).dialog( 'option', 'position', 'center' );
				// update the child position: (some of this should be pushed up-stream via dialog config options
			$j( this.selector + '~ .ui-dialog-buttonpane' ).css( {
				'position':'absolute',
				'left':'0px',
				'right':'0px',
				'bottom':'0px'
			} );
		}
		
		/**
		* addLoaderDialog
		*  small helper for putting a loading dialog box on top of everything
		* (helps block for request that
		*
		* @param msg text text of the loader msg
		*/
		$.addLoaderDialog = function( msg_txt ) {
			$.addDialog( msg_txt, msg_txt + '<br>' + mv_get_loading_img() );
		}
		
		/**
		* shortcut jquery binding to add a dialog window:
		*/
		$.addDialog = function ( title, msg_txt, btn ) {
			$( '#mwe_tmp_loader' ).remove();
			// append the style free loader ontop: 
			$( 'body' ).append( '<div id="mwe_tmp_loader" style="display:none" title="' + title + '" >' +
					msg_txt +
			'</div>' );
			// special btn == ok gives empty give a single "oky" -> "close"
			if ( btn == 'ok' ) {
				btn[ gM( 'mwe-ok' ) ] = function() {
					$j( '#mwe_tmp_loader' ).close();
				}
			}
			// turn the loader into a real dialog loader: 
			mw.load( [
				[
					'$j.ui'
				],
				[
					'$j.ui.dialog'
				]
			], function() {
				$( '#mwe_tmp_loader' ).dialog( {
					bgiframe: true,
					draggable: false,
					resizable: false,
					modal: true,
					width:400,
					buttons: btn
				} );
			} );
		}
		$.closeLoaderDialog = function() {
			mw.load( [
				[
					'$j.ui'
				],
				[
					'$j.ui.dialog'
				]
			], function() {
				$j( '#mwe_tmp_loader' ).dialog( 'destroy' ).remove();
			} );
		}
	
		$.mwProxy = function( apiConf ) {
			mw.load( ['mw.apiProxy'],
			function() {
				mw.apiProxy( apiConf );
			} );
		}
	} )( jQuery );
}
/*
* Utility functions:
*/
// Simple URL rewriter (could probably be refactored into an inline regular exp)
function getURLParamReplace( url, opt ) {
	var pSrc = mw.parseUri( url );
	if ( pSrc.protocol != '' ) {
		var new_url = pSrc.protocol + '://' + pSrc.authority + pSrc.path + '?';
	} else {
		var new_url = pSrc.path + '?';
	}
	var amp = '';
	for ( var key in pSrc.queryKey ) {
		var val = pSrc.queryKey[ key ];
		// Do override if requested
		if ( opt[ key ] )
			val = opt[ key ];
		new_url += amp + key + '=' + val;
		amp = '&';
	};
	// Add any vars that were not already there:
	for ( var i in opt ) {
		if ( !pSrc.queryKey[i] ) {
			new_url += amp + i + '=' + opt[i];
			amp = '&';
		}
	}
	return new_url;
}

/**
 * Given a float number of seconds, returns npt format response.
 *
 * @param float Seconds
 * @param boolean If we should show milliseconds or not.
 */
function seconds2npt( sec, show_ms ) {
	if ( isNaN( sec ) ) {
		// js_log("warning: trying to get npt time on NaN:" + sec);
		return '0:0:0';
	}
	var hours = Math.floor( sec / 3600 );
	var minutes = Math.floor( ( sec / 60 ) % 60 );
	var seconds = sec % 60;
	// Round the number of seconds to the required number of significant digits
	if ( show_ms ) {
		seconds = Math.round( seconds * 1000 ) / 1000;
	} else {
		seconds = Math.round( seconds );
	}
	if ( seconds < 10 )
		seconds = '0' +	seconds;
	if ( minutes < 10 )
		minutes = '0' + minutes;

	return hours + ":" + minutes + ":" + seconds;
}
/*
 * Take hh:mm:ss,ms or hh:mm:ss.ms input, return the number of seconds
 */
function npt2seconds( npt_str ) {
	if ( !npt_str ) {
		// js_log('npt2seconds:not valid ntp:'+ntp);
		return false;
	}
	// Strip {npt:}01:02:20 or 32{s} from time  if present
	npt_str = npt_str.replace( /npt:|s/g, '' );

	var hour = 0;
	var min = 0;
	var sec = 0;

	times = npt_str.split( ':' );
	if ( times.length == 3 ) {
		sec = times[2];
		min = times[1];
		hour = times[0];
	} else if ( times.length == 2 ) {
		sec = times[1];
		min = times[0];
	} else {
		sec = times[0];
	}
	// Sometimes a comma is used instead of period for ms
	sec = sec.replace( /,\s?/, '.' );
	// Return seconds float
	return parseInt( hour * 3600 ) + parseInt( min * 60 ) + parseFloat( sec );
}
/*
 * Simple helper to grab an edit token
 *
 * @param title The wiki page title you want to edit
 * @param api_url 'optional' The target API URL
 * @param callback The callback function to pass the token to
 */
function get_mw_token( title, api_url, callback ) {
	js_log( ':get_mw_token:' );
	if ( !title && wgUserName ) {
		title = 'User:' + wgUserName;
	}
	var reqObj = {
			'action': 'query',
			'prop': 'info',
			'intoken': 'edit',
			'titles': title
		};
	do_api_req( {
		'data': reqObj,
		'url' : api_url
		}, function( data ) {
			for ( var i in data.query.pages ) {
				if ( data.query.pages[i]['edittoken'] ) {
					if ( typeof callback == 'function' )
						callback ( data.query.pages[i]['edittoken'] );
				}
			}
			// No token found:
			return false;
		}
	);
}
// Do a remote or local API request based on request URL
// @param options: url, data, cbParam, callback
function do_api_req( options, callback ) {
	if ( typeof options.data != 'object' ) {
		return js_error( 'Error: request paramaters must be an object' );
	}
	// Generate the URL if it's missing
	if ( typeof options.url == 'undefined' || !options.url ) {
		if ( typeof wgServer == 'undefined' ) {
			return js_error( 'Error: no api url for api request' );
		}
		options.url = mw.getLocalApiUrl();
	}
	if ( typeof options.data == 'undefined' )
		options.data = { };

	// Force format to JSON
	options.data['format'] = 'json';

	// If action is not set, assume query
	if ( ! options.data['action'] )
		options.data['action'] = 'query';

	// js_log('do api req: ' + options.url +'?' + jQuery.param(options.data) );	
	if ( options.url == 'proxy' && mw.proxy ) {
		// assume the proxy is already "setup" since mw.proxy is defined.
		// @@todo should probably integrate that setup into the api call
		mw.proxy.doRequest( options.data,  callback );
	} else if ( mw.parseUri( document.URL ).host == mw.parseUri( options.url ).host ) {
		// Local request: do API request directly
		$j.ajax( {
			type: "POST",
			url: options.url,
			data: options.data,
			dataType: 'json', // API requests _should_ always return JSON data:
			async: false,
			success: function( data ) {
				callback( data );
			},
			error: function( e ) {
				js_error( ' error' + e + ' in getting: ' + options.url );
			}
		} );
	} else {
		// Remote request
		// Set the callback param if it's not already set
		if ( typeof options.jsonCB == 'undefined' )
			options.jsonCB = 'callback';

		var req_url = options.url;
		var paramAnd = ( req_url.indexOf( '?' ) == -1 ) ? '?' : '&';
		// Put all the parameters into the URL
		for ( var i in options.data ) {
			req_url += paramAnd + encodeURIComponent( i ) + '=' + encodeURIComponent( options.data[i] );
			paramAnd = '&';
		}
		var fname = 'mycpfn_' + ( mw.cb_count++ );
		_global[ fname ] = callback;
		req_url += '&' + options.jsonCB + '=' + fname;
		loadExternalJs( req_url );
	}
}
// Do a request:
// @@note this contains metavid specific local vs remote api remapping.
// this should be depreciated and we should use "$j.get" or an explicate api call 
// (we should not mix the two request types) 
function do_request( req_url, callback ) {
	js_log( 'do_request::req_url:' + mw.parseUri( document.URL ) + ' != ' +  mw.parseUri( req_url ).host );
	// If we are doing a request to the same domain or relative link, do a normal GET
	if ( mw.parseUri( document.URL ).host == mw.parseUri( req_url ).host ||
		req_url.indexOf( '://' ) == -1 ){ // if its a relative url go directly as well
		// Do a direct request
		$j.ajax( {
			type: "GET",
			url: req_url,
			async: false,
			success: function( data ) {
				callback( data );
			}
		} );
	} else {
		// Get data via DOM injection with callback
		global_req_cb.push( callback );
		// Prepend json_ to feed_format if not already requesting json format (metavid specific) 
		if ( req_url.indexOf( "feed_format=" ) != -1 && req_url.indexOf( "feed_format=json" ) == -1 )
			req_url = req_url.replace( /feed_format=/, 'feed_format=json_' );		
		loadExternalJs( req_url + '&cb=mv_jsdata_cb&cb_inx=' + ( global_req_cb.length -1 ) );
	}
}

function mv_jsdata_cb( response ) {
	js_log( 'f:mv_jsdata_cb:' + response['cb_inx'] );
	// Run the callback from the global request callback object
	if ( !global_req_cb[response['cb_inx']] ) {
		js_log( 'missing req cb index' );
		return false;
	}
	if ( !response['pay_load'] ) {
		js_log( "missing pay load" );
		return false;
	}
	switch( response['content-type'] ) {
		case 'text/plain':
		break;
		case 'text/xml':
			if ( typeof response['pay_load'] == 'string' ) {
				 response['pay_load'] = mw.parseXML( response['pay_load'] );
			}
		break
		default:
			js_log( 'bad response type' + response['content-type'] );
			return false;
		break;
	}
	global_req_cb[response['cb_inx']]( response['pay_load'] );
}
// Load external JS via DOM injection
function loadExternalJs( url, callback ) {
	js_log( 'load js: ' + url );
	// if(window['$j']) // use jquery call:
		/*$j.ajax({
			type: "GET",
			url: url,
			dataType: 'script',
			cache: true
		});*/
	// else{
		var e = document.createElement( "script" );
		e.setAttribute( 'src', url );
		e.setAttribute( 'type', "text/javascript" );
		/*if(callback)
			e.onload = callback;
		*/
		// e.setAttribute('defer', true);
		document.getElementsByTagName( "head" )[0].appendChild( e );
	// }
}

if ( typeof DOMParser == "undefined" ) {
	DOMParser = function () { }
	DOMParser.prototype.parseFromString = function ( str, contentType ) {
		if ( typeof ActiveXObject != "undefined" ) {
			var d = new ActiveXObject( "MSXML.DomDocument" );
			d.loadXML( str );
			return d;
		} else if ( typeof XMLHttpRequest != "undefined" ) {
			var req = new XMLHttpRequest;
			req.open( "GET", "data:" + ( contentType || "application/xml" ) +
					";charset=utf-8," + encodeURIComponent( str ), false );
			if ( req.overrideMimeType ) {
				req.overrideMimeType( contentType );
			}
			req.send( null );
			return req.responseXML;
		}
	}
}
/*
* Utility functions
*/
function js_log( string ) {
	// Add any prepend debug strings if necessary 
	if ( mw.getConfig( 'pre-append-log' ) )
		string = mw.getConfig( 'pre-append-log' ) + string;
			
	if ( window.console ) {
		window.console.log( string );
	} else {
		/**
		 * old IE and non-Firebug debug:
		 */
		/*var log_elm = document.getElementById('mv_js_log');
		if(!log_elm){
			document.getElementsByTagName("body")[0].innerHTML = document.getElementsByTagName("body")[0].innerHTML +
				'<div style="position:absolute;z-index:500;top:0px;left:0px;right:0px;height:10px;">'+
				'<textarea id="mv_js_log" cols="120" rows="5"></textarea>'+
				'</div>';

			var log_elm = document.getElementById('mv_js_log');
		}
		if(log_elm){
			log_elm.value+=string+"\n";
		}*/
	}
	return false;
}

function js_error( string ) {
	alert( string );
	return false;
}
