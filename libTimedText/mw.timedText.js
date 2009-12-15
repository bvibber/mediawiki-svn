/*
 * The Core timed Text interface object
 * 
 * handles class mappings for: 
 * 	menu display ( jquery.ui themeable )
 * 	timed text loading request
 *  timed text edit requests
 * 	timed text search & seek interface ( version 2 ) 
 */ 

mw.addMessages( {
	"mwe-back-btn" : "Back",	
	"mwe-chose-text" : "Chose text",
	"mwe-layout" : "Layout", 
	"mwe-ontop-video" : "Ontop of video",
	"mwe-bellow-video": "Bellow video", 
	"mwe-video-off" : "Hide subtitles"
	
} );

// Bind to mw (for uncluttered global namespace) 
( function( $ ) {	
	$.timedText = function ( embedPlayer, options ){
		return new TimedText( embedPlayer, options );
	}
	
	/** 
	 * Timed Text Object
	 * @param embedPlayer Host player for timedText interfaces 
	 */
	TimedText = function( embedPlayer, options ) {
		return this.init( embedPlayer, options);
	}
	TimedText.prototype = {
		
		/** 
		* Prefrences config order is: 
		* 1) user cookie
		* 2) media default or layout tags
		* 3) defaults provided in the config var: 
		*/
		config : {
			// Layout for basic "timedText" type can be 'ontop', 'off', 'below'
			'layout': 'below'			
		},
	
		/**
		* Set of timedText providers
		*/
		timedTextProvider:{
			'commons':{
				'api_url': mw.commons_api_url
			}				
		},		
			
		/**
		 * @constructor
		 * @param {Object} embedPlayer Host player for timedText interfaces 
		 * 
		 */
		init: function( embedPlayer, options ){
			var _this = this;
			this.embedPlayer = embedPlayer;	
			this.options = $j.extend( {
				'targetContainer' : null
			}, options )
			
			//Set up embedPlayer monitor hook: 
			embedPlayer.addHook( 'monitor', function(){
				_this.monitor();
			})			
			
			// Load cookie prefrences
			
		},
		
		/**
		* Monitor video time and update timed text filed[s]  
		*/ 
		monitor: function(){
			mw.log(' timedText monitor ');
		},
		
		/**
		 * Show the timed text menu
		 * @param {Object} jQuery selector to display the target
		 */
		showMenu: function( ){
			var _this = this;
			mw.log("TimedText:ShowMenu");
			
			// Build out menu with bindings ( with jquery calls )				
			var menuOptions = {
				'content'	: this.buildMenu(),
				'crumbDefaultText' : ' ',
				'targetContainer' : this.options.targetContainer,
				'autoShow' : true,
				'backLinkText' : gM( 'mwe-back-btn' ),
				'selectItemCallback' : function( item ){
					_this.selectMenuItem( item );
				}				
			};
			var $menuButton = $j('#' + this.embedPlayer.id + ' .timed-text');				
			$menuButton.unbind().menu( 
				menuOptions 
			);
			 
		},
		
		/**
		* Selection of a meun item
		* 
		* @param {Element} item Item selected
		*/
		selectMenuItem: function( item ){
			mw.log("selectMenuItem: " + $j( item ).attr('class') );
		},	
		
		/**
		* Builds the core timed Text menu
		* calls a few sub-functions:		
		* Basic menu layout:
		*		Chose Language
		*			All Subtiles here (if we have categories list them ) 
		*		Layout	
		*			Bellow video
		*			Ontop video ( only available to supported plugins )									
		*		[ Search Text ]
		*		[ Chapters ] seek to chapter
		*		 
		*/
		buildMenu: function(){
			var _this = this; 
			//build the source list menu item: 						
			
			return $j( '<ul>' ).append(			
				// Chose language option:  							
				_this.getLi( 'mwe-chose-text' ).append(
					_this.buildLanguageMenu()
				),
				// Layout Menu option
				_this.getLi( 'mwe-layout' ).append(
					$j('<ul>').append(
						_this.getLi( 'mwe-bellow-video' ),
						_this.getLi( 'mwe-ontop-video' ),
						_this.getLi( 'mwe-video-off' )	
					)
				)			
			);					
		},
		
		/**
		* Utility function to assist in menu build out:
		* Get menu line item (li) html:  <li><a> msgKey </a></li> 
		*
		* @param {String} msgKey Msg key for menu item
		*/ 
		getLi: function  ( msgKey ){
			return $j( '<li>' ).append(		
				$j('<a>')
				.attr('href', '#')
				.text( 
					gM( msgKey )
				)
			);	
		},
		
		/**
		* Builds the language source list menu
		* checks all text sources for category and language key attribute
		*/
		buildLanguageMenu: function(){
			var _this = this;
			// Get local reference to all timed text sources: ( text/xml, text/x-srt etc )			
			var sources = this.embedPlayer.mediaElement.getSources( 'text' );			
			
			// See if we have categories to worry about:
			var categoryBuckets = [ ]; 
			for( var i in sources ) {
				var source = sources[ i ];
				if( source.category ){
					if( ! categoryBuckets[  source.category ] )
						 categoryBuckets[  source.category ] = [ ];
					 categoryBuckets[  source.category ].push( source )
				}
			}
			$langMenu = $j('<ul>');
			if( categoryBuckets.length == 0 ){
				// No categories just return the source list			
				for( var i in sources ){
					var source = sources [ i ] 
					$langMenu.append(
						$j( '<li>' ).append(		
							$j('<a>')
							.attr('href', '#')
							.text( 
								source.title
							) )  
					);
				}
			}
			return $langMenu; 
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
	$.fn.timedText = function ( options ){		
		$j( this.selector ).each(function(){
			var embedPlayer = $j(this).get(0);			
			// Setup timed text for the given player: 
			if( ! embedPlayer.timedText )
				embedPlayer.timedText = new mw.timedText( embedPlayer, options);			
			
			// Run the default "showMenu" action:
			embedPlayer.timedText.showMenu();
		} );		
	}
} )( jQuery );