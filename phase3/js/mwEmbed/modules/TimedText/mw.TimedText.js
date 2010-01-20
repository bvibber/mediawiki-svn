/**
 * The Core timed Text interface object
 * 
 * handles class mappings for: 
 * 	menu display ( jquery.ui themeable )
 * 	timed text loading request
 *  timed text edit requests
 * 	timed text search & seek interface ( version 2 ) 
 *
 * @author: Michael Dale
 * 	
 * Some code borrowed from: http://www.annodex.net/~silvia/itext/elephant_no_skin_v2.html
 * ( Licensed under: MPL 1.1/GPL 2.0/LGPL 2.1 )
 * Contributor(s):
 *  Silvia Pfeiffer <silvia@siliva-pfeiffer.de>
 *
 */ 

mw.addMessages( {
	"mwe-back-btn" : "Back",	
	"mwe-chose-text" : "Chose text",
	"mwe-add-timed-text" : "Add timed text",
	"mwe-loading-text-edit" : "Loading timed text editor", 
	
	"mwe-search" : "Search clip",
	
	"mwe-layout" : "Layout", 
	"mwe-layout-ontop" : "Ontop of video",
	"mwe-layout-below": "Below video", 
	"mwe-layout-off" : "Hide subtitles",
	
	"mwe-loading-text" : "Loading text ...",
	
	"mwe-key-language": "$1, $2",
	
	"mwe-textcat-cc" : "Captions",
    "mwe-textcat-sub" : "Subtitles",
    "mwe-textcat-tad" : "Audio description",
    "mwe-textcat-ktv" : "Karaoke",
    "mwe-textcat-tik" : "Ticker text",
    "mwe-textcat-ar" : "Active regions",
    "mwe-textcat-nb" : "Annotation",
    "mwe-textcat-meta" : "Timed metadata",
    "mwe-textcat-trx" : "Transcript",
    "mwe-textcat-lrc" : "Lyrics",
    "mwe-textcat-lin" : "Linguistic markup",
    "mwe-textcat-cue" : "Cue points"
} );

