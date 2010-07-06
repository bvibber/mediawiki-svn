
//Wrap in mw closure to avoid global leakage
( function( mw ) {
	
mw.SequenceEditTimeline = function( sequenceEdit ) {
	return this.init( sequenceEdit );
};

// Set up the mvSequencer object
mw.SequenceEditTimeline.prototype = {
	// Lazy init $timelineTracksContainer
	$timelineTracksContainer : null,
	
	// store a pointer to the track layout
	trackLayout: null,	
	
	//Default height width of timeline clip:
	timelineThumbSize: {
		'height': 90,
		'width' : 120
	},
	
	init: function( sequenceEdit ){
		this.sequenceEdit = sequenceEdit;
	},

	getTimelineContainer: function(){
		return this.sequenceEdit.getContainer().find('.mwseq-timeline');
	},
	
	getTracksContainer: function(){
		if( ! this.$timelineTracksContainer ){
			// getTimelineContainer 
			this.getTimelineContainer().append( 
				$j('<div />')
				.addClass('timelineTrackContainer')		
				.append( 
					$j('<div />')
					.addClass( 'ui-layout-west trackNamesContainer'),
					
					$j('<div />')
					.addClass( 'ui-layout-center trackClipsContainer')
				)
			)
			// Apply layout control to track name / trackClips division  			
			this.$timelineTracksContainer = this.getTimelineContainer().find( '.timelineTrackContainer');
			this.trackLayout = this.$timelineTracksContainer
				.layout( {
					'applyDefaultStyles': true,		
					'west__size' : 150,
					'west__minSize' : 100,
					'west__maxSize' : 300
				} );
		}
		return this.$timelineTracksContainer;
	},
	resizeTimeline: function(){
		if( this.trackLayout ){
			this.trackLayout.resizeAll();
		}
	},
	//draw the timeline
	drawTimeline: function(){		
		// Empty the timeline container 
		this.getTimelineContainer().empty();
		
		// Get the top level sequence tracks 
		var seqTracks = this.sequenceEdit.getSmil().getBody().getSeqElements();		
		var trackType = 'video'; 
		// for now just two tracks first is video second is audio 
		for( var trackIndex=0; trackIndex < seqTracks.length; trackIndex++){
			
			if( trackType == 'audio' ){
				mw.log("SequenceEditTimeline::Error only two tracks presently suppoted");
				break;
			}
			// Draw the sequence track
			this.drawSequenceTrack( trackIndex, seqTracks[ trackIndex ], trackType);
			trackType = 'audio';	
		}	
	},
	
	drawSequenceTrack: function( trackIndex, sequenceNode, trackType ){		
		mw.log(" drawSequenceTrack: Track inx: " + trackIndex + ' trackType:' + trackType );
		// Check if we already have a container for this track set		
			
		// Add a sequence track Name			
		this.getTracksContainer().find('.trackNamesContainer').append( 
			this.getTrackNameInterface( trackIndex, sequenceNode, trackType )
		)
		
		// Add Sequence clips
		this.getTracksContainer().find('.trackClipsContainer').append( 
			this.getTrackClipInterface( trackIndex ,sequenceNode , trackType )
		)
		// Load and display all clip thumbnails 		
	},
		
	/**
	 * Get Track Clip Interface
	 */
	getTrackClipInterface: function( trackIndex, sequenceNode, trackType ){
		var _this = this;
		// setup a local pointer to the smil engine: 
		var smil = this.sequenceEdit.getSmil();
		// Get all the refs that are children of the sequenceNode with associated offsets and durations
		// for now assume all tracks start at zero:
		var startOffset = 0;
		var $trackClips = 
			$j('<div />')
			.attr('id', this.sequenceEdit.getId() + '_trackClips_' + trackIndex )
			.addClass('trackClips ui-corner-all');
		
		smil.getBody().getRefElementsRecurse( sequenceNode, startOffset, function( $node ){
			// Draw the node onto the timeline:
			
			// xxx would be good to support both "storyboard" and "timeline" view modes. 
			// for now just "storyboard"
			
			// add a clip float left box container
			$trackClips.append( 
				$j('<div />')
				.attr('id',  _this.getTimelineClipId( $node ) )
				.data('smilId', $node.attr('id'))
				.addClass('timelineClip ui-corner-all')
				.css( _this.timelineThumbSize )
				.loadingSpinner()			
				.click(function(){
					//Add clip to selection
					_this.handleMultiSelect( this );
				})
				.draggable( {
					axis:'x',
					containment:'#' + _this.sequenceEdit.getId() + '_trackClips_' + trackIndex,
					opacity:50,					
					//handle: ":not(.clip_control)",				
					scroll:true,
					drag:function( e, ui ) {						
						// debugger;
						//insert_key = _this.clipDragUpdate( ui, this );
					},
					start:function( e, ui ) {
						mw.log( 'start drag:' + this.id );
						// make sure we are ontop
						$j( this ).css( { top:'0px', zindex:10 } );
					},
					stop:function( e, ui ) {
						mw.log("stop drag");
						$j( this ).css( { top:'0px', zindex:0 } );
						// switch dom order
					}
				} )
			)
				
			
			// Check Buffer for when the first frame of the smilNode can be grabbed: 		
			smil.getBuffer().canGrabRelativeTime( $node, 0, function(){
				mw.log("getTrackClipInterface::canGrabRelativeTime for " + smil.getAssetId( $node ));
				_this.drawClipThumb( $node , 0);
			});
		})
		
		// Add global TrackClipInterface bindings:
		var keyBindings = this.sequenceEdit.getKeyBindings();		 
		keyBindings.bindEvent({
			'escape': function(){
				_this.getTimelineContainer().find( '.selectedClip' ).removeClass( 'selectedClip' );
			},
			'delete': function(){
				_this.removeSelectedClips();
			}
		})
		return $trackClips;
	},
	
	/**
	 * Remove selected clips and update the smil player
	 */
	removeSelectedClips: function(){
		var smil = this.sequenceEdit.getSmil();
		// modify the smil.dom and rebuild
		this.getTimelineContainer().find( '.selectedClip' ).each(function( inx, selectedClip ){
			// Remove from smil dom:
			smil.removeById( $j(selectedClip).data('smilId') );
			// Remove from timeline dom: 
			$j( selectedClip ).remove();			
		})		
		// Invalidate embedPlayer duration 
		this.sequenceEdit.getEmbedPlayer().duration = null;
		// Rebuild the smil duration:
		smil.getDuration( true );
		// Update the time display / stop playback if playing  
		this.sequenceEdit.getEmbedPlayer().stop();
	},
	
	/**
	 * Handle multiple selections based on what clips was just "cliked" 
	 */
	handleMultiSelect: function( clickClip ){
		var keyBindings = this.sequenceEdit.getKeyBindings();
		var $target = this.getTimelineContainer();
		var smil = this.sequenceEdit.getSmil();
		var embedPlayer = this.sequenceEdit.getEmbedPlayer();
		
		
		// Add the selectedClip class to the clickClip
		if( $j( clickClip ).hasClass( 'selectedClip') && $target.find( '.selectedClip' ).length == 1 ){
			$j( clickClip ).removeClass( 'selectedClip' );
		}else {
			$j( clickClip ).addClass( 'selectedClip' );
		}
		
		// If not in multi select mode remove all existing selections except for clickClip
		mw.log( ' HandleMultiSelect::' + keyBindings.shiftDown + '  ctrl_down:' + keyBindings.ctrlDown );
		
		if ( ! keyBindings.shiftDown && ! keyBindings.ctrlDown ) {
			$target.find( '.selectedClip' ).each( function( inx, selectedClip ) {
				if( $j( clickClip ).attr('id') != $j( selectedClip ).attr('id') ){
					$j( selectedClip ).removeClass('selectedClip');
				}	
			} );
		}
		// Seek to the current clip time ( startOffset of current )
		var seekTime = smil.$dom.find('#' + $j( clickClip ).data('smilId') ).data( 'startOffset' )
		embedPlayer.setCurrentTime( seekTime, function(){
			mw.log("handleMultiSelect::seek done")
		});
		
		// if shift select is down select the in-between clips
		if( keyBindings.shiftDown ){
			// get the min max of current selection (within the current track)
			var max_order = 0;
			var min_order = 999999999;
			$target.find( '.timelineClip' ).each( function( inx, curClip) {	
				if( $j(curClip).hasClass('selectedClip') ){
					// Set min max
					if ( inx < min_order )
						min_order = inx;
					if ( inx > max_order )
						max_order = inx;
				}
			} );
			// select all non-selected between max or min
			$target.find( '.timelineClip' ).each( function( inx, curClip) {	
				if( inx > min_order && inx < max_order ){
					$j(curClip).addClass( 'selectedClip')
				}
			});	
		}		
	},
	
	getTimelineClipId: function( $node ){
		return this.sequenceEdit.getSmil().getAssetId( $node ) + '_timelineClip';
	},
	
	// Draw a clip thumb into the timeline clip target
	drawClipThumb: function ( $node , relativeTime ){		
		var _this = this;
		var smil = this.sequenceEdit.getSmil();
		// Check the display type: 
		smil.getBuffer().canGrabRelativeTime( $node, relativeTime, function(){
			mw.log("drawClipThumb:: canGrabRelativeTime:" + _this.getTimelineClipId( $node ));

			var naturaSize = {};
			
			var drawElement = $j( '#' + smil.getAssetId( $node ) ).get(0);
			
			if(  drawElement.nodeName.toLowerCase() == 'img' ){
				naturaSize.height = drawElement.naturalHeight;
				naturaSize.width = drawElement.naturalWidth;
			} else if( drawElement.nodeName.toLowerCase() == 'video' ){
				naturaSize.height = drawElement.videoHeight;
				naturaSize.width = drawElement.videoWidth;
			}
			
			// Draw the thumb via canvas grab
			// NOTE I attempted to scale down the image using canvas but failed 
			// xxx should revisit thumb size issue:
			$j( '#' + _this.getTimelineClipId( $node ) ).html(
				$j('<canvas />')				
				.attr({
					height: naturaSize.height,
					width : naturaSize.width
				}).css( {
					height:'100%',
					widht:'100%'
				})
				.addClass("ui-corner-all")
			)
			.find( 'canvas')
			.get(0)	
			.getContext('2d')
			.drawImage( $j( '#' + smil.getAssetId( $node ) ).get(0), 0, 0)
		})		
	},
	/**
	 * Gets an sequence track control interface 
	 * 	features to add :: expand collapse, hide, mute etc. 
	 * 	for now just audio or video with icon 
	 */
	getTrackNameInterface: function( trackIndex,  sequenceNode, trackType ){				
		var $trackNameInterface = 					
			$j('<a />')
			.attr('href','#')
			.addClass( "ui-icon_link" );
		if( trackType == 'video'){
			$trackNameInterface.append( 				
				$j('<span />').addClass( 'ui-icon ui-icon-video'),
				$j('<span />').text( gM( 'mwe-sequenceedit-video-track' ) )
			)
		} else {
			$trackNameInterface.append( 				
				$j('<span />').addClass( 'ui-icon ui-icon-volume-on'),
				$j('<span />').text( gM( 'mwe-sequenceedit-audio-track' ) )
			)
		}
		// Wrap the track name in a box that matches the trackNames 
		return $j('<div />')
				.attr('id', this.sequenceEdit.getId() + '_trackName_' + trackIndex)
				.addClass('trackNames ui-corner-all')
				.append(
					$trackNameInterface
				)
	},
	
	getSequenceTrackTitle: function( sequenceNode ){
		if( $j( sequenceNode).attr('title') ){
			return $j( sequenceNode).attr('title');
		} 
		// Else return an empty string ( for now )
		return ''
	},
	
	getSequenceTrackId: function( index, sequenceNode ){
		if( ! $j( sequenceNode ).data('id') ){
			$j( sequenceNode ).data('id', this.sequenceEdit.getId() + '_sequenceTrack_' + index );
		}
		return  $j( sequenceNode ).data('id');		
	}
}
	
	
	
	
	
	
} )( window.mw );	