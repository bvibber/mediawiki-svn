/*
* skin js allows you to override contrlBuilder html/class output
*/

mw.addMessages( {
	"mwe-credit-title" : "Title: $1",
	"mwe-kaltura-platform-title" : "Kaltura open source video platform"
} );

var kskinConfig = {
	// The parent class for all kskin css: 
	playerClass: 'k-player',
	
	// Display time string length
	long_time_disp: false,
	
	// Options are handled internally
	external_options: false,
	
	// Volume control layout is horizontal
	volume_layout: 'horizontal',
	
	// Menu items for the kskin: 
	menu_items:[
		'playerSelect',
		'download',
		'share',
		'credits',
	],
	
	// Extends base components with kskin specific options:
	components: {		
		'play-btn-large' : {
			'h' : 55
		},		
		'options': {
			'w':50,
			'o':function() {
				return '<div class="ui-state-default ui-corner-bl rButton k-options" title="' + gM( 'mwe-player_options' ) + '" >' +
							'<span>' + gM( 'mwe-menu_btn' ) + '</span>' +
						'</div>'
			}
		},		
		'time_display': {
			'w':70
		},		
		/*
		* The playhead html
		*/
		'play_head': {
			'w':0, // special case (takes up remaining space)
			'o':function( ctrlObj ) {
				return '<div class="play_head" style="width: ' + ( ctrlObj.available_width - 25 ) + 'px;"></div>';
			}
		},
		'options_menu': {
			'w':0,
			'o':function( ctrlObj ) {
				var embedPlayer = ctrlObj.embedPlayer;
				
				// Setup menu offset ( if player height <  getOverlayHeight )				
				var menuOffset = ( embedPlayer.getPlayerHeight() <  ctrlObj.getOverlayHeight() ) ? 
					'top:' + ( embedPlayer.getPlayerHeight() + ctrlObj.getControlBarHeight() ) + 'px;'  : '';
														
				// Special common overflow hack: 
				// NOTE: should re-factor to just append menu to top body when it does not "fit" in the player
				if(   menuOffset != '' )
					$j( embedPlayer ).parents( '.thumbinner' ).css( 'overflow', 'visible' );				
														
				var o = '' +
				'<div id="blackbg_' + embedPlayer.id +'" class="k-menu ui-widget-content" ' +
					'style="width:' + ctrlObj.getOverlayWidth() + 'px; height:' +  ctrlObj.getOverlayHeight() + 'px;' + menuOffset + '">' +
						'<ul class="k-menu-bar">';
							// Output menu item containers: 
							for ( i = 0; i < ctrlObj.menu_items.length; i++ ) {
								var mk = ctrlObj.menu_items[i];
								o += '<li class="k-' + mk + '-btn" rel="' + mk + '">' +
										'<a href="#" title="' + gM( 'mwe-' + mk ) + '">' + gM( 'mwe-' + mk ) + '</a></li>';
							}
						o += '</ul>' +
						// We have to subtract the width of the k-menu-bar
						'<div class="k-menu-screens" style="width:' + (  ctrlObj.getOverlayWidth() - 75 ) +
							'px; height:' + ( ctrlObj.getOverlayHeight() - ctrlObj.getControlBarHeight() ) + 'px;">';
						
						// Output menu item containers: 
						for ( i = 0; i < ctrlObj.menu_items.length; i++ ) {
							o += '<div class="menu-screen menu-' + ctrlObj.menu_items[i] + '"></div>';
						}
						'</div>' +
					'</div>';
				return o;
			}
		}
	},
	
	/**
	* Get minimal width for interface overlay
	*/
	getOverlayWidth: function(){
		return ( this.embedPlayer.getPlayerWidth() < 200 )? 200 : this.embedPlayer.getPlayerWidth();
	},	
	
	/**
	* Get minimal height for interface overlay
	*/
	getOverlayHeight: function(){
		return ( this.embedPlayer.getPlayerHeight() < 160 )? 160 : this.embedPlayer.getPlayerHeight();
	},
	
	/**
	* Adds the skin Control Bindings
	*/
	addSkinControlBindings: function() {
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		
		// Set up control bar pointer
		var $playerTarget = embedPlayer.$interface;
		 		
   		// Options menu display:			
   		$playerTarget.find( '.k-options' )
   		.unbind()
   		.click( function() {     					 	
			if ( $playerTarget.find( '.k-menu' ).length == 0 ) {							
	   			// Stop the player if it does not support overlays:
				if ( !embedPlayer.supports['overlays'] ){				
					embedPlayer.stop();
				}
				// Add the menu binding        				
				_this.addMeunBinding();
			}
	   		// Set up the text and menu:       			 					
			var $ktxt = $j( this );
			var $kmenu = $playerTarget.find( '.k-menu' );
			if ( $kmenu.is( ':visible' ) ) {
				$kmenu.fadeOut( "fast", function() {
					$ktxt.find( 'span' ).html ( gM( 'mwe-menu_btn' ) );
				} );
				$playerTarget.find( '.play-btn-large' ).fadeIn( 'fast' );
			} else {
				$kmenu.fadeIn( "fast", function() {
					$ktxt.find( 'span' ).html ( gM( 'mwe-close_btn' ) );
				} );
				$playerTarget.find( '.play-btn-large' ).fadeOut( 'fast' );
			}
		} );				
		
	},
	
	/**
	* Adds binding for the options menu
	*
	* @param {Object} $tp Target video container for 
	*/
	addMeunBinding: function() {
		var _this = this;
		var embedPlayer = this.embedPlayer;
		// Set local player target pointer:
		var $playerTarget = embedPlayer.$interface;
		
		// Check if k-menu already exists:
		if ( $playerTarget.find( '.k-menu' ).length != 0 )
			return false;
		
		// Add options menu to top of player target children: 
		$playerTarget.prepend(
			_this.components[ 'options_menu' ].o( _this )
		);		
		
		
		// By default its hidden:
  		$playerTarget.find( '.k-menu' ).hide();
  			
  		// Add menu-items bindings:  
  		for ( i = 0; i < _this.menu_items.length ; i++ ) {
	        $playerTarget.find( '.k-' +  _this.menu_items[i] + '-btn' ).click( function() {	        	
	        	var mk = $j( this ).attr( 'rel' );
	        	// hide menu items	        	
	        	$targetItem = $playerTarget.find( '.menu-' + mk );	
				// call the function showMenuItem
				_this.showMenuItem(	mk );        									        	
	        	// Hide the others 
	        	$playerTarget.find( '.menu-screen' ).hide();
	        	
	        	// Show the target menu item:
	        	$targetItem.fadeIn( "fast" );
	        	 
				// Don't follow the # link								
	            return false;
			} );
  		}
	}, 
	
	/**
	* Shows a selected menu_item
	* 
	* @param {String} menu_itme Menu item key to display
	*/
	showMenuItem:function( menu_item ) {
		var embedPlayer = this.embedPlayer;
		//handle special k-skin specific display; 
		if( menu_item == 'credits'){
			this.showCredits(); 
		}else{
			// Call the base embedPlayer "show{Item}"
			this.embedPlayer['show' + menu_item.charAt( 0 ).toUpperCase() + menu_item.substring( 1 )](
				embedPlayer.$interface.find( '.menu-' + menu_item )
			);
		}
	},	
	
	/**
	* Show the "edit with kaltura" screen (  specific to kaltura skin )
	*/
	showKalturaEdit: function(){
		
	},	
	
	/**
	* Show the credit screen (presently specific to kaltura skin )
	*/  
	showCredits: function() {
		//set up the shortcuts:	
		embedPlayer = this.embedPlayer;
		var _this = this;	
		$target = embedPlayer.$interface.find( '.menu-credits' );

		$target.html( '<h2>' + gM( 'mwe-credits' ) + '</h2>'  +
			'<div class="credits_box ui-corner-all">' +
				mw.loading_spinner() + 
			'</div>'								
		);

		if( mw.getConfig( 'k_attribution' ) == true ){
			$target.append( 
				$j('<div/>').addClass( 'k-attribution' )
				.attr({
					'title': gM('mwe-kaltura-platform-title')
				})
				.click( function(){
					window.location = 'http://kaltura.com';
				})
			);
		}
		
		if( !embedPlayer.apiTitleKey ){
			$target.find('.credits_box').text(
				'Error: no title key to grab credits with' 
			);
			return ;
		}
				
		// Do the api request to populate the credits via the apiTitleKey 
		var request = {
			// Normalize the File NS (ie sometimes its present in apiTitleKey other times not
			'titles' : 'File:' + embedPlayer.apiTitleKey.replace(/File:|Image:/, '' ),
		    'prop' : 'revisions',
		    'rvprop' : 'content'
		};		
		var req_categories = new Array();
		var api_url = mw.getApiProviderURL( embedPlayer.apiProvider );
		if( ! api_url ){
			mw.log("Error: can't get credit screen without title key");
			return ;
		}
	    mw.getJSON( api_url , request, function( data ) {
			if( !data || !data.query || !data.query.pages ){
				$target.find('.credits_box').text(
					'Error: title key: ' + embedPlayer.apiTitleKey + ' not found' 
				);
				return false;
			}
			var pages = data.query.pages;			
			for(var i in pages){
				page = pages[ i ];
				if( page[ 'revisions' ] && page[ 'revisions' ][0]['*'] ){
					$target.find('.credits_box').html(
						_this.doCreditLineFromWikiText( page[ 'revisions' ][0]['*'] )
					);
				}
			}
	    } );
	},
	
	/**
	* Build a clip credit from the resource wikiText page
	*
	* NOTE: in the future this should parse the resource page template
	* 
	* @parm {String} wikiText Resource wiki text page contents
	*/
	doCreditLineFromWikiText: function ( wikiText ){
		var embedPlayer = this.embedPlayer;
		
		// Get the title str 
		var titleStr = embedPlayer.apiTitleKey.replace(/_/g, ' ');
		var titleLink = 'http://commons.wikimedia.org/wiki/File:' + embedPlayer.apiTitleKey;
		
		var imgWidth = ( this.getOverlayWidth() < 250 )? 45 : 90;
		
		// @@FIXME Do a quick check for source line:
		return $j( '<div/>' ).addClass( 'creditline' )
			.append(
				$j('<a/>').attr({
					'href' : titleLink,
					'title' :  titleStr
				}).html( 
					$j('<img/>').attr( {
						'border': 0, 
						'src' : embedPlayer.thumbnail						
					} ).css( {
						'width' : imgWidth,
						'height': parseInt( imgWidth * ( embedPlayer.height / embedPlayer.width ) )
					} )
				)
			)
			.append(			
				$j('<span>').html( 
					gM( 'mwe-credit-title' ,  
						// We use a div container to easialy get at the built out link
						$j('<div>').html( 
							$j('<a/>').attr({
								'href' : titleLink,
								'title' :  titleStr
							}).text( titleStr )
						).html()
					)
				)
			);
	}
};
