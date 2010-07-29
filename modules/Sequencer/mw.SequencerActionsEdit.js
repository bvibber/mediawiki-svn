/**
 * Handles dialogs for sequence actions such as 
 * 	"save sequence",
 * 	"rename", 
 * 	"publish"
 *  
 * Hooks into sequencerApiProvider to run the actual api operations  
 */

mw.SequencerActionsEdit = function( sequencer ) {
	return this.init( sequencer );
};

mw.SequencerActionsEdit.prototype = {
	
	// Stores the local edit history to support undo / redo  
	editStack : [],
	
	// Store the edit index  
	editIndex : 0,
	
	// The numbers of undos supported 
	numberOfUndos : mw.getConfig( 'Sequencer.numberOfUndos' ),
	
	init: function( sequencer ) {
		this.sequencer = sequencer; 
		// Set the initial edit state: 
		this.editStack.push(  this.sequencer.getSmil().getXMLString() );
	},	
	
	selectAll: function(){
		//Select all the items in the timeline
		$target = this.sequencer.getTimeline().getTimelineContainer();
		$target.find( '.timelineClip' ).addClass( 'selectedClip' );
	},
	
	/**
	 * Apply a smil xml transform state ( to support undo / redo ) 
	 */
	registerEdit: function(){	
		// Throw away any edit history after the current editIndex: 
		if( this.editStack.length && this.editIndex > this.editStack.length ) {
			this.editStack = this.editStack.splice(0, this.editIndex);
		}
		
		// @@TODO would be good to just compute the diff in JS and store that
		// ( instead of the full xml text ) 
		this.editStack.push(  this.sequencer.getSmil().getXMLString() );
		
		// Update the editIndex
		this.editIndex = this.editStack.length - 1;
	},
	
	/**
	 * Undo an edit action
	 */
	undo: function(){
		this.editIndex--;		
		// Change to previous state 
		this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
	},
	
	/**
	 * Redo an edit action
	 */
	redo: function(){
		this.editIndex ++;
		if( this.editStack[ this.editIndex ] ) {
			this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
		} else {
			mw.log( 'SequencerActionsEdit::Redo: Already at most recent edit avaliable');
		}
	}
}