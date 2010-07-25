// Wrap in mw closure to avoid global leakage
( function( mw ) {
	
mw.SequenceEditMenu = function( sequenceEdit ) {
	return this.init( sequenceEdit );
};

// Set up the mvSequencer object
mw.SequenceEditMenu.prototype = {
		
	init: function( sequenceEdit ){
		this.sequenceEdit = sequenceEdit;
	},
	
	// menuConfig system uses auto-defined msg keys
	// ie "new" mwe-sequenceedit-menu-file-new
	menuConfig : {
		'file': {
			'menuItems':{
				'new': {
					'shortCut': 'ctrl N',			
					'action' : function( _this ){
						mw.log("SequenceEditMenu::new sequence");
						_this.sequenceEdit.getFileActions().newSequence();
					}
					
				},
				'open': {
					'icon' : 'folder-open',
					'shortCut' : 'ctrl O',
					'action' : function( _this ){
					mw.log("SequenceEditMenu::open");
						_this.sequenceEdit.getFileActions().open();
					}
				},
				'divider': true,
				'save' : {
					'icon' : 'disk',
					'shortCut' : 'ctrl S',
					'action' : function( _this ){
						mw.log("SequenceEditMenu::save");
						_this.sequenceEdit.getFileActions().save();
					}
				}
			}
		},
		'edit':{
			'menuItems': {
				'undo': {
					'shortCut' : 'ctrl Z',
					'action': function( _this ){
						mw.log("SequenceEditMenu::undo");
					}
				},
				'redo' : {
					'shortCut' : 'ctrl Y',
					'action' : function( _this ){
						mw.log("SequenceEditMenu::redo");
					}
				}
			}
		},
		'view': {
			'menuItems': {
				'history': {					
					'action':function( _this ){
						mw.log("SequenceEditMenu::history");
					}						
				}
			}
		}
	},
	/**
	 * Draw the sequence menu
	 */
	drawMenu:function(){
		var _this = this;
		var $menuTarget = this.sequenceEdit.getMenuTarget();	
		$menuTarget.empty();

		for( var menu in this.menuConfig ){
			// Build out the ul for the given menu
			$menu = $j( '<ul>' );
			$menu.attr('title', gM('mwe-sequenceedit-menu-' + menu ) );
			for( var menuItemKey in this.menuConfig[ menu ]['menuItems'] ){
				var menuItem = this.menuConfig[ menu ]['menuItems'][ menuItemKey ];
				$menu.append(
					$j.getLineItem( 
						gM('mwe-sequenceedit-menu-' + menu + '-' + menuItemKey ),
						menuItem.icon, 
						menuItem.action
					)
				)
			}
		}
		
		
		
		if( false ){
			$menuTarget.append(
				$j.button({
					'text' : gM('mwe-sequenceedit-save-sequence'),
					'icon_id': 'disk'
				})
				.buttonHover()
				.click(function(){
					_this.sequenceEdit.getFileActions().save();
				})
			)
		}
		
		if( true ){
			$menuTarget.append(
				$j.button({
					'text' : gM('mwe-sequenceedit-view-sequence-xml'),
					'icon_id': 'disk'
				})
				.buttonHover()
				.click(function(){
					_this.sequenceEdit.getFileActions().viewXML();
				})
			)
		}
		
		// check if we should have a render button
		if( true ){
			$menuTarget.append(
				$j.button({
					'text' : gM('mwe-sequenceedit-render-sequence'),
					'icon_id': 'video'
				})
				.buttonHover()
				.click(function(){
					_this.sequenceEdit.getRender().renderDialog();
				})
			)
		}
		
		// check if we should include credits
		if( mw.getConfig( 'SequenceEdit.KalturaAttribution' ) ){
			if( true ){
				$menuTarget.append(
					$j('<span />')
					.css( 'float', 'right' )
					.append( 
						gM('mwe-sequenceedit-sequencer_credit_line',
							'http://kaltura.com',
							'http://wikimedia.org'
						)
					)
				)
			}
		}
	}
	
};

} )( window.mw );