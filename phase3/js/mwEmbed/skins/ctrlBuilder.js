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
			
		//var $opt = $j( '#mv_vid_options_' + embedPlayer.id );
		
		// videoOptions ... @@todo should be merged with something more like kskin.js:
		/*$opt.find( '.vo_selection' ).click( function() {			
			embedPlayer.displayOverlay();			
			embedPlayer.showPlayerSelect( embedPlayer.$interface.find( '.videoOptionsComplete' ) );			
			$opt.hide();
			return false;
		} );
		
		$opt.find( '.vo_download' ).click( function() {
			embedPlayer.displayOverlay();
			embedPlayer.showDownload(  embedPlayer.$interface.find( '.videoOptionsComplete' ) );
			$opt.hide();
			return false;
		} );
		
		$opt.find( '.vo_showcode' ).click( function() {
			embedPlayer.displayOverlay();
			embedPlayer.showShare(  embedPlayer.$interface.find( '.videoOptionsComplete' ) );
			$opt.hide();
			return false;
		} );
		*/
		
		this.doVolumeBinding();
		
		// Check if we have any custom skin Bindings to run
		if ( this.addSkinControlBindings && typeof( this.addSkinControlBindings ) == 'function' ){
			this.addSkinControlBindings();
		}
	},
	
	/** 
	* Close a menu overlay 
	*/
	closeMenuOverlay: function(){
		var _this = this;	
		var $overlay = $j( '#blackbg_' + this.embedPlayer.id );
		$overlay.fadeOut( "slow", function() {
			$overlay.remove();
		} );
		return false; // onclick action return false
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
		if( this.embedPlayer.getPlayerHeight() < 199 )
			return false;
				
		// See if we have native support for ogg: 
		var supporting_players = mw.EmbedTypes.players.getMIMETypePlayers( 'video/ogg' );
		for ( var i = 0; i < supporting_players.length; i++ ) {
			if ( supporting_players[i].id == 'videoElement' ) {
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
			if ( !foundOgg )
				return false;
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
		
		$j( '#dc_' + embedPlayer.id ).hover(
			function() {					
				if ( $j( '#gnp_' + embedPlayer.id ).length == 0 ) {
					var toppos = ( embedPlayer.instanceOf == 'mvPlayList' ) ? 25 : 10;
					
					$j( this ).append( 
						$j('<div />')
						.attr({
							'id': "gnp_" + embedPlayer.id								
						})
						.addClass( 'ui-state-highlight ui-corner-all' )
						.css({
							'position':'absolute',
							'display':'none',
							'background':'#FFF',
							'color':'#111',
							'top' : toppos + 'px',
							'left' : '10px',
							'right' : '10px'
						})
						.html( gM( 'mwe-for_best_experience' ) )
					);
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
					ctrObj.displayOverlay(  
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
					ctrObj.displayOverlay( gM('loading' ) );
					ctrlObj.showDownload( $j( ctrlObj.embedPlayer ).find( '.overlay-content' ) );										
				}
			)
		},		
		
		// Share the video menu
		'share': function( ctrlObj ) {
			return $j.getLineItem( 					
				gM( 'mwe-share' ),
				'mail-closed',
				function( ) {
					ctrlObj.showShare();						
				}
			)
		}
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
		
	  	// Fade in if the overlay is not already displayed: 
		var fade_in = true;
		if ( $j( '#blackbg_' + this.id ).length != 0 ) {
			fade_in = false;
			$j( '#blackbg_' + this.id ).remove();
		}
		
		$closeButton = $j('<span />')
			.text( gM('mwe-close_btn') )
			.css({
					'float' : 'right',
					'color' : '#FFF',
					'cursor' : 'pointer',
					'background' : '#111',
					'margin-right': '10px',
					'text-decoration' : 'underline'
				})
			.click(function() {
				_this.ctrlBuilder.closeMenuOverlay();
			})
			
		
		$overlayMenu = $j('<div />')
			.attr({
					'id' : 'blackbg_' + this.id
			})
			.addClass( 'videoComplete' )
			.css({
				'height' : this.ctrlBuilder.getOverlayHeight(),
				'width' :  this.ctrlBuilder.getOverlayWidth()
			})
			.append(
				$closeButton,
				$j('<div />')
					.addClass( 'overlay-content' )					
					.append( $overlayContent )
			)
		this.$interface.prepend( $overlayMenu );
		
		if ( fade_in )
			$j( '#blackbg_' + this.id ).fadeIn( "slow" );
		else
			$j( '#blackbg_' + this.id ).show();
		return false; // onclick action return false
	},	
	/**
	* Show the "share" msg
	* 
	* TODO share should be enabled via <embed> tag usage to be compatible
	* with sites social networking sites that allow <embed> tags but not js
	*  
	* @param {Object} $target Target jQuery object to set share html
	*/
	showShare:function( $target ) {
		var embedPlayer = this.embedPlayer;
		var	embed_code = embedPlayer.getEmbeddingHTML();		
		var _this = this;
		$target.empty();
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
        
        $target.append(
        	$j( '<h2 />' )
        	.text( gM( 'mwe-share_this_video' ) )      	
        	.append(
        		$shareList
        	)
        );
        
      	$target.append(
      		$j('<div />')
      		.addClass( 'source_wrap' )
      		.html(
      			$j( '<textarea />' )
      			.html( embed_code )
      			.click( function() {
					$j( this ).select();
				})
      		),
      		
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
			} ),
			
			$j('<div />')
			.addClass( "ui-state-highlight ui-corner-all" )
			.html(
				gM( 'mwe-read_before_embed' ) 
			)					
		);
	},
	/**
	* Shows the Player Select interface
	* 
	* @param {Object} $target jQuery target for output
	*/
	getPlayerSelect: function( ) {		
		mw.log('showPlayerSelect');		
		
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
					var $playerLink = $j( '<a />')
						.attr({
							'href' : '#',
							'rel' : 'sel_source',
							'id' : 'sc_' + source_id + '_' + supporting_players[i].id 
						})
						.text( supporting_players[i].getName() )
						.click( function() {
							var iparts = $j( this ).attr( 'id' ).replace(/sc_/ , '' ).split( '_' );
							var source_id = iparts[0];
							var default_player_id = iparts[1];
							mw.log( 'source id: ' +  source_id + ' player id: ' + default_player_id );
			
							embedPlayer.ctrlBuilder.closeMenuOverlay();
							embedPlayer.mediaElement.selectSource( source_id );
			
							mw.EmbedTypes.players.setPlayerPreference( default_player_id,
								 _this.mediaElement.sources[ source_id ].getMIMEType() );
			
							// Issue a stop
							embedPlayer.stop();				
			
							// Don't follow the # link:
							return false;
						} );
					
					if ( embedPlayer.selected_player.id == supporting_players[i].id && is_selected ) {
						$playerLink						
						.addClass('active' );												
					}
					$playerList.append(
						$j( '<li />' ).append(
							$playerLink
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
			$target.html( gM( 'loading_txt' ) );
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
		var embedPlayer = this.embedPlayer;
		$target.empty().append( 
			$j('<div />')
			.css({
				"color":"white"
			})
		);
		var $mediaList = $j( '<ul />' );
		var $textList =  $j( '<ul />' );
		$j.each( embedPlayer.mediaElement.getSources(), function( index, source ) {
			if(  source.getSrc() ) {
				var $dl_line = $j( '<li />').append(
					$j('<a />').
					css({
						"color" : "white"						
					})
					.attr( 'href', source.getSrc())
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
	* @param {String} component Component key to grab html output
	*/
	getComponent: function( component_id ) {
		if ( this.components[ component_id ] ) {
			return this.components[ component_id ].o( this );
		} else {
			return false;
		}
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
		* The options for the player, includes player selection, 
		* download, and share options
		*/
		'optionsMenu': {
			'w' : 0,
			'o' :  function( ctrlObj ) {
				/*var o = '<div id="mv_vid_options_' + ctrlObj.embedPlayer.id + '" class="videoOptions">' +
				'<div class="videoOptionsTop"></div>' +
				'<div class="videoOptionsBox">' +
				'<div class="block">' +
					'<h6>Video Options</h6>' +
				'</div>' +
					'<div class="block">' +
						'<p class="short_match vo_selection"><a href="#"><span>' + gM( 'mwe-chose_player' ) + '</span></a></p>' +
						'<p class="short_match vo_download"><a href="#"><span>' + gM( 'mwe-download' ) + '</span></a></p>' +
						'<p class="short_match vo_showcode"><a href="#"><span>' + gM( 'mwe-share' ) + '</span></a></p>';

					// link to the stream page if we are not already there:
					if ( ( ctrlObj.embedPlayer.roe || ctrlObj.embedPlayer.linkback ) && typeof mv_stream_interface == 'undefined' )
						o += '<p class="short_match"><a href="javascript:$j(\'#' + ctrlObj.id + '\').get(0).doLinkBack()"><span><strong>Source Page</strong></span></a></p>';

				o += '</div>' +
				'</div><!--videoOptionsInner-->' +
					'<div class="videoOptionsBot"></div>' +
				'</div><!--videoOptions-->';
				return o;
				*/
			}
		},
		
		/**
		* The kaltura attribution button
		*/
		'kalturaAttribution' : {
			'w' : 28,
			'o' : function( ctrlObj ){			
				return $j( '<div />' )
						.attr( 'title',  gM( 'mwe-kaltura-platform-title' ) )						
						.addClass( 'ui-state-default ui-corner-all ui-icon_link rButton' )
						.append( 
							$j('<span />')
							.addClass( 'ui-icon kaltura-icon' )
						)
						.unbind()
						.buttonHover()
						.click( function( ) { 
							window.location = 'http://kaltura.com';
						} );
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
						// Options binding:
						.menu( {
							'content'	: ctrlObj.getOptionsMenu(),														
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
						.buttonHover().click( function() {			
							ctrlObj.embedPlayer.showTextInterface();
						} );
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
			'w' : 90,
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
				var $playHead = $j( '<div />' )
					.addClass ( "play_head" )
					.css( "width", parseInt( ctrlObj.available_width - 20 ) + 'px' )					
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
