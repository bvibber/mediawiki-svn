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
	}
}