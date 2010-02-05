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

/*
 * controlsBuilder prototype:
 */
ctrlBuilder.prototype = {
	//Default Local values: 
	
	// Parent css Class name
	playerClass : 'mv-player',
	
	// Long string display of time value 
	long_time_disp: true,
	
	// If the options menu outside of player 
	external_options : true,
	
	// Default volume layout is "vertical"
	volume_layout : 'vertical',
	
	// Default control bar height is 33
	height: 33,		
	
	// Default supported components is merged with embedPlayerect supported types
	supportedComponets: {
	  'options':true,
	  'borders':true
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
		if ( window[ embedPlayer.skinName + 'Config'] ) {
		
			// Clone as to not override prototype: 	
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
		embedPlayer.$interface.find('.control-bar').remove();
			
		// Add some space to control_wrap for the control bar:
		embedPlayer.$interface.css({
			'height' : parseInt( embedPlayer.height ) + parseInt( this.height)
		});
		embedPlayer.$interface.append(
			$j('<div>')
			.addClass( 'ui-state-default ui-widget-header ui-helper-clearfix control-bar' )
			.html(
				this.buildControls()
			)
			.css('display', 'block')
		);
		// Add hooks once Controls are in DOM
		this.addControlHooks();
	},
	
	/**
	* Builds the interface controls
	* @returns the interface html string
	*/ 
	buildControls: function(){
		var _this = this;
		mw.log( 'f:controlsBuilder:: opt:' + this.options );
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		
		this.available_width = embedPlayer.getPlayerWidth();
		
		// Make pointer to the embedPlayer
		this.embedPlayer = embedPlayer;
		var _this = this;
		this.supportedComponets = $j.extend(this.supportedComponets, embedPlayer.supports);
		
		// Check for timed text support:
		if( embedPlayer.isTimedTextSupported() ){
			this.supportedComponets['timed_text'] = true;
		}
			
		// Append options to body (if not already there)
		if ( this.external_options && $j( '#mv_vid_options_' + this.id ).length == 0 ){
			$j( 'body' ).append( this.components[ 'options_menu' ].o( this ) );
		}

		var s = '';
		
		// Build component output: 
		for ( var i in this.components ) {
			// Make sure the given components is supported:
			if ( this.supportedComponets[ i ] ) {
				if ( this.available_width > this.components[i].w ) {
					// Special case with playhead don't add unless we have 50px
					if ( i == 'play_head' && this.available_width < 30 )
						continue;
					s += this.components[i].o( this  );
					this.available_width -= this.components[i].w;
				} else {
					mw.log( 'not enough space for control component:' + i );
				}
			}
		}
		return s;
	},
	
	/**
	* Get minimal width for interface overlay
	*/
	getOverlayWidth: function(){
		return ( this.embedPlayer.getPlayerWidth() < 300 )? 300 : this.embedPlayer.getPlayerWidth();
	},	
	
	/**
	* Get minimal height for interface overlay
	*/
	getOverlayHeight: function(){
		return ( this.embedPlayer.getPlayerHeight() < 200 )? 200 : this.embedPlayer.getPlayerHeight();
	},
	
	/**
	* Get the control bar height: 
	*/
	getControlBarHeight: function(){
		return this.height;
	},
	
	/**
	* addControlHooks
	* Adds control hooks once controls are in the DOM
	*/
	addControlHooks:function( ) {
		// Set up local pointer to the embedPlayer
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		
		// Setup target shortcut to	control-bar
		$target = embedPlayer.$interface;
				
		// Add play hook:
		$target.find( '.play-btn,.play-btn-large' ).unbind().buttonHover().click( function() {
			embedPlayer.play();
		} );

		// Add recommend firefox if we have non-native playback:
		if ( _this.doNativeWarningCheck() ) {
			$j( '#dc_' + embedPlayer.id ).hover(
				function() {
					if ( $j( '#gnp_' + embedPlayer.id ).length == 0 ) {
						var toppos = ( embedPlayer.instanceOf == 'mvPlayList' ) ? 25 : 10;
						$j( this ).append( 
							$j('<div />')
							.attr({
								'id': "gnp_" + embedPlayer.id,								
							})
							.addClass('ui-state-highlight ui-corner-all')
							.css({
								'position':'absolute',
								'display':'none',
								'background':'#FFF',
								'color':'#111',
								'top' : toppos + 'px',
								'left' : '10px',
								'right' : '10px'
							})
							.html( gM( 'mwe-for_best_experience' ) ),							 
							$j('<br />'),
							$j('<input />')
							.attr({
								'id' : 'ffwarn_' + embedPlayer.id,
							})
							.text( gM( 'mwe-do_not_warn_again' ) )
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
					}
					// Only show the warning if cookie and config are true
					if ( mw.getConfig( 'show_player_warning' ) === true  )
						$j( '#gnp_' + embedPlayer.id ).fadeIn( 'slow' );
				},
				function() {
					$j( '#gnp_' + embedPlayer.id ).fadeOut( 'slow' );
				}
			);
		}

		if ( $j.browser.msie  &&  $j.browser.version <= 6 ) {
			$j( embedPlayer.id + ' .play-btn-large' ).pngFix();
		}


		// Captions binding:
		$target.find( '.timed-text' ).unbind().buttonHover().click( function() {			
			embedPlayer.showTextInterface();
		} );

		// Options binding:
		$target.find( '.options-btn' ).unbind().buttonHover().click( function() {
			embedPlayer.doOptionsHTML();
		} );

		// Fullscreen binding:
		$target.find( '.fullscreen-btn' ).unbind().buttonHover().click( function() {
			embedPlayer.fullscreen();
		} );
		
		// Playhead binding
		mw.log( " should add slider binding: " + $target.find( '.play_head' ).length );
		$target.find( '.play_head' ).slider( {
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
				if ( _this.long_time_disp ) {
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
		$target.find( '.play_head .ui-slider-handle' ).css( 'z-index', 4 );
		$target.find( '.play_head .ui-slider-range' ).addClass( 'ui-corner-all' ).css( 'z-index', 2 );
		
		// Extended class list for jQuery ui themeing 
		//(we can probably refactor this with custom buffering highlighter)
		$target.find( '.play_head' ).append( this.getBufferHtml() );
			
		var $opt = $j( '#mv_vid_options_' + embedPlayer.id );
		
		// videoOptions ... @@todo should be merged with something more like kskin.js:
		$opt.find( '.vo_selection' ).click( function() {			
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
		
		this.doVolumeBinding();
		
		// Check if we have any custom skin Bindings to run
		if ( this.addSkinControlBindings && typeof( this.addSkinControlBindings ) == 'function' )
			this.addSkinControlBindings();
	},
	closeMenuOverlay: function(){
		var _this = this;	
		var $overlay = $j( '#blackbg_' + this.embedPlayer.id );
		$overlay.fadeOut( "slow", function() {
			$overlay.remove();
		} );
		return false; // onclick action return false
	},
	/**
	* check if a warning should be issued to non-native playback systems 
	*
	* dependent on mediaElement being setup 
	*/ 
	doNativeWarningCheck: function( ) {		
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
	* Binds the volume controls
	*/
	doVolumeBinding:function() {
		var embedPlayer = this.embedPlayer;
		var _this = this;		
		embedPlayer.$interface.find( '.volume_control' ).unbind().buttonHover().click( function() {
			mw.log( 'clicked volume control' );
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
					setTimeout( function doHideVolume() {
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
		
		if ( this.volume_layout == 'vertical' )
			sliderConf[ 'orientation' ] = "vertical";
		
		embedPlayer.$interface.find( '.volume-slider' ).slider( sliderConf );
	},
	
	/** 
	* Get the Buffer Html that overlays the playhead
	*/
	getBufferHtml:function() {
		return '<div class="ui-slider-range ui-slider-range-min ui-widget-header ' +
				'ui-state-highlight ui-corner-all ' +
				'mw_buffer" style="width:0px;height:100%;z-index:1;top:0px" />';
	},
	
	/**
	* Get component
	*
	* @param {String} component Component key to grab html output
	*/
	getComponent:function( component ) {
		if ( this.components[ component ] ) {
			return this.components[ component ].o( this );
		} else {
			return false;
		}
	},
	
	/**
	* Components Object
	* Take in the embedPlayer and return some html for the given component.
	*
	* components can be overwritten by skin javascript
	*/
	components: {		
		/*
		* The large play button in center of the player
		*/
		'play-btn-large': {
			'w' : 130,
			'h' : 96,
			'o':function( ctrlObj ) {
				// Get dynamic position for big play button
				return $j( '<div/>' ).attr( {
								'title'	: gM( 'mwe-play_clip' ),
								'class'	: "ui-state-default play-btn-large"
							} )
							.css( {
								'left' 	: ( ( ctrlObj.embedPlayer.getPlayerWidth() - this.w ) / 2 ),
								'top'	: ( ( ctrlObj.embedPlayer.getPlayerHeight() - this.h ) / 2 )
							} )
							.wrap( '<div/>' ).parent().html();
			}
		},
		
		/**
		* The options for the player, includes player selection, 
		* download, and share options
		*/
		'options_menu': {
			'w':0,
			'o':function( ctrlObj ) {
				var o = '<div id="mv_vid_options_' + ctrlObj.embedPlayer.id + '" class="videoOptions">' +
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
			}
		},
		
		/**
		* The options button, invokes display of the options menu
		*/
		'options': {
			'w':28,
			'o':function( ctrlObj ) {
				return '<div title="' + gM( 'mwe-player_options' ) + '" class="ui-state-default ui-corner-all ui-icon_link rButton options-btn">' +
							'<span class="ui-icon ui-icon-wrench"></span>' +
						'</div>';
			}
		},
		
		/**
		* The fullscreen button for displaying the video fullscreen
		*/
		'fullscreen': {
			'w':28,
			'o':function( ctrlObj ) {
				return '<div title="' + gM( 'mwe-player_fullscreen' ) + '" class="ui-state-default ui-corner-all ui-icon_link rButton fullscreen-btn">' +
							'<span class="ui-icon ui-icon-arrow-4-diag"></span>' +
						'</div>'
			}
		},
		
		
		/**
		* The pause button
		*/
		'pause': {
			'w':28,
			'o':function( ctrlObj ) {
				return '<div title="' + gM( 'mwe-play_clip' ) + '" class="ui-state-default ui-corner-all ui-icon_link lButton play-btn">' +
							'<span class="ui-icon ui-icon-play"/>' +
						'</div>';
			}
		},
		
		/*
		* The closed captions button
		*/
		'timed_text': {
			'w':28,
			'o':function( ctrlObj ) {
				return '<div title="' + gM( 'mwe-timed_text' ) + '" class="ui-state-default ui-corner-all ui-icon_link rButton timed-text">' +
							'<span class="ui-icon ui-icon-comment"></span>' +
						'</div>'
			}
		},
		
		/*
		* The volume control interface html
		*/
		'volume_control': {
			'w':28,
			'o':function( ctrlObj ) {
				var o = '';
				if ( ctrlObj.volume_layout == 'horizontal' )
					o += '<div class="ui-slider ui-slider-horizontal rButton volume-slider"></div>';
					
				o += '<div title="' + gM( 'mwe-volume_control' ) + '" class="ui-state-default ui-corner-all ui-icon_link rButton volume_control">' +
						'<span class="ui-icon ui-icon-volume-on"></span>';
						
				if ( ctrlObj.volume_layout == 'vertical' ) {
					o += '<div style="position:absolute;display:none;left:0px;" class="vol_container ui-corner-all">' +
							'<div class="volume-slider" ></div>' +
						'</div>';
				}
				o += '</div>';
				return o;
			}
		},
		
		/*
		* The time display area
		*/
		'time_display': {
			'w':90,
			'o':function( ctrlObj ) {
				return '<div class="ui-widget time-disp">' + ctrlObj.embedPlayer.getTimeRange() + '</div>';
			}
		},
		/*
		* The playhead html
		*/
		'play_head': {
			'w':0, // special case (takes up remaining space)
			'o':function( ctrlObj ) {
				return '<div class="play_head" style="width: ' + ( ctrlObj.available_width - 20 ) + 'px;"></div>';
			}
		}
	}
};