// Bind to mw (for uncluttered global namespace) 
( function( $ ) {				
	
	/** 
	 * Timed Text Object
	 * @param embedPlayer Host player for timedText interfaces 
	 */
	mw.TimedText = function( embedPlayer, options ) {
		return this.init( embedPlayer, options);
	}
	mw.TimedText.prototype = {
		
		/** 
		* Preferences config order is presently: 
		* 1) user cookie
		* 2) defaults provided in this config var: 
		*/
		config: {
			// Layout for basic "timedText" type can be 'ontop', 'off', 'below'
			'layout' : 'ontop',
			
			//Set the default local ( should be grabbed from the browser )			
			'userLanugage' : 'en',
			
			//Set the default category of timedText to display ( un-categorized timed-text is by default "SUB"  )
			'userCategory' : 'SUB'
		},
		
		/**
		 * The list of enabled sources 
		 */
		enabledSources: null,
				
		
		/**
		 * Stores the last text string per category to avoid dom checks 
		 * for updated text 
		 */
		prevText: null,
		
		/**
		* Text sources ( a set of textSource objects )
		*/
		textSources: null,
		
		/**
		* Text Source(s) Setup Flag
		*/
		textSourceSetupFlag: null,		
		
		/*
		 * Hard coded to "commons" right now .. but we will want to support per-asset provider id's
		 * in addition to a standard "callback" system from cross domain grabbing of srt's
		 */
		textProviderId : 'commons',
		
		/**
		* Valid "iText" categories
		*/
		validCategoriesKeys: [
			"CC",
	        "SUB",
	        "TAD",
	        "KTV",
	        "TIK",
	        "AR",
	        "NB",
	        "META",
	        "TRX",
	        "LRC",
	        "LIN",
	        "CUE"
		],
		
		/**
		 * Timed text extension to mime map
		 */
		timedTextExtMime: {
		    'srt': 'text/x-srt',
		    'mw-srt': 'text/mw-srt',
		    'cmml': 'text/cmml'
		},
			
		/**
		 * @constructor
		 * @param {Object} embedPlayer Host player for timedText interfaces  
		 */
		init: function( embedPlayer, options ){
			var _this = this;
			mw.log("TimedText: init() ")
			this.embedPlayer = embedPlayer;	
			this.options = options;
			
			//Init internal variables: 
			this.enabledSources = [];
			this.prevText = '';
			this.textSources = [];
			this.textSourceSetupFlag = false;						
			
			//Set default langauge via wgUserLanguage if set
			if( typeof wgUserLanguage != 'undefined'){
				this.config.userLanugage = wgUserLanguage;
			}
			
			// Load user preferences config:  
			preferenceConfig = mw.getUserConfig( 'timedTextConfig' );
			if( typeof preferenceConfig == 'object' ) {
				this.config = preferenceConfig;
			}
			
			//Set up embedPlayer hooks:			
			embedPlayer.addHook( 'monitor', function(){
				_this.monitor();
			} )
							
			embedPlayer.addHook( 'play', function(){
				// Will load and setup timedText sources (if not loaded already loaded )
				_this.setupTextSources();
			} );					
		},
		
		/**
		* Setups available text sources
		*   loads text sources
		* 	auto-selects a source based on the user language
		* @param {Function} callback Function to be called once text sources are setup. 
		*/
		setupTextSources: function( callback ){
			var _this = this;
			if( this.textSourceSetupFlag ){
				if( callback ){
					callback();
				}
				return ;		
			}
			// Load textSources
			_this.loadTextSources( function(){			
				
				// Enable a default source and issue a request to "load it"
				_this.autoSelectSource();
			
				// Load and parse the text value of enabled text sources:
				_this.loadEnabledSources();
				
				_this.textSourceSetupFlag = true;
				
				if( callback )
					callback();
			} );
		},		
		
		/**
		* Binds the timed text menu 
		* 	and updates its content from "getMainMenu"
		*
		* @param {Object} target to display the menu
		* @param {Boolean} autoShow If the menu should be displayed 
		*/
		bindMenu: function( target , autoShow){
			var _this = this;
			mw.log( "TimedText:bindMenu:" + target );
			_this.menuTarget = 	target;				
			var $menuButton = this.embedPlayer.$interface.find( '.timed-text' );
					
			// Else bind and show the menu 			
			// We already have a loader in embedPlayer so the delay of
			// setupTextSources is already taken into account
			_this.setupTextSources( function(){
				// NOTE: Button target should be an option or config						
				$menuButton.unbind().menu( {
					'content'	: _this.getMainMenu(),
					'crumbDefaultText' : ' ',
					'targetMenuContainer' : _this.menuTarget,
					'autoShow' : autoShow,
					'backLinkText' : gM( 'mwe-back-btn' )							
				} );				
			});								 
		},					
		
		/**
		* Monitor video time and update timed text filed[s]  		
		*/ 
		monitor: function( ){
			//mw.log(" timed Text monitor: " + this.enabledSources.length );
			embedPlayer = this.embedPlayer;
			// Setup local reference to currentTime: 
			var currentTime = embedPlayer.currentTime;					
			
			// Get the text per category
			var textCategories = [ ];
						
			for( var i = 0; i <  this.enabledSources.length ; i++ ) {
				var source =  this.enabledSources[ i ];
				this.updateSourceDisplay( source, currentTime );			
			}
		},			
		
		/**
		 * Load all the available text sources from the inline embed
		 * 	or from a timedTextProviders
		 * @param {Function} callback Function to call once text sources are loaded
		 */
		loadTextSources: function( callback ){
			var _this = this;
			this.textSources = [ ];
			// Get local reference to all timed text sources: ( text/xml, text/x-srt etc )			
			var inlineSources = this.embedPlayer.mediaElement.getSources( 'text' );
			// Add all the sources to textSources
			for( var i = 0 ; i < inlineSources.length ; i++ ){
				// make a new textSource:
				var source = new TextSource( inlineSources[i] );
				this.textSources.push( source );
			}			
			
			//If there are no inline sources check timedTextProviders & apiTitleKey
			if( !this.embedPlayer.apiTitleKey ){
				//no other sources just issue the callback: 						
				callback();
				return ;
			}
			
			// Try to get sources from text provider:
			var provider_id = ( this.embedPlayer.apiProvider ) ?  this.embedPlayer.apiProvider : 'local'; 
			var api_url = mw.getApiProviderURL( provider_id );		
			var assetKey = 	this.embedPlayer.apiTitleKey;			
			if( !api_url || !assetKey ){
				mw.log("Error: loading source without apiProvider or apiTitleKey");
				return ;
			}			
			//For now only support mediaWikiText provider library
			this.textProvider = new mw.MediaWikiTextProvider( {
				'provider_id' : provider_id,						
				'api_url': api_url,
				'embedPlayer': this.embedPlayer
			} );
					
			// Load the textProvider sources
			this.textProvider.loadSources( assetKey,  function( textSources ){
				for( var i in textSources ){					
					var textSource = textSources[ i ];					
					// Try to insert the itext source: 
					var textElm = document.createElement( 'itext' );
					$j( textElm ).attr( {
						'category' : 'SUB',
						'lang' 	: textSource.lang,
						'type' 	: _this.timedTextExtMime[ textSource.extension ],
						'titleKey' 	: textSource.titleKey
					} );
					//debugger;
					// Add the sources to the parent embedPlayer 
					// ( in case other interfaces want to access them )
					var embedSource = _this.embedPlayer.mediaElement.tryAddSource( textElm );	
					// Get a "textSource" object:
					var source = new TextSource( embedSource, _this.textProvider);
					_this.textSources.push( source );				
				}								
				// All sources loaded run callback: 
				callback();
			} );		
		},
		
		/**
		* Get the layout mode
		* 
		* Takes into consideration: 
		* 	Playback method overlays support ( have to put subtitles bellow video )
		* 	 
		*/
		getLayoutMode: function(){
		 	// Re-map "ontop" to "below" if player does not support 
		 	if( this.config.layout == 'ontop' && !this.embedPlayer.supports['overlays']  ){
		 		this.config.layout = 'below';
		 	}	
		 	return this.config.layout;	
		},
		
		/**
		* Auto selects a source given the local configuration 
		* 
		* NOTE: presently this selects a "single" source. 
		* In the future we could support multiple "enabled sources" 
		*/
		autoSelectSource: function(){	
			this.enabledSources = [];
									
			// Check if any source matches our "local"		
			for( var i in this.textSources ){
				var source = this.textSources[ i ];		
				if( this.config.userLanugage  &&
					this.config.userLanugage == source.lang.toLowerCase() ){	
					// Check for category if avaliable  
					this.enabledSources.push( source );
					return ;			
				} 
			}
			// If no userLang, source try enabling english:
			if( this.enabledSources.length == 0 ){
				for( var i in this.textSources ){
					var source = this.textSources[ i ];					
					if( source.lang.toLowerCase() == 'en' ){
						this.enabledSources.push( source );
						return ;
					}
				}	
			}
			// If still no source try the first source we get; 
			if( this.enabledSources.length == 0 ){
				for( var i in this.textSources ){
					var source = this.textSources[ i ];										
					this.enabledSources.push( source );
					return ;
				}	
			}
		},
				
		/**
		* Issue a request to load all enabled Sources
		*  Should be called anytime enabled Source list is updatd
		*/
		loadEnabledSources: function(){			
			for(var i in this.enabledSources ){
				var enabledSource = this.enabledSources[ i ];
				if( ! enabledSource.loaded )
					enabledSource.load();								
			}
		},			
		
		/**
		* Selection of a menu item
		* 
		* @param {Element} item Item selected
		*/
		selectMenuItem: function( item ){			
			mw.log("selectMenuItem: " + $j( item ).find('a').attr('class') );
		},	
		
		/**
		* Checks if a source is "on"
		* @return 
		* 	true if source is on
		* 	false if source is off
		* @type {Boolean}
		*/		
		isSourceEnabled: function( source ){
			for(var i in this.enabledSources ){
				var enabledSource = this.enabledSources[i];
				if( source.id ){
					if( source.id == enabledSource.id )
						return true;
				}
				if( source.lang ){
					if( source.lang == enabledSource.lang )
						return true;
				}
			}	
			return false;
		},
		/*
		* Get a source object by language, returns "false" if not found
		*/
		getSourceByLanguage: function ( langKey ){
			for(var i in this.textSources){
				var source = this.textSources[ i ];
				if( source.lang == langKey )
					return source;
			}
			return false;
		},
		
		/**
		* Builds the core timed Text menu and 
		* returns the binded jquery object / dom set
		*
		* Assumes text sources have been setup: (  _this.setupTextSources() )
		* 
		* calls a few sub-functions:		
		* Basic menu layout:
		*		Chose Language
		*			All Subtiles here ( if we have categories list them ) 
		*		Layout	
		*			Bellow video
		*			Ontop video ( only available to supported plugins )									
		*		[ Search Text ]
		*			[ This video ] 
		*			[ All videos ]
		*		[ Chapters ] seek to chapter
		*/
		getMainMenu: function(){
			var _this = this; 			
		
			
			// Build the source list menu item:	
			$menu = $j( '<ul>' );						
			// Chouse text menu item ( if there are sources)
			if( _this.textSources.length != 0 ){  											
				$menu.append( 
					_this.getLi( gM( 'mwe-chose-text'), 'comment' ).append(										
						_this.getLanguageMenu()
					),
						// Layout Menu option
					_this.getLi( gM( 'mwe-layout' ), 'image' ).append(
						_this.getLayoutMenu()
					)
					
					// Search Menu option
					//_this.getLi( gM('mwe-search'),  'search')					
				);					
			}
			// Put in the "Make Transcript" link
			$menu.append(
				_this.getLiAddText()
			); 
				
										
			return $menu;
		},
		
		/**
		 * Shows the timed text edit ui
		 * 
		 * @param {String} mode Mode or page to display ( to differentiate between edit vs new transcript) 
		 */		
		showTimedTextEditUI: function( mode ){
			var _this = this;
			// Show a loader:
			mw.addLoaderDialog( gM( 'mwe-loading-text-edit' ));
			// Load the timedText edit interface
			mw.load( 'TimedText.Edit', function(){				
				if( ! _this.editText ){
					_this.editText = new mw.TimedTextEdit( _this );
				}
				// Close the loader:
				mw.closeLoaderDialog();
				_this.editText.showUI();
			})
		},
		
		/**
		* Utility function to assist in menu build out:
		* Get menu line item (li) html:  <li><a> msgKey </a></li> 
		*
		* @param {String} msgKey Msg key for menu item
		*/
		
		/**
		 * Get the add text menu item: 
		 */		
		getLiAddText: function(){
			var _this = this;			
			return _this.getLi( gM( 'mwe-add-timed-text'), 'script', function(){
				_this.showTimedTextEditUI( 'add' );
			} )
		},
		
		/**
		* Get line item (li) from source object
		* @param {Object} source Source to get menu line item from
		*/
		getLiSource: function( source ){		
			var _this = this;
			//See if the source is currently "on"
			var source_icon = ( this.isSourceEnabled( source ) )? 'bullet' : 'radio-on'; 
			
			if( source.title ){
				return this.getLi( source.title, source_icon, function(){
					mw.log(" call selectTextSource");
					_this.selectTextSource( source ); 
				});
			}
			
			if( source.lang ){
				var langKey = source.lang.toLowerCase();
				_this.getLanguageName ( langKey );
				return this.getLi( 
					gM('mwe-key-language', [langKey,	unescape( mw.languages[ source.lang ] )	] ), 
					source_icon,
					function(){
						mw.log(" call selectTextSource");
						_this.selectTextSource( source ); 
					} 
				);
			}			
		},				
		
		/**
		* Get line item ( li ) from text string
		* @param {String} string Text to display for the menu item
		* @param {String} icon jQuery UI icon key displayed to the left of the menu item
		*/
		getLi: function( string, icon , callback){			
			var $li = $j( '<li>' ).append(		
				$j('<a>')
					.attr('href', '#')
					.click( callback )			
			)
			if( icon ){
				$li.find( 'a' ).append(	
					$j('<span style="float:left;"></span>')
						.addClass( 'ui-icon ui-icon-' + icon ) 
				)		
			}		
			$li.find( 'a' ).append( $j('<span>').text( string ) );			
			//mw.log(' li html: ' + $j('<div>').append( $li ).html() );
			return $li;
		},
		
		/**
	 	 * Get lagnuage name from language key
	 	 * @param {String} lang_key Language key
	 	 */
	 	getLanguageName: function( lang_key ){
	 		if( mw.languages[ lang_key ]){
	 			return mw.languages[ lang_key ];
	 		}
	 		return false
	 	},	
		
		/** 
		* Builds and returns the "layout" menu 
		* @return {Object} 
		* 	The jquery menu dom object
		*/		
		getLayoutMenu: function(){
			var _this = this;
			var layoutOptions = [ ];
			
			//Only display the "ontop" option if the player supports it: 
			if( this.embedPlayer.supports[ 'overlays' ] )
				layoutOptions.push( 'ontop' );
				
			//Add below and "off" options: 	
			layoutOptions.push( 'below' );
			layoutOptions.push( 'off' );
									
			$ul = $j('<ul>');			
			$j.each( layoutOptions, function( na, layoutMode ){				
				var icon = ( _this.config.layout == layoutMode ) ? 'bullet' : 'radio-on';   
				$ul.append( 
					_this.getLi( 
						gM( 'mwe-layout-' + layoutMode), 
						icon,
						function(){		
							_this.selectLayout( layoutMode );							
						} ) 
					)		
			});			
			return $ul;
		},
		
		/**
		* Select a new layout
		* @param {Object} layoutMode The selected layout mode
		*/
		selectLayout: function( layoutMode ){
			var _this = this;
			if( layoutMode != _this.config.layout ){			
				// Update the config and redraw layout
				_this.config.layout = layoutMode;
				
				// Update the user config: 
				mw.setUserConfig( 'timedTextConfig', _this.config);
						 
				// Update the display: 
				_this.updateLayout();
			}
		},
		
		/**
		* Updates the timed text layout ( should be called when  config.layout changes )
		*/
		updateLayout: function(){
			var $playerTarget =  this.embedPlayer.$interface;	
			$playerTarget.find('.itext').remove();
			this.refreshDisplay();
		},
		
		/**
		* Select a new source
		*
		* @param {Object} source Source object selected
		*/
		selectTextSource: function( source ){
			var _this = this;
			mw.log(" select source: " + source.lang );			
			
			// Update the config language if the source includes language
			if( source.lang )
				this.config.userLanugage =  source.lang;
				
			if( source.category )
				this.config.userCategory = source.category;
			
			// (@@todo update category & setup category language buckets? )
			
			// Remove any other sources selected in sources category
			this.enabledSources = [];
			
			this.enabledSources.push( source );
			//Set any existing text target to "loading"
			if( !source.loaded ) {
				var $playerTarget = this.embedPlayer.$interface; 			
				$playerTarget.find('.itext').text( gM('mwe-loading-text') );
			}
			// Load the text:
			source.load( function(){
				// Refresh the interface: 
				_this.refreshDisplay();
			})								
		},
		
		/**
		* Refresh the display, updates the timedText layout, menu, and text display 
		*/
		refreshDisplay: function(){
			// Empyt out previus text to force an interface update: 
			this.prevText = [];
			// Refresh the Menu (if it has a target to refresh) 
			if( this.menuTarget ){				
				this.bindMenu(  this.menuTarget, false )
			}
			// Issues a "monitor" command to update the timed text for the new layout
			this.monitor();
		},
		
		/**
		* Builds the language source list menu
		* checks all text sources for category and language key attribute
		*/
		getLanguageMenu: function(){
			var _this = this;
							
			// See if we have categories to worry about:
			
			var catSourceList = [ ];	
			// ( All sources should have a category (depreciate ) 
			var sourcesWithoutCategory = [ ];	
			for( var i in this.textSources ) {
				var source = this.textSources[ i ];
				if( source.category ){
					var catKey = source.category ;
					// Init Category menu item if it does not already exist: 
					if( !catSourceList[ catKey ] ){						
						// Set up catList pointer: 
						catSourceList[ catKey ] = [ ]						
					}
					// Append to the source category key menu item:
					catSourceList[ catKey ].push(
						_this.getLiSource( source )
					)		
				}else{			
					sourcesWithoutCategory.push( _this.getLiSource( source ) );
				}
			}
			var $langMenu = $j('<ul>');			
			// Check if we have multiple categories ( if not just list them under the parent menu item)
			if( catSourceList.length > 1 ){			
				for(var catKey in catSourceList){				
					$catChildren = $j('<ul>');
					for(var i in catSourceList[ catKey ]){
						$catChildren.append(
							catSourceList[ catKey ][i]
						) 
					}
					// Append a cat menu item for each category list
					$langMenu.append(
						_this.getLi( gM( 'mwe-textcat-' + catKey.toLowerCase() ) ).append(
							$catChildren
						)
					);
				}
			} else {
				for(var catKey in catSourceList){		
					for(var i in catSourceList[ catKey ]){
						$langMenu.append(
							catSourceList[ catKey ][i]
						) 
					}
				}
			}		
			
			for(var i in sourcesWithoutCategory){
				$langMenu.append( sourcesWithoutCategory[i] )
			}			
			
			//Add in the "add text" to the end of the interface: 
			$langMenu.append( 
				_this.getLiAddText() 
			);
			
			return $langMenu; 
		},			
		
		/**
		 * Updates a source display in the interface for a given time
		 * @param {Object} source Source to update
		 */
		updateSourceDisplay: function ( source, time ){			
			// Get the source text for the requested time: 		
			var text = source.getTimedText( time );
			
			// We do a type comparison so that "undefined" != "false" 
			if( text === this.prevText[ source.category ] )
				return ;
			
			//mw.log( 'updateTextDisplay: ' + text );	
					
			var $playerTarget =  this.embedPlayer.$interface;	
			var $textTarget = $playerTarget.find( '.itext_' + source.category + ' span' );			
			// If we are missing the target add it: 		
			if( $textTarget.length == 0){
				this.addItextDiv( source.category )
				// Re-grab the textTarget:
				$textTarget = $playerTarget.find( '.itext_' + source.category + ' span' );
			}
			
			
			// If text is "false" fade out the subtitle: 
			if( text === false ){
				$textTarget.fadeOut('fast');
			}else{
				// Fade in the target if not visible
				if( ! $textTarget.is(':visible') ){
					$textTarget.fadeIn('fast');
				}					
				// Update text ( use "html" instead of "text" so that parsers can swap in html for formating
				$textTarget.html( text );
			}
			//mw.log( ' len: ' + $textTarget.length + ' ' + $textTarget.html() );
			// Update the prev text:
			this.prevText[ source.category ] = text;		
		},
		
		
		/**
		 * Add an itext div to the embedPlayer
		 */
		addItextDiv: function( category ){			 		
			mw.log(" addItextDiv: " +  category )			
			// Get the relative positioned player class from the ctrlBuilder:
			var $playerTarget =  this.embedPlayer.$interface;
			
			//Remove any existing itext divs for this player;
			$playerTarget.find('.itext_' + category ).remove();
			
			// Setup the display text div: 
			var layoutMode = this.getLayoutMode();
			if( layoutMode == 'ontop' ){				  
				$playerTarget.append(
					$j('<div>').addClass( 'itext' + ' ' + 'itext_' + category )					
						.css( {
							"max-width": ( this.embedPlayer.width - 20 ), 
							'position':'absolute',
							'bottom': ( this.embedPlayer.ctrlBuilder.height + 10 ),
							'width': '100%',
							'display': 'block',
    						'opacity': .8,
							'text-align':'center'								
						}).append(
							$j('<span>').css({
								'color':'white',
								'background-color':'#333'
							})
						)    						
				);				
				// Resize the interface for layoutMode == 'ontop' 
				this.embedPlayer.$interface.animate({
					'height': this.embedPlayer.height + this.embedPlayer.ctrlBuilder.height
				})	
			}else if ( layoutMode == 'below'){
				// Append before controls: 
				$playerTarget.find( '.control-bar' ).before(
					$j('<div>').addClass( 'itext' + ' ' + 'itext_' + category )
						.css({
							'display': 'block',
							'width': '100%',
							'height': '60px',
							'background-color':'#000',
							'text-align':'center',
							'padding-top':'5px'
						} ).append(
							$j('<span>').css( {
								'color':'white'													
							} )
						) 
				);		
				var height = 62 + this.embedPlayer.height + this.embedPlayer.ctrlBuilder.height;
				mw.log( 'set height:' + height );
				// Resize the interface for layoutMode == 'below' 
				this.embedPlayer.$interface.animate({
					'height': height
				})		
				mw.log( ' height of ' + this.embedPlayer.id + ' is now: ' + $j( '#' + this.embedPlayer.id ).height() );
			}	
			mw.log( 'should have been appended: ' + $playerTarget.find('.itext').length );		
		}
	}		 
		
	/**
	 * TextSource object extends a base mediaSource object 
	 *  with some timedText features
	 *
	 * @param {Object} source Source object to extend
	 * @param {Object} {Optional} textProvider The text provider interface ( to load source from api ) 
	 */
	TextSource = function( source , textProvider){
		return this.init( source, textProvider );
	}
	TextSource.prototype = {
	
		//The load state:
		loaded: false,
		
		// Container for the captions
		// captions include "start", "end" and "content" fields
		captions: [],
		
		// The previous index of the timed text served
		// Avoids searching the entire array on time updates. 
		prevIndex: 0,
		
		/**
		 * @constructor Inherits mediaSource from embedPlayer
		 * @param {source} Base source element
		 */
		init: function( source , textProvider){
			for( var i in source){
				this[i] = source[i];	
			}			
			// Set default category to subtitle if unset: 
			if( ! this.category ){
				this.category = 'SUB';
			}
			//Set the textProvider if provided
			if( textProvider ){
				this.textProvider = textProvider;
			}
		},
		
		/**
		 * Function to load and parse the source text
		 * @param {Function} callback Function called once text source is loaded
		 */
		load: function( callback ){
			var _this = this;
						
			//check if its already loaded:
			if( _this.loaded ){
				if( callback ){ 
					callback();
				}
			}
			// Set parser handler: 
			switch( this.getMIMEType() ){
				//Special mediaWiki srt format ( support wiki-text in srt's )
				case 'text/mw-srt':
					var handler = parseMwSrt;
				break;
				case 'text/x-srt':				
					var handler = parseSrt;
				break;
				case 'text/cmml':
					var handler = parseCMML; 
				break;
				default: 
					var hanlder = null;
				break;
			}
			if( !handler ){
				mw.log("Error: no handler for type: " + this.getMIMEType() );
				return ;
			}						
			// Try to load src via src attr:			
			if( this.getSrc() ){
				// Issue the direct load request ( if we can ) 
				if ( !mw.isLocalDomain( this.getSrc() ) ){
					mw.log("Error: cant load crossDomain src:" + this.getSrc()   )
					return ;
				}
				$j.get( this.getSrc(), function( data ){		
					// Parse and load captions:
					_this.captions = handler( data );
					// Update the loaded state:
					_this.loaded = true;
					if( callback ){ 
						callback();
					}
				}, 'text' );
				return ;
			}			
			
			// Try to load src via textProvider:
			if( this.textProvider && this.titleKey ){
				this.textProvider.loadTitleKey( this.titleKey, function( data ){
					if( data ){
						_this.captions = handler( data );					
					}					
					if( callback ){ 
						callback();
					}
					return ;
				});
			}
		},
		
		/**
		* Returns the text content for requested time
		*
		* @param {String} time Time in seconds
		*/				
		getTimedText: function ( time ){			
			var prevCaption =  this.captions[ this.prevIndex ];
			
			// Setup the startIndex: 
			if( prevCaption && time >= prevCaption.start ){
				var startIndex = this.prevIndex;
			}else{
				//If a backwards seek start searching at the start: 
				var startIndex = 0;
			}			 			
			// Start looking for the text via time, return first match: 
			for( var i = startIndex ; i < this.captions.length; i ++ ){
				caption = this.captions[ i ];				
				if( time >= caption.start  && 
					time <= caption.end ){
					this.prevIndex = i;
					return caption.content;
				}
			}
			//No text found in range return false: 
			return false;
		}		
	}
	/**	
	 * parse mediaWiki html srt 
	 * @param {Object} data XML data string to be parsed
	 */
	function parseMwSrt( data ){
		var captions = [ ];
		var curentCap = [];
		var parseNextAsTime = false;		
		// Optimize: we could use javascript strings functions instead of jQuery XML parsing:  		
		$j( '<div>' + data + '</div>' ).find('p').each( function(){					
			currentPtext = $j(this).html();
			//mw.log( 'pText: ' + currentPtext );
			
			//Check if the p matches the "all in one line" match: 
			var m = currentPtext.replace('--&gt;', '-->').match(/\d+\s(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?\n?(.*)/);
            if (m) {               
               captions.push({
				'start': 
					(parseInt(m[1], 10) * 60 * 60) +
			        (parseInt(m[2], 10) * 60) +
			        (parseInt(m[3], 10)) +
			        (parseInt(m[4], 10) / 1000),
				'end':
                  	(parseInt(m[5], 10) * 60 * 60) +
                 	(parseInt(m[6], 10) * 60) +
                  	(parseInt(m[7], 10)) +
                  	(parseInt(m[8], 10) / 1000),
               	'content': $j.trim( m[9] )
               });
               return 'next';
            } 
            // Else check for multi-line match:
            if( parseInt( currentPtext ) ==  currentPtext ){
            	if( curentCap.length != 0) {
            		captions.push( curentCap );
            	}
				curentCap = {
					'content': ''
				};	
				return 'next';
			}
			//Check only for time match:
			var m = currentPtext.replace('--&gt;', '-->').match(/(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?/);
			if (m) {
				curentCap['start']= 
					(parseInt(m[1], 10) * 60 * 60) +
			        (parseInt(m[2], 10) * 60) +
			        (parseInt(m[3], 10)) +
			        (parseInt(m[4], 10) / 1000);
				curentCap['end']=
                  	(parseInt(m[5], 10) * 60 * 60) +
                 	(parseInt(m[6], 10) * 60) +
                  	(parseInt(m[7], 10)) +
                  	(parseInt(m[8], 10) / 1000);
                return 'next';
			}
			//Else content for the curentCap
			if( currentPtext != '<br>' ){
				curentCap['content'] +=  currentPtext;
			}           		
		});
		//Push last subtitle: 
		if( curentCap.length != 0){
    		captions.push( curentCap );
    	}		    	
		return captions;
	}
	/**
	 * srt timed text parse handle:
	 * @param {String} data Srt string to be parsed
	 */
	function parseSrt( data ) {		
		// Remove dos newlines
	    var srt = data.replace(/\r+/g, '');
	     
	    // Trim white space start and end
	    srt = srt.replace(/^\s+|\s+$/g, '');
	    
	    // Remove all html tags for security reasons
	    srt = srt.replace(/<[a-zA-Z\/][^>]*>/g, ''); 
	
	    // Get captions
	    var captions = [ ];
	    var caplist = srt.split('\n\n');
	    for (var i = 0; i < caplist.length; i=i+1) {
	        var caption = "";
	        var content, start, end, s;
	        caption = caplist[i];
	        s = caption.split(/\n/);
	        if (s[0].match(/^\d+$/) && s[1].match(/\d+:\d+:\d+/)) {	        	
	            // ignore caption number in s[0]
	            // parse time string
	            var m = s[1].match(/(\d+):(\d+):(\d+)(?:,(\d+))?\s*--?>\s*(\d+):(\d+):(\d+)(?:,(\d+))?/);
	            if (m) {
	                start =
	                  (parseInt(m[1], 10) * 60 * 60) +
	                  (parseInt(m[2], 10) * 60) +
	                  (parseInt(m[3], 10)) +
	                  (parseInt(m[4], 10) / 1000);
	                end =
	                  (parseInt(m[5], 10) * 60 * 60) +
	                  (parseInt(m[6], 10) * 60) +
	                  (parseInt(m[7], 10)) +
	                  (parseInt(m[8], 10) / 1000);
	            } else {
	                // Unrecognized timestring
	                continue;
	            }
	            // concatenate text lines to html text
	            content = s.slice(2).join("<br>");
	        } else {
	            // file format error or comment lines
	            continue;
	        }
	        captions.push({
	        	'start' : start,
	        	'end' : end, 
	        	'content' : content
	        } );
	    }
	
	    return captions;
	}
	/** 
	 * CMML parser handle
	 * @param {Mixed} data String or XML tree of CMML data to be parsed
	 */
	function parseCMML( data ){
		var captions = [ ];
		$j( data ).find( 'clip' ).each( function( inx, clip ) {			
	        var content, start, end;
			// mw.log(' on clip ' + clip.id);
			start = mw.npt2seconds( $j( clip ).attr( 'start' ).replace( 'npt:', '' ) );
			end = mw.npt2seconds( $j( clip ).attr( 'end' ).replace( 'npt:', '' ) );
			
			$j( clip ).find( 'body' ).each( function( binx, bn ) {
				if ( bn.textContent ) {
					content = bn.textContent;
				} else if ( bn.text ) {
					content = bn.text;
				}
			} );
			captions.push ( {
				'start' : start, 
				'end' : end, 
				'content' : content
			} );
		} );
		
		return captions;
	}		
	
	/**
	 * Text Providers
	 * 
	 * text provider objects let you map your player to a timed text provider 
	 * can provide discovery, and contribution push back
	 * 
	
	// Will add a base class once we are serving more than just mediaWiki "commons"  
	mw.BaseTextProvider = function(){
		return this.init(); 	
	}
	mw.BaseTextProvider.prototype = {
		init: function(){
			
		}
	} 

	 */
	 var default_textProvider_attr = [
	 	'api_url',
	 	'provider_id',
	 	'timed_text_NS',
	 	'embedPlayer'
	 ];
	 	
	 mw.MediaWikiTextProvider = function( options ){
	 	this.init( options )
	 }	
	 mw.MediaWikiTextProvider.prototype = {
	 	
	 	// The api url:
	 	api_url: null,
	 	
	 	// The timed text namespace
	 	timed_text_NS: null,
	 	
	 	/**
	 	* @constructor
	 	* @param {Object} options Set of options for the provider
	 	*/
	 	init: function( options ){
	 		for(var i in default_textProvider_attr){
	 			var attr =  default_textProvider_attr[ i ];
	 			if( options[ attr ] )
	 				this[ attr ] = options[ attr ];
	 				
	 		}	 		
	 	},
	 	
	 	/**   
	 	 * Loads a single text source by titleKey
	 	 * @param {titleKey}  
	 	 */
	 	loadTitleKey: function( titleKey, callback ){
	 		var request = {
				'action': 'parse',
				'page': titleKey
			};
			mw.getJSON( this.api_url, request, function( data ){							
				if ( data && data.parse && data.parse.text['*'] ) {		
					callback(  data.parse.text['*']  );			
					return;		
				}
				mw.log("Error: could not load:" + titleKey);
				callback( false );
			} );
	 	},
	 	
	 	/**
	 	 * Loads all available source for a given assetKey
	 	 *
	 	 * @param {String} assetKey  For mediaWiki the assetKey is the "wiki title"
	 	 */
	 	loadSources: function( assetKey, callback ){
	 		var request = {};
	 		var _this = this;
	 		this.getSourcePages( assetKey, function( sourcePages ){
	 			mw.log(' got sub pages... ');	 			
	 			if( ! sourcePages.query.allpages ){
	 				//Check if a shared asset
	 				mw.log( 'no subtitle pages found');
	 				callback();
	 				return ;
	 			}
	 			// We have sources put them into the player	 			
	 			callback( _this.getSources( sourcePages ) );
	 		} );
	 	},
	 	
	 	/**
	 	 * Get the subtitle pages
	 	 * @param {String} titleKey Title to get subtitles for
	 	 * @param {Function} callback Function to call once NS subs are grabbed 
	 	 */ 
	 	getSourcePages: function( titleKey, callback ){
	 		var _this = this;
		 	var request =  {
				'list' : 'allpages',
				'apprefix' : titleKey,
				'apnamespace' : this.getTimedTextNS(),
				'aplimit' : 200,
				'prop':'revisions'
			};
			mw.getJSON( this.api_url, request, function( sourcePages ) {
				if (	sourcePages.error && sourcePages.error.code == 'apunknown_apnamespace' ) {
					var request = { 
						'list' : 'allpages', 
						'apprefix' : _this.getCanonicalTimedTextNS() + ':' + _this.embedPlayer.apiTitleKey 
					};
					mw.getJSON( apiUrl, request, function( sourcePages ) {
						callback( sourcePages )
					} );
				} else {
					callback( sourcePages );
				}
			} );	
	 	},
	 	
	 	/**
	 	 * Get the sources from sourcePages data object ( api result )
	 	 * @param {Object} sourcePages Source page result object
	 	 */
	 	getSources: function( sourcePages  ) {
			var _this = this;
			// look for text tracks:
			var foundTextTracks = false;
			var sources = [];
			for ( var i in sourcePages.query.allpages ) {
				
				var subPage = sourcePages.query.allpages[i];
				var langKey = subPage.title.split( '.' );
				var extension = langKey.pop();
				langKey = langKey.pop();
				
				//NOTE: we hard code the mw-srt type
				// ( This is because mediaWiki srt files can have wiki-text and parsed as such )
				if( extension == 'srt' ){ 
					extension = 'mw-srt';
				}
				
				if ( ! _this.isSuportedLang( langKey ) ) {
					mw.log( 'Error: langkey:' + langKey + ' not supported' );
				} else {
					sources.push( {
						'extension': extension,
						'lang': langKey,
						'titleKey': subPage.title
					} );										
				}
			}
			return sources;
	 	},
	 	
	 	/**
	 	 * Return the namespace (if not encoded on the page return default 102 )
	 	 */
	 	getTimedTextNS: function(){
	 		if( this.timed_text_NS )
	 			return this.timed_text_NS;
			if ( typeof wgNamespaceIds != 'undefined' && wgNamespaceIds['timedtext'] ) {
				this.timed_text_NS = wgNamespaceIds['timedtext'];
			}else{
				//default value is 102 ( probably should store this elsewhere )
				this.timed_text_NS = 102;
			}
			return this.timed_text_NS;
	 	},
	 	
	 	/**
	 	 * Get the Canonical timed text namespace text 
	 	 */
	 	getCanonicalTimedTextNS: function(){
	 		return 'TimedText';
	 	},	 	
	 	
	 	/**
	 	 * Check if the language is supported
	 	 */
	 	isSuportedLang: function( lang_key ){
	 		if( mw.languages[ lang_key ]){
	 			return true;
	 		}
	 		return false;
	 	} 		 
	 	
	 }
	
	
} )( window.mw );

/**
* jQuery entry point for timedText interface:
*/
( function( $ ) {
	/**
	* jquery timedText binding.
	* Calls mw.timedText on the given selector
	* 
	* @param {Object} options Options for the timed text menu 
	*/
	$.fn.timedText = function ( action, target  ){	
		if( !target )
			options = action;
		if( !options )
			options = {};
		
		$j( this.selector ).each(function(){
			var embedPlayer = $j(this).get(0);			
			
			// Setup timed text for the given player: 
			if( ! embedPlayer.timedText ){
				embedPlayer.timedText = new mw.TimedText( embedPlayer, options);
			}			
			
			//
			if( action == 'showMenu' ) {
				// Bind the menu to the target with autoShow = true
				embedPlayer.timedText.bindMenu( target, true );
			}
		} );		
	}
} )( jQuery );

/**
 * List of all languages mediaWiki supports ( Avoid an api call to get this same info )
 * http://commons.wikimedia.org/w/api.php?action=query&meta=siteinfo&siprop=languages&format=jsonfm
 */
mw.languages = {
	"aa" : "Qaf\u00e1r af",
	"ab" : "\u0410\u04a7\u0441\u0443\u0430",
	"ace" : "Ac\u00e8h",
	"af" : "Afrikaans",
	"ak" : "Akan",
	"aln" : "Geg\u00eb",
	"als" : "Alemannisch",
	"am" : "\u12a0\u121b\u122d\u129b",
	"an" : "Aragon\u00e9s",
	"ang" : "Anglo-Saxon",
	"ar" : "\u0627\u0644\u0639\u0631\u0628\u064a\u0629",
	"arc" : "\u0710\u072a\u0721\u071d\u0710",
	"arn" : "Mapudungun",
	"arz" : "\u0645\u0635\u0631\u0649",
	"as" : "\u0985\u09b8\u09ae\u09c0\u09af\u09bc\u09be",
	"ast" : "Asturianu",
	"av" : "\u0410\u0432\u0430\u0440",
	"avk" : "Kotava",
	"ay" : "Aymar aru",
	"az" : "Az\u0259rbaycan",
	"ba" : "\u0411\u0430\u0448\u04a1\u043e\u0440\u0442",
	"bar" : "Boarisch",
	"bat-smg" : "\u017demait\u0117\u0161ka",
	"bcc" : "\u0628\u0644\u0648\u0686\u06cc \u0645\u06a9\u0631\u0627\u0646\u06cc",
	"bcl" : "Bikol Central",
	"be" : "\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f",
	"be-tarask" : "\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f (\u0442\u0430\u0440\u0430\u0448\u043a\u0435\u0432\u0456\u0446\u0430)",
	"be-x-old" : "\u0411\u0435\u043b\u0430\u0440\u0443\u0441\u043a\u0430\u044f (\u0442\u0430\u0440\u0430\u0448\u043a\u0435\u0432\u0456\u0446\u0430)",
	"bg" : "\u0411\u044a\u043b\u0433\u0430\u0440\u0441\u043a\u0438",
	"bh" : "\u092d\u094b\u091c\u092a\u0941\u0930\u0940",
	"bi" : "Bislama",
	"bm" : "Bamanankan",
	"bn" : "\u09ac\u09be\u0982\u09b2\u09be",
	"bo" : "\u0f56\u0f7c\u0f51\u0f0b\u0f61\u0f72\u0f42",
	"bpy" : "\u0987\u09ae\u09be\u09b0 \u09a0\u09be\u09b0\/\u09ac\u09bf\u09b7\u09cd\u09a3\u09c1\u09aa\u09cd\u09b0\u09bf\u09af\u09bc\u09be \u09ae\u09a3\u09bf\u09aa\u09c1\u09b0\u09c0",
	"bqi" : "\u0628\u062e\u062a\u064a\u0627\u0631\u064a",
	"br" : "Brezhoneg",
	"bs" : "Bosanski",
	"bug" : "\u1a05\u1a14 \u1a15\u1a18\u1a01\u1a17",
	"bxr" : "\u0411\u0443\u0440\u044f\u0430\u0434",
	"ca" : "Catal\u00e0",
	"cbk-zam" : "Chavacano de Zamboanga",
	"cdo" : "M\u00ecng-d\u0115\u0324ng-ng\u1e73\u0304",
	"ce" : "\u041d\u043e\u0445\u0447\u0438\u0439\u043d",
	"ceb" : "Cebuano",
	"ch" : "Chamoru",
	"cho" : "Choctaw",
	"chr" : "\u13e3\u13b3\u13a9",
	"chy" : "Tsets\u00eahest\u00e2hese",
	"ckb" : "Soran\u00ee \/ \u06a9\u0648\u0631\u062f\u06cc",
	"ckb-latn" : "\u202aSoran\u00ee (lat\u00een\u00ee)\u202c",
	"ckb-arab" : "\u202b\u06a9\u0648\u0631\u062f\u06cc (\u0639\u06d5\u0631\u06d5\u0628\u06cc)\u202c",
	"co" : "Corsu",
	"cr" : "N\u0113hiyaw\u0113win \/ \u14c0\u1426\u1403\u152d\u140d\u140f\u1423",
	"crh" : "Q\u0131r\u0131mtatarca",
	"crh-latn" : "\u202aQ\u0131r\u0131mtatarca (Latin)\u202c",
	"crh-cyrl" : "\u202a\u041a\u044a\u044b\u0440\u044b\u043c\u0442\u0430\u0442\u0430\u0440\u0434\u0436\u0430 (\u041a\u0438\u0440\u0438\u043b\u043b)\u202c",
	"cs" : "\u010cesky",
	"csb" : "Kasz\u00ebbsczi",
	"cu" : "\u0421\u043b\u043e\u0432\u0463\u0301\u043d\u044c\u0441\u043a\u044a \/ \u2c14\u2c0e\u2c11\u2c02\u2c21\u2c10\u2c20\u2c14\u2c0d\u2c1f",
	"cv" : "\u0427\u04d1\u0432\u0430\u0448\u043b\u0430",
	"cy" : "Cymraeg",
	"da" : "Dansk",
	"de" : "Deutsch",
	"de-at" : "\u00d6sterreichisches Deutsch",
	"de-ch" : "Schweizer Hochdeutsch",
	"de-formal" : "Deutsch (Sie-Form)",
	"diq" : "Zazaki",
	"dk" : "Dansk (deprecated:da)",
	"dsb" : "Dolnoserbski",
	"dv" : "\u078b\u07a8\u0788\u07ac\u0780\u07a8\u0784\u07a6\u0790\u07b0",
	"dz" : "\u0f47\u0f7c\u0f44\u0f0b\u0f41",
	"ee" : "E\u028begbe",
	"el" : "\u0395\u03bb\u03bb\u03b7\u03bd\u03b9\u03ba\u03ac",
	"eml" : "Emili\u00e0n e rumagn\u00f2l",
	"en" : "English",
	"en-gb" : "British English",
	"eo" : "Esperanto",
	"es" : "Espa\u00f1ol",
	"et" : "Eesti",
	"eu" : "Euskara",
	"ext" : "Estreme\u00f1u",
	"fa" : "\u0641\u0627\u0631\u0633\u06cc",
	"ff" : "Fulfulde",
	"fi" : "Suomi",
	"fiu-vro" : "V\u00f5ro",
	"fj" : "Na Vosa Vakaviti",
	"fo" : "F\u00f8royskt",
	"fr" : "Fran\u00e7ais",
	"frc" : "Fran\u00e7ais cadien",
	"frp" : "Arpetan",
	"fur" : "Furlan",
	"fy" : "Frysk",
	"ga" : "Gaeilge",
	"gag" : "Gagauz",
	"gan" : "\u8d1b\u8a9e",
	"gan-hans" : "\u8d63\u8bed(\u7b80\u4f53)",
	"gan-hant" : "\u8d1b\u8a9e(\u7e41\u9ad4)",
	"gd" : "G\u00e0idhlig",
	"gl" : "Galego",
	"glk" : "\u06af\u06cc\u0644\u06a9\u06cc",
	"gn" : "Ava\u00f1e'\u1ebd",
	"got" : "\ud800\udf32\ud800\udf3f\ud800\udf44\ud800\udf39\ud800\udf43\ud800\udf3a",
	"grc" : "\u1f08\u03c1\u03c7\u03b1\u03af\u03b1 \u1f11\u03bb\u03bb\u03b7\u03bd\u03b9\u03ba\u1f74",
	"gsw" : "Alemannisch",
	"gu" : "\u0a97\u0ac1\u0a9c\u0ab0\u0abe\u0aa4\u0ac0",
	"gv" : "Gaelg",
	"ha" : "\u0647\u064e\u0648\u064f\u0633\u064e",
	"hak" : "Hak-k\u00e2-fa",
	"haw" : "Hawai`i",
	"he" : "\u05e2\u05d1\u05e8\u05d9\u05ea",
	"hi" : "\u0939\u093f\u0928\u094d\u0926\u0940",
	"hif" : "Fiji Hindi",
	"hif-deva" : "\u092b\u093c\u0940\u091c\u0940 \u0939\u093f\u0928\u094d\u0926\u0940",
	"hif-latn" : "Fiji Hindi",
	"hil" : "Ilonggo",
	"ho" : "Hiri Motu",
	"hr" : "Hrvatski",
	"hsb" : "Hornjoserbsce",
	"ht" : "Krey\u00f2l ayisyen",
	"hu" : "Magyar",
	"hy" : "\u0540\u0561\u0575\u0565\u0580\u0565\u0576",
	"hz" : "Otsiherero",
	"ia" : "Interlingua",
	"id" : "Bahasa Indonesia",
	"ie" : "Interlingue",
	"ig" : "Igbo",
	"ii" : "\ua187\ua259",
	"ik" : "I\u00f1upiak",
	"ike-cans" : "\u1403\u14c4\u1483\u144e\u1450\u1466",
	"ike-latn" : "inuktitut",
	"ilo" : "Ilokano",
	"inh" : "\u0413\u0406\u0430\u043b\u0433\u0406\u0430\u0439 \u011eal\u011faj",
	"io" : "Ido",
	"is" : "\u00cdslenska",
	"it" : "Italiano",
	"iu" : "\u1403\u14c4\u1483\u144e\u1450\u1466\/inuktitut",
	"ja" : "\u65e5\u672c\u8a9e",
	"jbo" : "Lojban",
	"jut" : "Jysk",
	"jv" : "Basa Jawa",
	"ka" : "\u10e5\u10d0\u10e0\u10d7\u10e3\u10da\u10d8",
	"kaa" : "Qaraqalpaqsha",
	"kab" : "Taqbaylit",
	"kg" : "Kongo",
	"ki" : "G\u0129k\u0169y\u0169",
	"kiu" : "Kurmanc\u00ee",
	"kj" : "Kwanyama",
	"kk" : "\u049a\u0430\u0437\u0430\u049b\u0448\u0430",
	"kk-arab" : "\u202b\u0642\u0627\u0632\u0627\u0642\u0634\u0627 (\u062a\u0674\u0648\u062a\u06d5)\u202c",
	"kk-cyrl" : "\u202a\u049a\u0430\u0437\u0430\u049b\u0448\u0430 (\u043a\u0438\u0440\u0438\u043b)\u202c",
	"kk-latn" : "\u202aQazaq\u015fa (lat\u0131n)\u202c",
	"kk-cn" : "\u202b\u0642\u0627\u0632\u0627\u0642\u0634\u0627 (\u062c\u06c7\u0646\u06af\u0648)\u202c",
	"kk-kz" : "\u202a\u049a\u0430\u0437\u0430\u049b\u0448\u0430 (\u049a\u0430\u0437\u0430\u049b\u0441\u0442\u0430\u043d)\u202c",
	"kk-tr" : "\u202aQazaq\u015fa (T\u00fcrk\u00efya)\u202c",
	"kl" : "Kalaallisut",
	"km" : "\u1797\u17b6\u179f\u17b6\u1781\u17d2\u1798\u17c2\u179a",
	"kn" : "\u0c95\u0ca8\u0ccd\u0ca8\u0ca1",
	"ko" : "\ud55c\uad6d\uc5b4",
	"ko-kp" : "\ud55c\uad6d\uc5b4 (\uc870\uc120)",
	"kr" : "Kanuri",
	"kri" : "Krio",
	"krj" : "Kinaray-a",
	"ks" : "\u0915\u0936\u094d\u092e\u0940\u0930\u0940 - (\u0643\u0634\u0645\u064a\u0631\u064a)",
	"ksh" : "Ripoarisch",
	"ku" : "Kurd\u00ee \/ \u0643\u0648\u0631\u062f\u06cc",
	"ku-latn" : "\u202aKurd\u00ee (lat\u00een\u00ee)\u202c",
	"ku-arab" : "\u202b\u0643\u0648\u0631\u062f\u064a (\u0639\u06d5\u0631\u06d5\u0628\u06cc)\u202c",
	"kv" : "\u041a\u043e\u043c\u0438",
	"kw" : "Kernowek",
	"ky" : "\u041a\u044b\u0440\u0433\u044b\u0437\u0447\u0430",
	"la" : "Latina",
	"lad" : "Ladino",
	"lb" : "L\u00ebtzebuergesch",
	"lbe" : "\u041b\u0430\u043a\u043a\u0443",
	"lez" : "\u041b\u0435\u0437\u0433\u0438",
	"lfn" : "Lingua Franca Nova",
	"lg" : "Luganda",
	"li" : "Limburgs",
	"lij" : "L\u00edguru",
	"lmo" : "Lumbaart",
	"ln" : "Ling\u00e1la",
	"lo" : "\u0ea5\u0eb2\u0ea7",
	"loz" : "Silozi",
	"lt" : "Lietuvi\u0173",
	"lv" : "Latvie\u0161u",
	"lzh" : "\u6587\u8a00",
	"mai" : "\u092e\u0948\u0925\u093f\u0932\u0940",
	"map-bms" : "Basa Banyumasan",
	"mdf" : "\u041c\u043e\u043a\u0448\u0435\u043d\u044c",
	"mg" : "Malagasy",
	"mh" : "Ebon",
	"mhr" : "\u041e\u043b\u044b\u043a \u041c\u0430\u0440\u0438\u0439",
	"mi" : "M\u0101ori",
	"mk" : "\u041c\u0430\u043a\u0435\u0434\u043e\u043d\u0441\u043a\u0438",
	"ml" : "\u0d2e\u0d32\u0d2f\u0d3e\u0d33\u0d02",
	"mn" : "\u041c\u043e\u043d\u0433\u043e\u043b",
	"mo" : "\u041c\u043e\u043b\u0434\u043e\u0432\u0435\u043d\u044f\u0441\u043a\u044d",
	"mr" : "\u092e\u0930\u093e\u0920\u0940",
	"ms" : "Bahasa Melayu",
	"mt" : "Malti",
	"mus" : "Mvskoke",
	"mwl" : "Mirand\u00e9s",
	"my" : "\u1019\u103c\u1014\u103a\u1019\u102c\u1018\u102c\u101e\u102c",
	"myv" : "\u042d\u0440\u0437\u044f\u043d\u044c",
	"mzn" : "\u0645\u064e\u0632\u0650\u0631\u0648\u0646\u064a",
	"na" : "Dorerin Naoero",
	"nah" : "N\u0101huatl",
	"nan" : "B\u00e2n-l\u00e2m-g\u00fa",
	"nap" : "Nnapulitano",
	"nb" : "\u202aNorsk (bokm\u00e5l)\u202c",
	"nds" : "Plattd\u00fc\u00fctsch",
	"nds-nl" : "Nedersaksisch",
	"ne" : "\u0928\u0947\u092a\u093e\u0932\u0940",
	"new" : "\u0928\u0947\u092a\u093e\u0932 \u092d\u093e\u0937\u093e",
	"ng" : "Oshiwambo",
	"niu" : "Niu\u0113",
	"nl" : "Nederlands",
	"nn" : "\u202aNorsk (nynorsk)\u202c",
	"no" : "\u202aNorsk (bokm\u00e5l)\u202c",
	"nov" : "Novial",
	"nrm" : "Nouormand",
	"nso" : "Sesotho sa Leboa",
	"nv" : "Din\u00e9 bizaad",
	"ny" : "Chi-Chewa",
	"oc" : "Occitan",
	"om" : "Oromoo",
	"or" : "\u0b13\u0b21\u0b3c\u0b3f\u0b06",
	"os" : "\u0418\u0440\u043e\u043d\u0430\u0443",
	"pa" : "\u0a2a\u0a70\u0a1c\u0a3e\u0a2c\u0a40",
	"pag" : "Pangasinan",
	"pam" : "Kapampangan",
	"pap" : "Papiamentu",
	"pcd" : "Picard",
	"pdc" : "Deitsch",
	"pdt" : "Plautdietsch",
	"pfl" : "Pf\u00e4lzisch",
	"pi" : "\u092a\u093e\u093f\u0934",
	"pih" : "Norfuk \/ Pitkern",
	"pl" : "Polski",
	"pms" : "Piemont\u00e8is",
	"pnb" : "\u067e\u0646\u062c\u0627\u0628\u06cc",
	"pnt" : "\u03a0\u03bf\u03bd\u03c4\u03b9\u03b1\u03ba\u03ac",
	"ps" : "\u067e\u069a\u062a\u0648",
	"pt" : "Portugu\u00eas",
	"pt-br" : "Portugu\u00eas do Brasil",
	"qu" : "Runa Simi",
	"rif" : "Tarifit",
	"rm" : "Rumantsch",
	"rmy" : "Romani",
	"rn" : "Kirundi",
	"ro" : "Rom\u00e2n\u0103",
	"roa-rup" : "Arm\u00e3neashce",
	"roa-tara" : "Tarand\u00edne",
	"ru" : "\u0420\u0443\u0441\u0441\u043a\u0438\u0439",
	"ruq" : "Vl\u0103he\u015fte",
	"ruq-cyrl" : "\u0412\u043b\u0430\u0445\u0435\u0441\u0442\u0435",
	"ruq-latn" : "Vl\u0103he\u015fte",
	"rw" : "Kinyarwanda",
	"sa" : "\u0938\u0902\u0938\u094d\u0915\u0943\u0924",
	"sah" : "\u0421\u0430\u0445\u0430 \u0442\u044b\u043b\u0430",
	"sc" : "Sardu",
	"scn" : "Sicilianu",
	"sco" : "Scots",
	"sd" : "\u0633\u0646\u068c\u064a",
	"sdc" : "Sassaresu",
	"se" : "S\u00e1megiella",
	"sei" : "Cmique Itom",
	"sg" : "S\u00e4ng\u00f6",
	"sh" : "Srpskohrvatski \/ \u0421\u0440\u043f\u0441\u043a\u043e\u0445\u0440\u0432\u0430\u0442\u0441\u043a\u0438",
	"shi" : "Ta\u0161l\u1e25iyt",
	"si" : "\u0dc3\u0dd2\u0d82\u0dc4\u0dbd",
	"simple" : "Simple English",
	"sk" : "Sloven\u010dina",
	"sl" : "Sloven\u0161\u010dina",
	"sli" : "Schl\u00e4sch",
	"sm" : "Gagana Samoa",
	"sma" : "\u00c5arjelsaemien",
	"sn" : "chiShona",
	"so" : "Soomaaliga",
	"sq" : "Shqip",
	"sr" : "\u0421\u0440\u043f\u0441\u043a\u0438 \/ Srpski",
	"sr-ec" : "\u0421\u0440\u043f\u0441\u043a\u0438 (\u045b\u0438\u0440\u0438\u043b\u0438\u0446\u0430)",
	"sr-el" : "Srpski (latinica)",
	"srn" : "Sranantongo",
	"ss" : "SiSwati",
	"st" : "Sesotho",
	"stq" : "Seeltersk",
	"su" : "Basa Sunda",
	"sv" : "Svenska",
	"sw" : "Kiswahili",
	"szl" : "\u015al\u016fnski",
	"ta" : "\u0ba4\u0bae\u0bbf\u0bb4\u0bcd",
	"tcy" : "\u0ca4\u0cc1\u0cb3\u0cc1",
	"te" : "\u0c24\u0c46\u0c32\u0c41\u0c17\u0c41",
	"tet" : "Tetun",
	"tg" : "\u0422\u043e\u04b7\u0438\u043a\u04e3",
	"tg-cyrl" : "\u0422\u043e\u04b7\u0438\u043a\u04e3",
	"tg-latn" : "tojik\u012b",
	"th" : "\u0e44\u0e17\u0e22",
	"ti" : "\u1275\u130d\u122d\u129b",
	"tk" : "T\u00fcrkmen\u00e7e",
	"tl" : "Tagalog",
	"tn" : "Setswana",
	"to" : "lea faka-Tonga",
	"tokipona" : "Toki Pona",
	"tp" : "Toki Pona (deprecated:tokipona)",
	"tpi" : "Tok Pisin",
	"tr" : "T\u00fcrk\u00e7e",
	"ts" : "Xitsonga",
	"tt" : "\u0422\u0430\u0442\u0430\u0440\u0447\u0430\/Tatar\u00e7a",
	"tt-cyrl" : "\u0422\u0430\u0442\u0430\u0440\u0447\u0430",
	"tt-latn" : "Tatar\u00e7a",
	"tum" : "chiTumbuka",
	"tw" : "Twi",
	"ty" : "Reo M\u0101`ohi",
	"tyv" : "\u0422\u044b\u0432\u0430 \u0434\u044b\u043b",
	"udm" : "\u0423\u0434\u043c\u0443\u0440\u0442",
	"ug" : "Uyghurche\u200e \/ \u0626\u06c7\u064a\u063a\u06c7\u0631\u0686\u06d5",
	"ug-arab" : "\u0626\u06c7\u064a\u063a\u06c7\u0631\u0686\u06d5",
	"ug-latn" : "Uyghurche\u200e",
	"uk" : "\u0423\u043a\u0440\u0430\u0457\u043d\u0441\u044c\u043a\u0430",
	"ur" : "\u0627\u0631\u062f\u0648",
	"uz" : "O'zbek",
	"ve" : "Tshivenda",
	"vec" : "V\u00e8neto",
	"vep" : "Vepsan kel'",
	"vi" : "Ti\u1ebfng Vi\u1ec7t",
	"vls" : "West-Vlams",
	"vo" : "Volap\u00fck",
	"vro" : "V\u00f5ro",
	"wa" : "Walon",
	"war" : "Winaray",
	"wo" : "Wolof",
	"wuu" : "\u5434\u8bed",
	"xal" : "\u0425\u0430\u043b\u044c\u043c\u0433",
	"xh" : "isiXhosa",
	"xmf" : "\u10db\u10d0\u10e0\u10d2\u10d0\u10da\u10e3\u10e0\u10d8",
	"yi" : "\u05d9\u05d9\u05b4\u05d3\u05d9\u05e9",
	"yo" : "Yor\u00f9b\u00e1",
	"yue" : "\u7cb5\u8a9e",
	"za" : "Vahcuengh",
	"zea" : "Ze\u00eauws",
	"zh" : "\u4e2d\u6587",
	"zh-classical" : "\u6587\u8a00",
	"zh-cn" : "\u202a\u4e2d\u6587(\u4e2d\u56fd\u5927\u9646)\u202c",
	"zh-hans" : "\u202a\u4e2d\u6587(\u7b80\u4f53)\u202c",
	"zh-hant" : "\u202a\u4e2d\u6587(\u7e41\u9ad4)\u202c",
	"zh-hk" : "\u202a\u4e2d\u6587(\u9999\u6e2f)\u202c",
	"zh-min-nan" : "B\u00e2n-l\u00e2m-g\u00fa",
	"zh-mo" : "\u202a\u4e2d\u6587(\u6fb3\u9580)\u202c",
	"zh-my" : "\u202a\u4e2d\u6587(\u9a6c\u6765\u897f\u4e9a)\u202c",
	"zh-sg" : "\u202a\u4e2d\u6587(\u65b0\u52a0\u5761)\u202c",
	"zh-tw" : "\u202a\u4e2d\u6587(\u53f0\u7063)\u202c",
	"zh-yue" : "\u7cb5\u8a9e",
	"zu" : "isiZulu"
}