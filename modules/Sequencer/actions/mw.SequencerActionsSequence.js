/**
 * Handles dialogs for sequence actions such as 
 * 	"save sequence",
 * 	"rename", 
 * 	"publish"
 *  
 * Hooks into sequencerApiProvider to run the actual api operations  
 */

mw.SequencerActionsSequence = function( sequencer ) {
	return this.init( sequencer );
};

mw.SequencerActionsSequence.prototype = {
	init: function( sequencer ) {
		this.sequencer = sequencer; 
	},	
	save: function(){
		var _this = this;
		// Check if we have an api provider defined
		if( ! this.sequencer.getServer().exists() ){
			mw.addDialog( gM('mwe-sequencer-no-server-defined') )
			return ;
		}
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-loading_user_rights'),			
			'content' : gM('mwe-sequencer-loading_user_rights'),
			'width' : 450
		});
			
		// Check if we can save 
		this.sequencer.getServer().userCanSave( function( canSave ){
			if( canSave === false ){
				$dialog.html( gM( 'mwe-sequencer-no_edit_permissions') );
				// Add close text
				$dialog.dialog( "option", "closeText", gM('mwe-ok') );
				return ;
			}
			_this.showSaveDialog( $dialog );
		});
	},
	showSaveDialog: function( $dialog ){
		var _this = this;
		// Else user 'can save' present a summary text box
		var saveDialogButtons = {	};
		saveDialogButtons[ gM('mwe-sequencer-edit_save') ] = function(){
			$dialog.empty().append(
				gM('mwe-sequencer-saving_wait' ),
				$j('<div />').loadingSpinner()
			);
			_this.doSaveWithSummary( $dialog.find('.saveSummary').val(), function( status, errorMsg ){
				if( status === false ){
					$dialog.text( errorMsg )
				} else {
					// save success
					$dialog.text( gM( 'mwe-sequencer-save_done' ) )										
				}
				// Only let the user hit 'ok'
				var closeButton = {};
				closeButton[gM('mwe-ok')]= function(){ $j(this).dialog('close') };
				$dialog.dialog( "option", "buttons", closeButton);	
			});				
		};
		saveDialogButtons[ gM('mwe-sequencer-edit_cancel') ] = function(){
			$dialog.dialog('close');
		};		            
		$dialog.empty().append(
			gM('mwe-sequencer-save-summary' ),
			$j('<input />')								
				.css({'width': 400 })
				.addClass( 'saveSummary' )
				.attr({						
					'maxlength': 255 
				})
		)
		.dialog( "option", "buttons", saveDialogButtons );
	},
	doSaveWithSummary : function( summary, callback ){
		this.sequencer.getServer().save( 
			summary, 
			this.sequencer.getSmil().getXMLString(), 
			callback 
		);
	},
	/**
	 * Display the publish dialog 
	 * ( confirm the user has firefogg and rights to save a new version of the file )
	 */
	publish: function(){
		var _this = this;
		// add a loading dialog
		var $dialog = mw.addDialog({
			'resizable':'true',
			'title' : gM('mwe-sequencer-loading-publish-render'),			
			'content' : gM('mwe-sequencer-loading-publish-render'),
			'width' : 450,
			'height' : 400
		});
		$dialog.append( $j('<div />').loadingSpinner() );
		// Grab the firefogg render
		mw.load( ['AddMedia.firefogg','FirefoggRender'], function(){
			// Get a Firefogg object to check if firefogg is installed
			var myFogg = new mw.Firefogg( {
				'only_fogg':true
			});			
			if ( !myFogg.getFirefogg() ) {
				$dialog.empty().append(
					$j('<div />').attr('id', 'show_install_firefogg') 
				);
				myFogg.showInstallFirefog( '#show_install_firefogg' );				
				return ;
			}
						
			// Build a data-url of the current sequence:
			$dialog.dialog( "option", "title", gM('mwe-sequencer-running-publish') );
			
			$dialog.empty().append(
				$j( '<video />' )
				.attr({
					'id': 'publishVideoTarget',
					'src' : _this.sequencer.getDataUrl(),
					'type' : 'application/smil'
				})
				.css({
					'width' : '400px',
					'height' : '300px'
				})
				,
				$j('<div />' )
				.css( 'clear', 'both' ),
				$j('<span />' ).text( gM( 'mwe-sequencer-publishing-status') ),	
				$j('<span />' ).attr( 'id', 'firefoggStatusTarget' ),
				$j('<span />')
				.css('float', 'right')
				.text("%")
			);
			
			// Embed the player and continue application flow			
			$j('#publishVideoTarget').embedPlayer({
				'controls' : false
			}, function(){
				// this should be depreciated ( hidden interface bug in mwEmbed ) 
				$j('#publishVideoTarget').parent().show();
				// Start up the render
				var foggRender = $j('#publishVideoTarget').firefoggRender({
					'statusTarget' : '#firefoggStatusTarget',
					'saveToLocalFile' : false,
					'doneRenderCallback': function( fogg ){
						_this.uploadRenderedVideo( $dialog, fogg );
					}
				});
				var buttons = {};
				buttons[ gM('mwe-cancel') ] = function(){
					foggRender.stopRender();
					$j( this ).dialog( 'close' );
				}
				// Add cancel button 
				$dialog.dialog( "option", "buttons", buttons );	
				foggRender.doRender();
			});		
		});
	},
	// Upload the video from a supplied fogg target 
	// note xx this might be better handlded in a firefogg library  
	// @param {jQuery Object } $dialog
	// @param {firefogg Object} 
	uploadRenderedVideo: function( $dialog, fogg ){
		var uploadStatus = function(){
		}
		this.sequencer.getServer().getVideoUploadSettings( function( url, request ){
			fogg.post( url, 'file', request )
		})		
	}
}