/**
 * Handles the "tools" window top level component driver 
 */

//Wrap in mw closure to avoid global leakage
( function( mw ) {
	
mw.SequencerTools = function( sequencer ) {
	return this.init( sequencer );
};

// Set up the mvSequencer object
mw.SequencerTools.prototype = {
	init: function(	sequencer ){
		this.sequencer = sequencer;
	},
	// The current selected tool
	currentToolId: null,
	
	// JSON tools config
	tools:{
		'trim':{
			'editWidgets' : [ 'trimTimeline' ], 
			'editableAttributes' : ['clipBegin','dur' ],			
			'contentTypes': ['video', 'audio']
		},
		'duration':{			 
			'editableAttributes' : [ 'dur' ],
			'contentTypes': ['image']
		},
		'panzoom' : {
			'editableAttributes' : [ 'panZoom' ],
			'contentTypes': ['video', 'image']
		}
	},
	editableAttributes:{
		'clipBegin':{
			'type': 'time',
			'title' : gM('mwe-sequencer-start-time' )		
		},
		'dur' :{
			'type': 'time',
			'title' : gM('mwe-sequencer-clip-duration' )
		},
		'panZoom' :{
			'type' : 'panzoom',
			'title' : gM('mwe-sequencer-clip-layout' )
		}
	},
	editableTypes: {
		'time' : {
			update : function( _this, smilClip, attributeName, value){
				// Validate time
				var seconds = _this.sequencer.getSmil().parseTime( value );
				$j( smilClip ).attr( attributeName, mw.seconds2npt( seconds ) );
				// Update the clip duration :
				_this.sequencer.getEmbedPlayer().getDuration( true );
				
				// Seek to "this clip" 
				_this.sequencer.getEmbedPlayer().setCurrentTime( 
					$j( smilClip ).data('startOffset')
				);								
			},			
			getSmilVal : function( _this, smilClip, attributeName ){
				var smil = _this.sequencer.getSmil();	
				return mw.seconds2npt( 
						smil.parseTime( 
							$j( smilClip ).attr( attributeName ) 
						)
					);
			}
		}
	},
	editActions: {
		'preview' : {
			'icon' : 'play',
			'title' : gM('mwe-sequencer-preview'),
			'action': function( _this, smilClip ){				
				_this.sequencer.getPlayer().previewClip( smilClip );
				// xxx todo  update preview button to "pause" / "play" 
			}
		},
		'cancel':{
			'icon': 'close',
			'title' : gM('mwe-cancel'),
			'action' : function( _this, smilClip ){
				$j.each( 
					_this.getToolSet( 
						_this.sequencer.getSmil().getRefType( smilClip ) 
					), 
					function( inx, toolId ){
						var tool = _this.tools[toolId];
						for( var i=0; i < tool.editableAttributes.length ; i++ ){
							var attributeName = tool.editableAttributes[i]; 
							var $editToolInput = $j('#' + _this.getEditToolInputId( toolId, attributeName ) );  					
							// Restore all original attribute values
							smilClip.attr( attributeName, $editToolInput.data('initialValue') );
						}				
					}
				);
								
				// Update the clip duration :
				_this.sequencer.getEmbedPlayer().getDuration( true );
				
				// Update the embed player
				_this.sequencer.getEmbedPlayer().setCurrentTime( 
					$j( smilClip ).data('startOffset')
				);

				// Close / empty the toolWindow
				_this.setDefaultText();
			}
		}
	},
	editWidgets: {
		'trimTimeline':{
			'onChange': function( _this, target, smilClip ){				
				var smil = _this.sequencer.getSmil();
				// Update the preview thumbs
				
				// (local function so it can be updated after the start time is done with its draw ) 
				var updateDurationThumb = function(){
					// Check the duration:
					var clipDur = $j('#editTool_trim_dur').val();
					if( clipDur ){
						// Render a thumbnail for the updated duration  
						smil.getLayout().drawElementThumb( 
							$j( target ).find('.trimEndThumb'),
							smilClip,
							clipDur
						);
					}
				}
				
				var clipBeginTime = $j('#editTool_trim_clipBegin').val();
				if( !clipBeginTime ){
					$j(target).find('.trimStartThumb').hide();
				} else {
					mw.log("Should update trimStartThumb::" +  $j(smilClip).attr('clipBegin') );
					// Render a thumbnail for relative start time = 0  
					smil.getLayout().drawElementThumb( 
						$j( target ).find('.trimStartThumb'), 
						smilClip, 
						0,
						updateDurationThumb()
					)
				}
			},
			// Return the trimTimeline edit widget
			'draw': function( _this, target, smilClip ){
				var smil = _this.sequencer.getSmil();
				// check if thumbs are supported 
				if( _this.sequencer.getSmil().getRefType( smilClip ) == 'video' ){ 
					$j(target).append(
						$j('<div />')					
						.addClass( 'trimStartThumb ui-corner-all' ),					
						$j('<div />')					
						.addClass( 'trimEndThumb ui-corner-all' ),
						$j('<div />').addClass('ui-helper-clearfix') 
					)			
				}
				
				// Add a trim binding: 
				$j('#editTool_trim_clipBegin,#editTool_trim_dur').change(function(){
					_this.editWidgets.trimTimeline.onChange( _this, target, smilClip);
				})
				// Update the thumbnails:
				_this.editWidgets.trimTimeline.onChange( _this, target, smilClip);
				
				// Get the clip full duration to build out the timeline selector
				smil.getBody().getClipAssetDuration( smilClip, function( fullClipDuration ) {
					
					var sliderToTime = function( sliderval ){
						return parseInt( fullClipDuration * ( sliderval / 1000 ) );
					}
					var timeToSlider = function( time ){
						return parseInt( ( time / fullClipDuration ) * 1000 );
					}
					var startSlider = timeToSlider( smil.parseTime( $j('#editTool_trim_clipBegin').val() ) );
					var sliderValues = [
					    startSlider,
					    startSlider + timeToSlider( smil.parseTime( $j('#editTool_trim_dur').val() ) )
					];								
					// Return a trim tool binded to smilClip id update value events. 
					$j(target).append(
						$j('<div />')
						.attr( 'id', _this.sequencer.id + '_trimTimeline' )
						.css({
							'left' : '5px',
							'right' : '15px',
							'margin': '5px'
						})
						.slider({
							range: true,
							min: 0,
							max: 1000,
							values: sliderValues,
							slide: function(event, ui) {															
								$j('#editTool_trim_clipBegin').val( 
									mw.seconds2npt( sliderToTime( ui.values[0] ), true ) 
								);
								$j('#editTool_trim_dur').val(  
									mw.seconds2npt( sliderToTime( ui.values[1] - ui.values[0] ), true )
								);
							},
							change: function( event, ui ) {
								var attributeValue = 0, sliderIndex  = 0;
								
								// Update clipBegin 
								_this.editableTypes['time'].update( _this, smilClip, 'clipBegin',  sliderToTime( ui.values[ 0 ] ) );
								
								// Update dur
								_this.editableTypes['time'].update( _this, smilClip, 'dur',   sliderToTime( ui.values[ 1 ]- ui.values[0] ) );
																				
								// update the widget display
								_this.editWidgets.trimTimeline.onChange( _this, target, smilClip);
								
								// Register the edit state for undo / redo 
								_this.sequencer.getActionsEdit().registerEdit();
								
							}
						})
					);
				});
				// On resize event
				
				// Fill in timeline images
				
			}
		}
	},
	getDefaultText: function(){
		return  gM('mwe-sequencer-no_selected_resource');
	},
	setDefaultText: function(){
		this.sequencer.getEditToolTarget().html(
			this.getDefaultText() 
		)
	},
	getEditToolInputId: function( toolId, attributeName){
		return 'editTool_' + toolId + '_' + attributeName;
	},
	/**
	 * update the current displayed tool ( when an undo, redo or history jump changes smil state ) 
	 */
	updateToolDisplay: function(){
		var _this = this;
		// Update all tool input values:: trigger change event if changed
		var smilClip = this.getCurrentSmilClip();
		
		$j.each( 
			_this.getToolSet( 
				_this.sequencer.getSmil().getRefType( smilClip ) 
			), 
			function( inx, toolId ){
				var tool = _this.tools[toolId];
				for( var i=0; i < tool.editableAttributes.length ; i++ ){
					var attributeName = tool.editableAttributes[i]; 
					var $editToolInput = $j('#' + _this.getEditToolInputId( toolId, attributeName ) );  					
					// Sync with smilClip value 
					if( smilClip.attr( attributeName ) != $editToolInput.val() ){
						$editToolInput.val(  smilClip.attr( attributeName ) );
						// trigger change event: 
						$editToolInput.change();
					}
				}				
			}
		);		
	},
	getToolSet: function( refType ){
		var toolSet = [];
		for( var toolId in this.tools){		
			if( this.tools[toolId].contentTypes){
				if( $j.inArray( refType, this.tools[toolId].contentTypes) != -1 ){
					toolSet.push( toolId );
				}
			}
		}
		return toolSet;
	},
	drawClipEditTools: function( smilClip ){
		var _this = this;
		var toolId = '';
		var $target = this.sequencer.getEditToolTarget();
		
		// Set the current smilClip 
		this.currentSmilClip = smilClip;
		
		
		$target.empty().append(
			$j('<div />')
			.addClass( 'editToolsContainer' )
			.append( 
				$j('<ul />') 
			)
		);
				
		// get the toolId based on what "ref type" smilClip is:		
		$j.each( this.getToolSet(  this.sequencer.getSmil().getRefType( smilClip ) ), function( inx, toolId ){			
				
			var tool = _this.tools[ toolId ];
			
			// set the currentTool if not already set 
			if(!_this.currentToolId){
				_this.currentToolId = toolId;
			}
			
			// Append the title to the ul list
			$target.find( 'ul').append( 
				$j('<li />').append( 
					$j('<a />')
					.attr('href', '#tooltab_' + toolId )
					.text( gM('mwe-sequencer-tools-' + toolId) ) 
				)
			);
			
			// Append the tooltab container
			$target.append(
				$j('<div />')
				.attr('id', 'tooltab_' + toolId )				
			)
			var $toolContainer = $target.find( '#tooltab_' + toolId );
			
			// Build out the attribute list for the given tool: 
			for( var i=0; i < tool.editableAttributes.length ; i++ ){
				attributeName = tool.editableAttributes[i];
				$toolContainer.append(
					_this.getEditableAttribute( smilClip, toolId, attributeName )
				);
			}
			
			// Output a float divider: 
			$toolContainer.append( $j('<div />').addClass('ui-helper-clearfix') );
			
			// Build out tool widgets 
			if( tool.editWidgets ){
				for( var i =0 ; i < tool.editWidgets.length ; i ++ ){
					var editWidgetId = tool.editWidgets[i];
					if( ! _this.editWidgets[editWidgetId] ){
						mw.log("Error: not recogonized widget: " + editWidgetId);
						continue;
					}
					// Append a target for the edit widget:
					$toolContainer.append( 
						$j('<div />')
						.attr('id', 'editWidgets_' + editWidgetId)
					);			
					// Draw the binded widget:
					_this.editWidgets[editWidgetId].draw( 
						_this, 
						$j( '#editWidgets_' + editWidgetId ),
						smilClip
					)
					// Output a float divider: 
					$toolContainer.append( $j('<div />').addClass( 'ui-helper-clearfix' ) );
				}	
			}				
		});
		
		// Add tab bindings
		$target.find('.editToolsContainer').tabs({
			select: function(event, ui) {
				debugger;
			}
		})
		// Build out global edit Actions buttons ( per 'current tool' )		
		for( var editActionId in this.editActions ){		
			$target.append( 
				this.getEditAction( smilClip, editActionId )
			)	
		}
	},
	getCurrentSmilClip: function(){
		return this.currentSmilClip;
	},
	getCurrentToolId: function(){
		return this.currentToolId;
	},
	
	getEditAction: function( smilClip, editActionId ){		
		if(! this.editActions[ editActionId ]){
			mw.log("Error: getEditAction: " + editActionId + ' not found ');
			return ;
		}
		var _this = this;
		var editAction = this.editActions[ editActionId ];
		$actionButton = $j.button({
				icon: editAction.icon, 
				text: editAction.title
			})
			.css({
				'float': 'left',
				'margin': '5px'
			})
			.click( function(){
				editAction.action( _this, smilClip );
			})
		return $actionButton;
	},
	getEditableAttribute: function( smilClip, toolId, attributeName ){
		if( ! this.editableAttributes[ attributeName ] ){
			mw.log("Error: editableAttributes : " + attributeName + ' not found');
			return; 
		}
		var _this = this;
		var editAttribute = this.editableAttributes[ attributeName ];
		var editType = editAttribute.type;
		
		var initialValue =  _this.editableTypes[ editType ].getSmilVal(
			_this, 
			smilClip, 
			attributeName
		);
		return $j( '<div />' )
			.css({
				'float': 'left',
				'font-size': '12px',
				'width': '160px',
				'border': 'solid thin #999',
				'background-color': '#EEE',
				'padding' : '2px',
				'margin' : '5px'
			})
			.addClass('ui-corner-all')
			.append( 
				$j('<span />')
				.css('margin', '5px')
				.text( editAttribute.title ),
				
				$j('<input />')
				.attr( {
					'id' : _this.getEditToolInputId( toolId, attributeName),
					'size': 6
				})
				.data('initialValue', initialValue )
				.sequencerInput( _this.sequencer )
				.val( initialValue )
				.change(function(){					
					// Run the editableType update function: 
					_this.editableTypes[ editType ].update( 
							_this, 
							smilClip, 
							attributeName, 
							$j( this ).val() 
					);				
					// widgets can bind directly to this change action. 					
				})
			);
	}		
}

} )( window.mw );