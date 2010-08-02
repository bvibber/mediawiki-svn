// Wrap in mw closure to avoid global leakage
( function( mw ) {
	
mw.SequencerMenu = function( sequencer ) {
	return this.init( sequencer );
};

// Set up the mvSequencer object
mw.SequencerMenu.prototype = {
		
	init: function( sequencer ){
		this.sequencer = sequencer;
	},
	
	// menuConfig system uses auto-defined msg keys
	// ie "new" mwe-sequencer-menu-file-new
	menuConfig : {
		'sequence': {
			'new': {
				'icon' : 'document',
				'shortCut': 'ctrl N',			
				'action' : function( _this ){
					mw.log("SequencerMenu::new sequence");
					_this.sequencer.getActionsSequence().newSequence();
				}
				
			},
			'open': {
				'icon' : 'folder-open',
				'shortCut' : 'ctrl O',
				'action' : function( _this ){
					mw.log("SequencerMenu::open");
					_this.sequencer.getActionsSequence().open();
				}
			},
			'divider': true,
			'save' : {
				'icon' : 'disk',
				'shortCut' : 'ctrl S',
				'action' : function( _this ){
					mw.log("SequencerMenu::save");
					_this.sequencer.getActionsSequence().save();
				}
			},
			'renderdisk' : {
				'icon' : 'gear',
				'action' : function( _this ){
					_this.sequencer.getRender().renderDialog();
				}
			}
		},
		'edit':{
			'undo': {
				'shortCut' : 'ctrl Z',
				'disabled' : true,
				'icon' : 'arrowreturnthick-1-w',
				'action': function( _this ){
					_this.sequencer.getActionsEdit().undo();
				}
			},
			'redo' : {
				'shortCut' : 'ctrl Y',
				'disabled' : true,
				'icon' : 'arrowreturnthick-1-e',
				'action' : function( _this ){						
					_this.sequencer.getActionsEdit().redo();
				}
			},
			'divider': true,
			'selectall': {
				'action' : function( _this ){
					mw.log("SequencerMenu::selectall");
					_this.sequencer.getActionsEdit().selectAll();
				}
			}
		},
		'view': {
			'smilxml': {
				'icon' : 'script',
				'action': function( _this ){
					_this.sequencer.getActionsView().viewXML();
				}
			},
			'history': {	
				'icon' : 'clock',
				'action':function( _this ){
					mw.log("SequencerMenu::history");
				}						
			}
		}
	},
	/**
	 * Draw the sequence menu
	 */
	drawMenu:function(){
		var _this = this;
		var $menuTarget = this.sequencer.getMenuTarget();	
		$menuTarget.empty();

		for( var topMenuKey in this.menuConfig ){				
			// Create a function to preserve topMenuKey binding scope
			function drawTopMenu( topMenuKey ){
				// Add the menu target		
				$menuTarget
				.append( 
					$j('<span />')
					.html( gM('mwe-sequencer-menu-' + topMenuKey )  )
					.css({
						'padding': '7px',
						'cursor' : 'default'
					})
					.attr( 'id', _this.sequencer.id + '_' + topMenuKey + '_topMenuItem')
					.addClass( 'ui-state-default' )
					.buttonHover()
			    	// Add menu binding: 
			    	.menu({
						content: _this.getMenuSet( _this.menuConfig, topMenuKey ),
						showSpeed: 100 
					})
					// should fire post jquery.fn.menu() mousedown binding ::
					.mousedown(function(){
						// sync the disabled enabled state to menu
						for( var menuItemKey in _this.menuConfig[ topMenuKey ] ){
							var $menuItem = $j( '#' + _this.getMenuItemId( topMenuKey, menuItemKey ) );						
							var isDisabled = _this.menuConfig[ topMenuKey ][menuItemKey].disabled;						
							if( $menuItem.hasClass( 'disabled') ){
								if( ! isDisabled ){
									$menuItem.removeClass( 'disabled' )
								}
							} else {
								if( isDisabled ){
									$menuItem.addClass( 'disabled' );
								}
							}
						}
					})
				)
			}
			drawTopMenu( topMenuKey );
		}		
		
		// Check if we should include kaltura credits
		if( mw.getConfig( 'Sequencer.KalturaAttribution' ) ){
			$menuTarget.append(
				$j('<span />')
				.css({ 
					'float': 'right',
					'font-size': '12px'
				})
				.append( 
					gM('mwe-sequencer-sequencer_credit_line',
						'http://kaltura.com',
						'http://wikimedia.org'
					)
				)
			)
		}
	},
	/* return a top menuItem with all its associated menuItems */
	getMenuSet: function( menuConfig, menuKey ){
		var _this = this;
		// Build out the ul for the given menu
		var $menu = $j( '<ul />' )
			.attr({
				'id' : _this.sequencer.id + '_' + menuKey + '_content',
				'title' : gM('mwe-sequencer-menu-' + menuKey ) 
			})
			.addClass('sequencer-menu');
		for( var menuItemKey in menuConfig[ menuKey ] ){
			// Check for special divider key
			if( menuItemKey == 'divider'){
				$menu.append(
					$j('<li />')
					.addClass('divider')
					.append( $j('<hr />').css('width', '80%') )
				);
				continue;
			}			
			$menu.append(				
				_this.getMenuItem( menuKey, menuItemKey )
			)
		}
		return $menu;
	},
	// Get menu item 
	getMenuItem: function( menuKey, menuItemKey ){
		var _this = this;
		var menuItem = this.menuConfig[ menuKey ][ menuItemKey ];
		
		var $li = $j.getLineItem( 
			gM('mwe-sequencer-menu-' + menuKey + '-' + menuItemKey ),
			menuItem.icon, 
			function(){
				if( typeof menuItem.action == 'function'){
					menuItem.action( _this );
					return ;
				}
				mw.log( "Error:: SequencerMenu:: no action item for " + menuKey + '-' + menuItemKey );
			}
		);
		
		// Set the ID for easy reference
		$li.attr( 'id',  _this.getMenuItemId( menuKey, menuItemKey ) )
		
		// Set the tooltip / title if provided
		if( mw.Language.isMsgKeyDefined( 'mwe-sequencer-menu-' + menuKey + '-' + menuItemKey + '-desc' ) ){
			$li.attr( 'title', gM('mwe-sequencer-menu-' + menuKey + '-' + menuItemKey + '-desc') )
		}
		
		return $li;
	},
	
	disableMenuItem: function( menuKey, menuItemKey ){
		
		this.menuConfig[ menuKey ][ menuItemKey ].disabled = true;		
		$menuItemTarget = $j('#' + this.getMenuItemId(menuKey, menuItemKey ) );		
		if( $menuItemTarget.length && ! $menuItemTarget.hasClass( 'disabled' ) ){
			$menuItemTarget.addClass( 'disabled' );
		}

		/*this.menuConfig[ menuKey ][ menuItemKey ].enabled = false;
		
		$j('#' + this.getMenuItemId(menuKey, menuItemKey ) )
		// remove menu role
		.attr( 'role', '')
		// Set to faded out
		.css({
			'opacity' : .5
		})*/
		
	},
	
	enableMenuItem: function( menuKey, menuItemKey ){
		this.menuConfig[ menuKey ][ menuItemKey ].disabled = false;
		alert( 'EnableMenuItem' + menuKey + ' key:' + menuItemKey );
		$menuItemTarget = $j('#' + this.getMenuItemId( menuKey, menuItemKey ) );
		if( $menuItemTarget.hasClass( 'disabled' ) ){
			$menuItemTarget.removeClass( 'disabled' );
		}
		
		/*
		$j('#' + this.getMenuItemId(menuKey, menuItemKey ) )
		// remove menu role
		.attr( 'role', 'menuitem')
		// Set to faded out
		.css({
			'opacity' : 1
		})*/
	},
	
	getMenuItemId: function( menuKey, menuItemKey ){
		return 'menuItem_' + this.sequencer.id + '_' + menuKey + '_' + menuItemKey;
	}
};

} )( window.mw );