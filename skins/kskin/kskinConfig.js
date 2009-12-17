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
		'options_menu': {
			'w':0,
			'o':function( ctrlObj ) {
				var embedObj = ctrlObj.embedObj;
				
				// Setup menu offset ( if player height <  getOverlayHeight )				
				var menuOffset = ( embedObj.getPlayerHeight() <  ctrlObj.getOverlayHeight() ) ? 
					'top:' + ( embedObj.getPlayerHeight() + ctrlObj.getControlBarHeight() ) + 'px;'  : '';
														
				// Special common overflow hack: 
				// NOTE: should re-factor to just append menu to top body when it does not "fit" in the player
				if(   menuOffset != '' )
					$j( embedObj ).parents( '.thumbinner' ).css( 'overflow', 'visible' );				
														
				var o = '' +
				'<div class="k-menu ui-widget-content" ' +
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
		return ( this.embedObj.getPlayerWidth() < 200 )? 200 : this.embedObj.getPlayerWidth();
	},	
	
	/**
	* Get minimal height for interface overlay
	*/
	getOverlayHeight: function(){
		return ( this.embedObj.getPlayerHeight() < 160 )? 160 : this.embedObj.getPlayerHeight();
	},
	
	/**
	* Adds the skin Control Bindings
	*/
	addSkinControlBindings: function() {
		var embedObj = this.embedObj;
		var _this = this;
		var $tp = $j( '#' + embedObj.id );
		
		// Adds options and bindings: (we do this onClick )  
		
		 		
   		// Options menu display:			
   		$tp.find( '.k-options' ).unbind().click( function() {   			
			if ( $j( '#' + embedObj.id + ' .k-menu' ).length == 0 ) {
	   			// Stop the player if it does not support overlays:
				if ( !embedObj.supports['overlay'] )
					$tp.get( 0 ).stop();
				// Add the options       				
				_this.addOptionsBinding( $tp );
			}
	   		// Set up the text and menu:       			 					
			var $ktxt = $j( this );
			var $kmenu = $tp.find( '.k-menu' );
			if ( $kmenu.is( ':visible' ) ) {
				$kmenu.fadeOut( "fast", function() {
					$ktxt.find( 'span' ).html ( gM( 'mwe-menu_btn' ) );
				} );
				$tp.find( '.play-btn-large' ).fadeIn( 'fast' );
			} else {
				$kmenu.fadeIn( "fast", function() {
					$ktxt.find( 'span' ).html ( gM( 'mwe-close_btn' ) );
				} );
				$tp.find( '.play-btn-large' ).fadeOut( 'fast' );
			}
		} );				
		
	},
	
	/**
	* Adds binding for the options menu
	*
	* @param {Object} $tp Target video container for 
	*/
	addOptionsBinding: function( $tp ) {
		var _this = this;
		var embedObj = this.embedObj;
		if ( $j( '#' + embedObj.id + ' .k-menu' ).length != 0 )
			return false;
		
		  
		$tp.find( '.' + _this.playerClass ).prepend(
			_this.components[ 'options_menu' ].o( _this )
		);		
		
		
		// By default its hidden:
  		$tp.find( '.k-menu' ).hide();
  			
  		// Output menu-items: 
  		for ( i = 0; i < _this.menu_items.length ; i++ ) {
	        $tp.find( '.k-' +  _this.menu_items[i] + '-btn' ).click( function() {
	        	var mk = $j( this ).attr( 'rel' );
	        	$target = $j( '#' + embedObj.id  + ' .menu-' + mk ).hide();
	        	// Generate the menu html not already done:
	        	if ( $target.children().length == 0 ) {
					// call the function show{Menuitem} with target:
					_this.showMenuItem(	mk );        								
	        	}
	        	
	        	// Slide out the others 
	        	$j( '#' + embedObj.id  + ' .menu-screen' ).hide();
	        	$target.fadeIn( "fast" );
	        	 
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
		//handle special k-skin specific display; 
		if( menu_item == 'credits'){
			this.showCredits(); 
		}else{
			//call the base embedObj "show{Item}"
			this.embedObj['show' + menu_item.charAt( 0 ).toUpperCase() + menu_item.substring( 1 )](
				$j( '#' + this.embedObj.id + ' .menu-' + menu_item )
			);
		}
	},	
	
	/**
	* Do the credit screen (presently specific to kaltura skin:)
	*/  
	showCredits: function() {
		//set up the shortcuts:	
		embedObj = this.embedObj;
		var _this = this;	
		$target = $j( '#' + embedObj.id + ' .menu-credits' );

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
		
		if( !embedObj.wikiTitleKey ){
			$target.find('.credits_box').text(
				'Error: no title key to grab credits with' 
			);
			return ;
		}
				
		// Do the api request to populate the credits via the wikiTitleKey ( tied to "commons" )
		var request = {
			// Normalize the File NS (ie sometimes its present in wikiTitleKey other times not
			'titles' : 'File:' + embedObj.wikiTitleKey.replace(/File:|Image:/, '' ),
		    'prop' : 'revisions',
		    'rvprop' : 'content'
		};		
		var req_categories = new Array();
	    mw.getJSON( mw.commons_api_url, request, function( data ) {
			if( !data || !data.query || !data.query.pages ){
				$target.find('.credits_box').text(
					'Error: title key: ' + embedObj.wikiTitleKey + ' not found' 
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
		var embedObj = this.embedObj;
		
		// Get the title str 
		var titleStr = embedObj.wikiTitleKey.replace(/_/g, ' ');
		var titleLink = 'http://commons.wikimedia.org/wiki/File:' + embedObj.wikiTitleKey;
		
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
						'src' : embedObj.thumbnail						
					} ).css( {
						'width' : imgWidth,
						'height': parseInt( imgWidth * ( embedObj.height / embedObj.width ) )
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
