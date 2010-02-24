/**
* Msg text is inherited from embedPlayer 
*/

/**
* ctrlBuilder object
*	@param the embedPlayer element we are targeting
*/
var ctrlBuilder = function( embedPlayer, options ) {
	return this.init( embedPlayer, options );
};

/**
 * controlsBuilder prototype:
 */
ctrlBuilder.prototype = {
	//Default Local values: 
	
	// Parent css Class name
	playerClass : 'mv-player',
	
	// Long string display of time value
	longTimeDisp: true,
	
	// If the options menu outside of player
	external_options : true,
	
	// Default volume layout is "vertical"
	volume_layout : 'vertical',
	
	// Default control bar height is 33
	height: 33,		
	
	// Default supported components is merged with embedPlayer set of supported types
	supportedComponets: {
	
		// All playback types support options	  
		'options': true			  		
	},	
	
	// Default supported menu items is merged with skin menu items
	supportedMenuItems: {
		'playerSelect' : true, 
		
		// Download the file menu
		'download' : true, 
		
		// Share the video menu
		'share' : true
	},	
	
	// Flag to store the current fullscreen mode
	fullscreenMode: false,
	
	/**
	* Initialization Object for the control builder
	*
	* @param {Object} embedPlayer EmbedPlayer interface
	*/ 
	init: function( embedPlayer ) {
		var _this = this;
		this.embedPlayer = embedPlayer;

		// Check for skin overrides for ctrlBuilder
		if ( window[ embedPlayer.skinName + 'Config' ] ) {
		
			// Clone as to not override prototype with the skin config
			var _this = $j.extend( true, { }, this, window[ embedPlayer.skinName + 'Config'] );
			
			return _this;
		}
		// Return the ctrlBuilder Object: 
		return this;
	},
	
	/**
	* Get the controls html
	* @return {String} html output of controls
	*/
	addControls: function() {
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		
		// Set up local ctrlBuilder
		var _this = this;

		// Remove any old controls: 
		embedPlayer.$interface.find( '.control-bar' ).remove();
			
		// Add some space to control_wrap for the control bar:
		embedPlayer.$interface.css( {
			'height' : parseInt( embedPlayer.height ) + parseInt( this.height)
		} );
		embedPlayer.$interface.append(
			$j('<div>')
			.addClass( 'ui-state-default ui-widget-header ui-helper-clearfix control-bar' )
			.css('display', 'block')
		);		
	
		//Add the Controls with their bindings
		this.addControlComponents();
	
		// Add hooks once Controls are in DOM
		this.addControlHooks();
	},
	
	/**
	* Builds the interface controls
	* @returns the interface html string
	*/ 
	addControlComponents: function( ) {
		var _this = this;			
		mw.log( 'f:controlsBuilder:: opt:' + this.options );
		
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		
		//Set up local var to control container:
		var $controlBar = embedPlayer.$interface.find( '.control-bar' );  
		
		this.available_width = embedPlayer.getPlayerWidth();
		
		// Make pointer to the embedPlayer
		this.embedPlayer = embedPlayer;
				
		// Build the supportedComponets list
		this.supportedComponets = $j.extend( this.supportedComponets, embedPlayer.supports );
		
		// Check for timed text support:
		if( embedPlayer.isTimedTextSupported() ){
			this.supportedComponets['timedText'] = true;
		}		
		// Check for kalturaAttribution 	
		if( mw.getConfig( 'kalturaAttribution' ) ){							 
			this.supportedComponets[ 'kalturaAttribution' ] = true;
		}
		
				
		// Output components 
		for ( var component_id in this.components ) {
		
			// Check for (component === false ) and skip  
			if( this.components[ component_id ] === false  ){
				continue;
			}
			
			// Special case with playhead skip if we have > 30px of space for it
			if ( component_id == 'playHead' && this.available_width < 30 ){
				continue;
			}			
			
			// For now skip "fullscreen" for "audio" assets or where height is 0px
			if(  component_id == 'fullscreen' && this.embedPlayer.height == 0 ){
				continue;
			}		
			  
			// Make sure the given components is supported:
			if ( this.supportedComponets[ component_id ] ) {
				if ( this.available_width > this.components[ component_id ].w ) {											
					// Append the component
					$controlBar.append( 
						_this.getComponent( component_id ) 
					);
					
					this.available_width -= this.components[ component_id ].w;
				} else {
					mw.log( 'Not enough space for control component:' + component_id );
				}
			}
		}
	},
	/**
	 * Toggles full screen by calling 
	 *  doFullScreenPlayer to enable fullscreen mode
	 *  restoreWindowPlayer to restor window mode
	 */
	toggleFullscreen: function(){
		if( this.fullscreenMode ){
			this.restoreWindowPlayer();
			this.fullscreenMode = false;
		}else{
			this.doFullScreenPlayer();
			this.fullscreenMode = true;
		}
	},
	
	/**
	* Do full-screen mode 
	*/ 
	doFullScreenPlayer: function(){
		mw.log(" ctrlBuilder :: toggle full-screen ");
					
		// Setup pointer to control builder :
		var _this = this;
		
		// Setup loadl refrence to embed player: 
		var embedPlayer = this.embedPlayer;
		
		// Setup a local refrence to the player interface: 
		var $interface = embedPlayer.$interface;
		
		// Add the black overlay: 		
		$j( '<div />' )
		.addClass( 'mw-fullscreen-overlay' )
		// Set some arbitrary high z-index
		.css('z-index', mw.getConfig( 'fullScreenIndex' ) ) 
		.appendTo('body')
		.hide()
		.fadeIn("slow");
		
		// Setup target height width based on max window size	
		var fullWidth = $j(window).width() - 5 ;
		var fullHeight =  $j(window).height() -5;
		
		// Set target width
		targetWidth = fullWidth;
		targetHeight = targetWidth * ( embedPlayer.height / embedPlayer.width  )
		// Check if it exted the height constrait: 
		if( targetHeight >  fullHeight ){		
			targetHeight = fullHeight;				
			targetWidth = targetHeight * ( embedPlayer.width  / embedPlayer.height  );
		}		
		
		// Change the interface to absolute positioned: 
		this.windowPositionStyle = $interface.css( 'position' );
		$interface.css( {
			'position' : 'absolute',
			'z-index' : mw.getConfig( 'fullScreenIndex' ) + 1 
		} );		
	
		// Get the base offset: 
		this.windowOffset = $interface.offset();
		var topOffset = '0px';
		var leftOffset = '0px';
		//Check if we have an offsetParent
		if( $interface.offsetParent().length ){
			topOffset = -this.windowOffset.top + 'px';
			leftOffset = -this.windowOffset.left + 'px';
		}
		$interface.animate( {			
			'top' : topOffset,
			'left' : leftOffset,
			'width' : '99%',
			'height' : '99%',
			'z-index' : '999999'
		} )
		
		// Set the player height width: 
		$j( this.embedPlayer ).css( {
			'position' : 'relative',
			'z-index' : '999999'
		} )		
		// Animate a zoom ( while keeping aspect )
		.animate( {
			'top' : '0px',
			'left' : '0px',
			'width' : targetWidth,
			'height' : targetHeight 
		} )		
		
		/*
		-moz-transform:scale(1.97833) translate(-5px, 4px);
		-moz-transform-origin:50.0852% 45.6621%;
		left:0;
		position:relative;
		top:0;
		*/ 
				
		// bind hide controls when mouse is not active
		
		// bind resize reize window to resize window
		
		// bind escape to restore clip resolution
		$j( window ).keyup( function(event) {
			// Escape check
			if( event.keyCode == 27 ){
				_this.restoreWindowPlayer();
			}
		} );
		 
	},
	restoreWindowPlayer: function(){
		var _this = this;
		var embedPlayer = this.embedPlayer;
		if( this.fullscreenMode == false ){
			return ;	
		}
		// Set fullscreen mode to false
		this.fullscreenMode = false;
		
		var $interface = embedPlayer.$interface;
		$j('.mw-fullscreen-overlay').fadeOut( 'slow' );
		$interface.animate( {
			'top' : this.windowOffset.top,
			'left' : this.windowOffset.left,
			// height is embedPlayer height + ctrlBuilder height: 
			'height': embedPlayer.height + _this.height,
			'width' : embedPlayer.width					
		},function(){
			$interface.css( {
				'position' : _this.windowPositionStyle,
				'top' : null,
				'left' : null 
			} );
		} );
		// resize the player: 
		$j( embedPlayer ).animate( {
			'width' : embedPlayer.width,
			'height' : embedPlayer.height
		})
	},
	
	/**
	* Get minimal width for interface overlay
	*/
	getOverlayWidth: function( ) {
		return ( this.embedPlayer.getPlayerWidth() < 300 )? 300 : this.embedPlayer.getPlayerWidth();
	},	
	
	/**
	* Get minimal height for interface overlay
	*/
	getOverlayHeight: function( ) {
		return ( this.embedPlayer.getPlayerHeight() < 200 )? 200 : this.embedPlayer.getPlayerHeight();
	},
	
	/**
	* Get the control bar height: 
	*/
	getControlBarHeight: function( ) {
		return this.height;
	},
	
	/**
	* addControlHooks
	* Adds control hooks once controls are in the DOM
	*/
	addControlHooks: function( ) {
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		
		// Setup target shortcut to	control-bar
		$target = embedPlayer.$interface;

		// Add recommend firefox if we have non-native playback:
		if ( _this.checkNativeWarning( ) ) {
			_this.doNativeWarning();
		}
		// Do png fix for ie6
		if ( $j.browser.msie  &&  $j.browser.version <= 6 ) {			
			$j('#' + embedPlayer.id + ' .play-btn-large' ).pngFix();
		}
		
		this.doVolumeBinding();
		
		// Check if we have any custom skin Bindings to run
		if ( this.addSkinControlBindings && typeof( this.addSkinControlBindings ) == 'function' ){
			this.addSkinControlBindings();
		}
	},
	
	
	/**
	* Check if a warning should be issued to non-native playback systems 
	*
	* dependent on mediaElement being setup 
	*/ 
	checkNativeWarning: function( ) {		
		// Check cookie to see if user requested to hide it
		if ( $j.cookie( 'show_player_warning' ) == 'false' ) {
			return false;
		}		
		
		// If the resolution is too small don't display the warning
		if( this.embedPlayer.getPlayerHeight() < 199 ){
			return false;
		}

		// See if we have native support for ogg: 
		var supporting_players = mw.EmbedTypes.players.getMIMETypePlayers( 'video/ogg' );
		for ( var i = 0; i < supporting_players.length; i++ ) {
			if ( supporting_players[i].id == 'oggNative' ) {
				return false;
			}
		}
		
		// See if we are using mwEmbed without a ogg source in which case no point in promoting firefox :P			
		if (  this.embedPlayer.mediaElement &&  this.embedPlayer.mediaElement.sources ) {
			var foundOgg = false;
			var playable_sources =  this.embedPlayer.mediaElement.getPlayableSources();
			for ( var sInx = 0; sInx < playable_sources.length; sInx++ ) {
				var mime_type = playable_sources[sInx].mime_type;
				if ( mime_type == 'video/ogg' ) {
					foundOgg = true;
				}
			}
			// No ogg src... no point in download firefox link
			if ( !foundOgg ){
				return false;
			}
		}
		return true;
	},
	
	/**
	* Does a native warning check binding to the player on mouse over. 
	*/
	doNativeWarning: function( ) {
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		
		$j( embedPlayer ).hover(
			function() {					
				if ( $j( '#gnp_' + embedPlayer.id ).length == 0 ) {
					var toppos = ( embedPlayer.instanceOf == 'mvPlayList' ) ? 25 : 10;
					
					$j( this ).append(
						$j('<div />')
						.attr( {
							'id': "gnp_" + embedPlayer.id								
						} )
						.addClass( 'ui-state-highlight ui-corner-all' )
						.css({
							'position' : 'absolute',
							'display' : 'none',
							'background' : '#FFF',
							'color' : '#111',
							'top' : toppos + 'px',
							'left' : '10px',
							'right' : '10px'
						})
						.html( gM( 'mwe-for_best_experience' ) )
					)
					
					$target_warning = $j( '#gnp_' + embedPlayer.id );			
										
					$target_warning.append( 					 
						$j('<br />')
					);
						
					
					$target_warning.append( 
						$j( '<input />' )
						.attr({
							'id' : 'ffwarn_' + embedPlayer.id,
							'type' : "checkbox",
							'name' : 'ffwarn_' + embedPlayer.id
						})							
						.click( function() {
							if ( $j( this ).is( ':checked' ) ) {
								// Set up a cookie for 7 days:
								$j.cookie( 'show_player_warning', false, { expires: 7 } );
								// Set the current instance
								mw.setConfig( 'show_player_warning', false );
								$j( '#gnp_' + embedPlayer.id ).fadeOut( 'slow' );
							} else {
								mw.setConfig( 'show_player_warning', true );
								$j.cookie( 'show_player_warning', true );
							}
						} )							
					);
					$target_warning.append( 
						$j('<span />')
						.text( gM( 'mwe-do_not_warn_again' ) )
					)
				}				
															
				// Only show the warning if cookie and config are true
				if ( mw.getConfig( 'show_player_warning' ) === true  ){
					$j( '#gnp_' + embedPlayer.id ).fadeIn( 'slow' );
				}
			},
			function() {
				$j( '#gnp_' + embedPlayer.id ).fadeOut( 'slow' );
			}
		);
	},
	
	/**
	* Binds the volume controls
	*/
	doVolumeBinding: function( ) {
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		embedPlayer.$interface.find( '.volume_control' ).unbind().buttonHover().click( function() {
			mw.log( 'Volume control toggle' );
			embedPlayer.toggleMute();
		} );
		
		// Add vertical volume display hover
		if ( this.volume_layout == 'vertical' ) {
			// Default volume binding:
			var hoverOverDelay = false;
			var $targetvol = embedPlayer.$interface.find( '.vol_container' );
			embedPlayer.$interface.find( '.volume_control' ).hover(
				function() {
					$targetvol.addClass( 'vol_container_top' );
					// Set to "below" if playing and embedType != native
					if ( embedPlayer && embedPlayer.isPlaying && embedPlayer.isPlaying() && !embedPlayer.supports['overlays'] ) {
						$targetvol.removeClass( 'vol_container_top' ).addClass( 'vol_container_below' );
					}
					$targetvol.fadeIn( 'fast' );
					hoverOverDelay = true;
				},
				function() {
					hoverOverDelay = false;
					setTimeout( function() {
						if ( !hoverOverDelay ) {
							$targetvol.fadeOut( 'fast' );
						}
					}, 500 );
				}
			);
		}
		
		// Setup play-head slider:
		var sliderConf = {
			range: "min",
			value: 80,
			min: 0,
			max: 100,
			slide: function( event, ui ) {
				var perc = ui.value / 100;
				// mw.log('update volume:' + perc);
				embedPlayer.updateVolumen( perc );
			},
			change:function( event, ui ) {
				var perc = ui.value / 100;
				if ( perc == 0 ) {
					embedPlayer.$interface.find( '.volume_control span' ).removeClass( 'ui-icon-volume-on' ).addClass( 'ui-icon-volume-off' );
				} else {
					embedPlayer.$interface.find( '.volume_control span' ).removeClass( 'ui-icon-volume-off' ).addClass( 'ui-icon-volume-on' );
				}
				var perc = ui.value / 100;
				embedPlayer.updateVolumen( perc );
			}
		}
		
		if ( this.volume_layout == 'vertical' ) {
			sliderConf[ 'orientation' ] = "vertical";
		}
		
		embedPlayer.$interface.find( '.volume-slider' ).slider( sliderConf );
	},
	
	/**
	* Get the options menu ul with li menu items
	*/
	getOptionsMenu: function( ) {		
		$optionsMenu = $j( '<ul />' );
		for( var i in this.optionMenuItems ){
		
			// Make sure its supported in the current ctrlBuilder config: 
			if( ! this.supportedMenuItems[ i ] 	) {
			 	continue;
			}
			$optionsMenu.append(
				this.optionMenuItems[i]( this )
			);
		}				
		return $optionsMenu;
	},		
	
	/**	
	* Option menu items
	*
	* @return
	* 	'li' a li line item with click action for that menu item  
	*/
	optionMenuItems: {	
		// Player select menu item
		'playerSelect': function( ctrlObj ){
			return $j.getLineItem( 					
				gM( 'mwe-chose_player' ),
				'gear',
				function( ) {
					ctrlObj.displayOverlay(  
						ctrlObj.getPlayerSelect()
					);						
				}
			)
		},				
								
		// Download the file menu
		'download': function( ctrlObj ) {
			return $j.getLineItem( 					
				 gM( 'mwe-download' ),
				'disk',
				function( ) {
					ctrlObj.displayOverlay( gM('mwe-loading_txt' ) );					
					// Call show download with the target to be populated
					ctrlObj.showDownload(  		
						ctrlObj.embedPlayer.$interface.find( '.overlay-content' ) 
					);										
				}
			)
		},		
		
		// Share the video menu
		'share': function( ctrlObj ) {
			return $j.getLineItem( 					
				gM( 'mwe-share' ),
				'mail-closed',
				function( ) {
					ctrlObj.displayOverlay( 
						ctrlObj.getShare()
					);						
				}
			)
		}
	},
	
	/** 
	* Close a menu overlay 
	*/
	closeMenuOverlay: function(){
		var _this = this;	
		var embedPlayer = this.embedPlayer;
		var $overlay = embedPlayer.$interface.find( '.overlay-win,.ui-widget-overlay,.ui-widget-shadow' );
		
		$overlay.fadeOut( "slow", function() {
			$overlay.remove();
		} );
		// Show the big play button: 
		embedPlayer.$interface.find( '.play-btn-large' ).fadeIn( 'slow' );
		return false; // onclick action return false
	},
	
	/** 
	* Generic function to display custom HTML overlay 
	* on video.
	* 
	* @param {String} overlayContent content to be displayed
	*/
	displayOverlay: function( overlayContent ) {
		var _this = this;
		var embedPlayer = this.embedPlayer;
		
		if ( !this.supportedComponets[ 'overlays' ] ) {
			embedPlayer.stop();
		}						  
		// Hide the big play button: 
		embedPlayer.$interface.find( '.play-btn-large' ).hide();		
		
		// Check if overlay window is already present: 
		if ( embedPlayer.$interface.find( '.overlay-win' ).length != 0 ) {
			//Update the content			
			embedPlayer.$interface.find( '.overlay-content' ).html(
				overlayContent
			);
			return ;
		}
		
		// Add an overlay
		embedPlayer.$interface.append( 
			$j('<div />')
			.addClass( 'ui-widget-overlay' )
			.css( {
				'height' : this.getOverlayHeight() + 'px'
			} )
		);
		
		// Setup the close button 
		$closeButton = $j('<span />')
		.addClass( 'ui-icon ui-icon-closethick' )
		.css({
			'position': 'absolute',
			'cursor' : 'pointer',
			'top' : '2px',
			'right' : '2px'
		})
		.buttonHover()
		.click( function() {
			_this.closeMenuOverlay();
		} );
					
		var overlayMenuCss = {
			'height' : 200,
			'width' :  250,
			'position' : 'absolute',
			'left' : '10px',
			'top': '15px',
			'overflow' : 'auto',
			'padding' : '4px',
			'z-index' : 2
		};	
		$overlayMenu = $j('<div />')
			.addClass( 'overlay-win ui-state-default ui-widget-header ui-corner-all' )
			.css( overlayMenuCss )
			.append(
				$closeButton,
				$j('<div />')
					.addClass( 'overlay-content' )					
					.append( overlayContent )
			)
			
		// Clone the overlay menu css: 
		var shadowCss = jQuery.extend( true, {}, overlayMenuCss );
		shadowCss['height' ] = 210;
		shadowCss['width' ] = 260;
		shadowCss[ 'z-index' ] = 1;		
		$overlayShadow = $j( '<div />' )
			.addClass('ui-widget-shadow ui-corner-all')
			.css( shadowCss );
			
		// Append the overlay menu to the player interface			
		embedPlayer.$interface.prepend( 
			$overlayMenu,
			$overlayShadow
		)
		.find( '.overlay-win' )
		.fadeIn( "slow" );					
		
		return false; // onclick action return false
	},	
	
	/**
	* Get the "share" interface
	* 
	* TODO share should be enabled via <embed> tag usage to be compatible
	* with sites social networking sites that allow <embed> tags but not js
	*  
	* @param {Object} $target Target jQuery object to set share html
	*/
	getShare: function( ) {
		var embedPlayer = this.embedPlayer;
		var	embed_code = embedPlayer.getEmbeddingHTML();		
		var _this = this;
		
		var $shareInterface = $j('<div />');
		
		$shareList = $j( '<ul />' );
		
        $shareList
        .append(
			$j('<li />')
	        .append(
	        	$j('<a />')
	        	.attr('href', '#')
	        	.addClass( 'active' )
	        	.text(
	        		gM( 'mwe-embed_site_or_blog' )
	        	)
	        )
	    )
        
       $shareInterface.append(
        	$j( '<h2 />' )
        	.text( gM( 'mwe-share_this_video' ) )      	
        	.append(
        		$shareList
        	)
        );
        
      	$shareInterface.append(      		
			
      		$j('<span />')
      		.addClass( 'source_wrap' )
      		.html(
      			$j( '<textarea />' )
      			.html( embed_code )
      			.click( function() {
					$j( this ).select();
				})
      		),
      		
      		$j('<br />'),
      		$j('<br />'),
				
      		$j('<button />')
      		.addClass( 'ui-state-default ui-corner-all copycode' )
      		.text( gM( 'mwe-copy-code' ) )
      		.click(function() {
				$target.find( 'textarea' ).focus().select();
				// Copy the text if supported:
				if ( document.selection ) {
					CopiedTxt = document.selection.createRange();
					CopiedTxt.execCommand( "Copy" );
				}
			} )
			
		);
		return $shareInterface;
	},
	
	/**
	* Shows the Player Select interface
	* 
	* @param {Object} $target jQuery target for output
	*/
	getPlayerSelect: function( ) {		
		mw.log('getPlayerSelect');		
		
		var embedPlayer = this.embedPlayer;
						
		$playerSelect = $j('<div />')
		.append( 
			$j( '<h2 />' )
			.text( gM( 'mwe-chose_player' )  )
		);
		 		
		$j.each( embedPlayer.mediaElement.getPlayableSources(), function( source_id, source ) {
			var playable = mw.EmbedTypes.players.defaultPlayer( source.getMIMEType() );

			var is_selected = ( source == embedPlayer.mediaElement.selected_source );			
			
			$playerSelect.append( 
				$j( '<h2 />' )
				.text( source.getTitle() )
			);
			
			if ( playable ) {
				$playerList = $j('<ul />');
				// output the player select code:
				var supporting_players = mw.EmbedTypes.players.getMIMETypePlayers( source.getMIMEType() );

				for ( var i = 0; i < supporting_players.length ; i++ ) {									
									
					// Add link to select the player if not already selected )
					if( embedPlayer.selected_player.id == supporting_players[i].id && is_selected ) {	
						// Active player ( no link )
						$playerLine = $j( '<span />' )
						.text( 
						 	supporting_players[i].getName()
						)
						.addClass( 'ui-state-highlight ui-corner-all' );	
						
					} else {
						// Non active player add link to select: 
						$playerLine = $j( '<a />')
							.attr({
								'href' : '#',
								'rel' : 'sel_source',
								'id' : 'sc_' + source_id + '_' + supporting_players[i].id 
							})
							.addClass( 'ui-corner-all')
							.text( supporting_players[i].getName() )
							.click( function() {
								var iparts = $j( this ).attr( 'id' ).replace(/sc_/ , '' ).split( '_' );
								var source_id = iparts[0];
								var default_player_id = iparts[1];
								mw.log( 'source id: ' +  source_id + ' player id: ' + default_player_id );
				
								embedPlayer.ctrlBuilder.closeMenuOverlay();
								embedPlayer.mediaElement.selectSource( source_id );
				
								mw.EmbedTypes.players.setPlayerPreference( 
									default_player_id,
									embedPlayer.mediaElement.sources[ source_id ].getMIMEType() 
								);
				
								// Issue a stop
								embedPlayer.stop();				
				
								// Don't follow the # link:
								return false;
							} )
							.hover(
								function(){
									$j( this ).addClass('ui-state-active')
								},
								function(){
									$j( this ).removeClass('ui-state-active')
								}
							);
					}
					
					// Add the player line to the player list:					
					$playerList.append(
						$j( '<li />' ).append(
							$playerLine
						)
					);
				}
				
				// Append the player list: 
				$playerSelect.append( $playerList );
				
			} else {
				// No player available: 
				$playerSelect.append( gM( 'mwe-no-player',  source.getTitle() ) ) 
			}
		} );
		
		// Return the player select elements
		return $playerSelect;		
	},
	
	/**
	* Loads sources and calls showDownloadWithSources
	* @param {Object} $target jQuery target to output to
	*/
	showDownload: function( $target ) {
		var _this = this;
		var embedPlayer = this.embedPlayer;
		// Load the roe if available (to populate out download options:		
		// mw.log('f:showDownload '+ this.roe + ' ' + this.mediaElement.addedROEData);
		if ( embedPlayer.roe && embedPlayer.mediaElement.addedROEData == false ) {
			$target.html( gM( 'mwe-loading_txt' ) );
			embedPlayer.getMvJsonUrl( this.roe, function( data ) {
			   embedPlayer.mediaElement.addROE( data );
			   _this.showDownloadWithSources( $target );
			} );
		} else {
			_this.showDownloadWithSources( $target );
		}
	},
	
	/**
	* Shows the download interface with sources loaded
	* @param {Object} $target jQuery target to output to
	*/
	showDownloadWithSources : function( $target ) {
		var _this = this;
		mw.log( 'showDownloadWithSources::' + $target.length );
		var embedPlayer = this.embedPlayer;
		// Empty the target:
		$target.empty();
		
		var $mediaList = $j( '<ul />' );
		var $textList =  $j( '<ul />' );
		$j.each( embedPlayer.mediaElement.getSources(), function( index, source ) {
			if(  source.getSrc() ) {
				mw.log("add src: "  + source.getTitle() );
				var $dl_line = $j( '<li />').append(
					$j('<a />')					
					.attr( 'href', source.getSrc() )
					.text(  source.getTitle() )
				);		
				// Add link to correct "bucket" 
							
				//Add link to time segment:
				if ( source.getSrc().indexOf( '?t=' ) !== -1 ) {
					$target.append( $dl_line );
				} else if ( this.getMIMEType() == "text/cmml" || this.getMIMEType() == "text/x-srt" ) {
					// Add link to text list
					$textList.append( $dl_line );
				} else {
					// Add link to media list
					$mediaList.append( $dl_line );
				}
				
			}
		} );
		if( $mediaList.find('li').length != 0 ) {
			$target.append(
				$j('<h2 />')
				.text( gM( 'mwe-download_full' ) ),
				$mediaList
			)
		}
		
		if( $textList.find('li').length != 0 ) {
			$target.append(
				$j('<h2 />')
				.text( gM( 'mwe-download_text' ) ),
				$textList
			)
		}		
	},
	
	
	/**
	* Get component
	*
	* @param {String} component_id Component key to grab html output
	*/
	getComponent: function( component_id ) {
		if ( this.components[ component_id ] ) {
			return this.components[ component_id ].o( this );
		} else {
			return false;
		}
	},
	
	/**
	 * Get a component height
	 * 
	 * @param {String} component_id Component key to grab height
	 */
	getComponentHeight: function( component_id ) {
		if ( this.components[ component_id ] 
			&& this.components[ component_id ].h ) 
		{
			return this.components[ component_id ].h
		}
		return false;
	},
	
	/**
	* Components Object
	* Take in the embedPlayer and return some html for the given component.	
	*
	* components can be overwritten by skin javascript
	*
	* Component JSON structure is as follows:
	* 'o' Function to return a binded jQuery object ( accepts the ctrlObject as a parameter )
	* 'w' The width of the component
	* 'h' The height of the component ( if height is undefined the height of the control bar is used )
	*/
	components: {		
		/**
		* The large play button in center of the player
		*/
		'playButtonLarge': {
			'w' : 130,
			'h' : 96,
			'o' : function( ctrlObj ) {		
						
				return $j( '<div/>' )
					.attr( {
						'title'	: gM( 'mwe-play_clip' ),
						'class'	: "ui-state-default play-btn-large"
					} )
					// Get dynamic position for big play button
					.css( {
						'left' 	: ( ( ctrlObj.embedPlayer.getPlayerWidth() - this.w ) / 2 ),
						'top'	: ( ( ctrlObj.embedPlayer.getPlayerHeight() - this.h ) / 2 )
					} )
					// Add play hook:
					.buttonHover().click( function() {
						 ctrlObj.embedPlayer.play();
					} );
			}			
		},
		
		/**
		* The kaltura attribution button
		*/
		'kalturaAttribution' : {
			'w' : 28,
			'o' : function( ctrlObj ){			
				return $j('<a />')
					.attr({
						'href': 'http://kaltura.com',
						'title' : gM( 'mwe-kaltura-platform-title' ),
						'target' : '_new'
					})
					.append(
						$j( '<div />' )
						.addClass( 'rButton' )
						.css({
							'top' : '9px',
							'left' : '2px'
						})
						.append( 
							$j('<span />')
							.addClass( 'ui-icon kaltura-icon' )
						)
					)						
			}
		},
		
		/**
		* The options button, invokes display of the options menu
		*/
		'options': {
			'w': 28,
			'o': function( ctrlObj ) {
				return $j( '<div />' )
						.attr( 'title',  gM( 'mwe-player_options' ) )						
						.addClass( 'ui-state-default ui-corner-all ui-icon_link rButton options-btn' )
						.append( 
							$j('<span />')
							.addClass( 'ui-icon ui-icon-wrench' )
						)	
						.buttonHover()		
						// Options binding:
						.menu( {
							'content' : ctrlObj.getOptionsMenu(),		
							'positionOpts': {
								'directionV' : 'up',								
								'offsetY' : 32,
								'directionH' : 'left',
								'offsetX' : -28
							}							
						} );	
			}
		},
		
		/**
		* The fullscreen button for displaying the video fullscreen
		*/
		'fullscreen': {
			'w': 28,
			'o': function( ctrlObj ) {				
				return $j( '<div />' )
						.attr( 'title', gM( 'mwe-player_fullscreen' ) )
						.addClass( "ui-state-default ui-corner-all ui-icon_link rButton fullscreen-btn" )
						.append(
							$j( '<span />' )
							.addClass( "ui-icon ui-icon-arrow-4-diag" )
						)
							// Fullscreen binding:
						.buttonHover().click( function() {
							ctrlObj.embedPlayer.fullscreen();
						} );
			}
		},
		
		
		/**
		* The pause / play button
		*/
		'pause': {
			'w': 28,
			'o': function( ctrlObj ) {
				return $j( '<div />' )
						.attr( 'title', gM( 'mwe-play_clip' ) )
						.addClass ( "ui-state-default ui-corner-all ui-icon_link lButton play-btn" )
						.append( 
							$j( '<span />' )
							.addClass( "ui-icon ui-icon-play" )
						)
						// Play / pause binding
						.buttonHover()
						.click( function() {
							ctrlObj.embedPlayer.play();		
						});
			}
		},
		
		/**
		* The closed captions button
		*/
		'timedText': {
			'w': 28,
			'o': function( ctrlObj ) {
				return $j( '<div />' )
						.attr( 'title', gM( 'mwe-timed_text' ) )
						.addClass( "ui-state-default ui-corner-all ui-icon_link rButton timed-text" )
						.append( 
							$j( '<span />' )
							.addClass( "ui-icon ui-icon-comment" )
						)
						// Captions binding:
						.buttonHover()
						.click( function() {			
							ctrlObj.embedPlayer.showTextInterface();
						} )
						/*
						* menu: 
						.menu( {
							'content' : gM('mwe-loading_txt')
							'positionOpts': {
								'directionV' : 'up',								
								'offsetY' : 32,
								'directionH' : 'left',
								'offsetX' : -28
							}							
						} );
						*/
			}
		},
		
		/** 
		* The volume control interface html
		*/
		'volumeControl': {
			'w' : 28,
			'o' : function( ctrlObj ) {
				$volumeOut = $j( '<span />' );
				if ( ctrlObj.volume_layout == 'horizontal' ) {
					$volumeOut.append(  
						$j( '<div />' )
						.addClass( "ui-slider ui-slider-horizontal rButton volume-slider" )
					);
				}
				// Add the volume control icon
				$volumeOut.append( 	
				 	$j('<div />')
				 	.attr( 'title', gM( 'mwe-volume_control' ) )
				 	.addClass( "ui-state-default ui-corner-all ui-icon_link rButton volume_control" )
				 	.append( 
				 		$j( '<span />' )
				 		.addClass( "ui-icon ui-icon-volume-on" )
				 	)
				 );
						
				if ( ctrlObj.volume_layout == 'vertical' ) {
					$volumeOut.find('.volume_control').append( 	
						$j( '<div />' )
						.css( {
							'position' : 'absolute',
							'display' : 'none',
							'left' : '0px;'
						})
						.addClass( "vol_container ui-corner-all" )
						.append( 
							$j( '<div />' )
							.addClass ( "volume-slider" )
						)
					);
				}				
				//Return the inner html 
				return $volumeOut.html();
			}
		},
		
		/*
		* The time display area
		*/
		'timeDisplay': {
			'w' : 100,
			'o' : function( ctrlObj ) {
				return $j( '<div />' )
						.addClass( "ui-widget time-disp" )
						.append( 
							ctrlObj.embedPlayer.getTimeRange()
						)
						
			}
		},
		
		/**
		* The playhead component
		*/
		'playHead': {
			'w':0, // special case (takes up remaining space)
			'o':function( ctrlObj ) {
				var embedPlayer = ctrlObj.embedPlayer;
				var _this = this;
				var $playHead = $j( '<div />' )
					.addClass ( "play_head" )
					.css({ 
						"position" : 'absolute',
						"left" : '33px',
						"right" :  ( (embedPlayer.getPlayerWidth() - ctrlObj.available_width) - 33) + 'px' 
					})					
					// Playhead binding
					.slider( {
						range: "min",
						value: 0,
						min: 0,
						max: 1000,
						start: function( event, ui ) {
							var id = ( embedPlayer.pc != null ) ? embedPlayer.pc.pp.id:embedPlayer.id;
							embedPlayer.userSlide = true;
							$j( id + ' .play-btn-large' ).fadeOut( 'fast' );
							// If playlist always start at 0
							embedPlayer.start_time_sec = ( embedPlayer.instanceOf == 'mvPlayList' ) ? 0:
											mw.npt2seconds( embedPlayer.getTimeRange().split( '/' )[0] );
						},
						slide: function( event, ui ) {
							var perc = ui.value / 1000;
							embedPlayer.jump_time = mw.seconds2npt( parseFloat( parseFloat( embedPlayer.getDuration() ) * perc ) + embedPlayer.start_time_sec );
							// mw.log('perc:' + perc + ' * ' + embedPlayer.getDuration() + ' jt:'+  this.jump_time);
							if ( _this.longTimeDisp ) {
								embedPlayer.setStatus( gM( 'mwe-seek_to', embedPlayer.jump_time ) );
							} else {
								embedPlayer.setStatus( embedPlayer.jump_time );
							}
							// Update the thumbnail / frame
							if ( embedPlayer.isPlaying == false ) {
								embedPlayer.updateThumbPerc( perc );
							}
						},
						change:function( event, ui ) {
							// Only run the onChange event if done by a user slide 
							// (otherwise it runs times it should not)
							if ( embedPlayer.userSlide ) {
								embedPlayer.userSlide = false;
								embedPlayer.seeking = true;
								// Stop the monitor timer (if we can)				
								embedPlayer.stopMonitor();
			
								var perc = ui.value / 1000;
								// set seek time (in case we have to do a url seek)
								embedPlayer.seek_time_sec = mw.npt2seconds( embedPlayer.jump_time, true );
								mw.log( 'do jump to: ' + embedPlayer.jump_time + ' perc:' + perc + ' sts:' + embedPlayer.seek_time_sec );
								embedPlayer.setStatus( gM( 'mwe-seeking' ) );
								embedPlayer.doSeek( perc );
							}
						}
					} );
		
				// Up the z-index of the default status indicator:
				$playHead.find( 'ui-slider-handle' ).css( 'z-index', 4 );
				$playHead.find( '.ui-slider-range' ).addClass( 'ui-corner-all' ).css( 'z-index', 2 );
		
				// Add buffer html: 
				$playHead.append( 
					$j('<div />')
					.addClass( "ui-slider-range ui-slider-range-min ui-widget-header")
					.addClass( "ui-state-highlight ui-corner-all mw_buffer")
				);
									
				return $playHead;	
			}
		}
	}
};
