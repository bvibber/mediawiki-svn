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
	},	
	
	selectAll: function(){
		//Select all the items in the timeline
		$target = this.sequencer.getTimeline().getTimelineContainer();
		$target.find( '.timelineClip' ).addClass( 'selectedClip' );
	},
	
	/**
	 * Set up the edit stack
	 */
	setupEditStack: function(){
		this.editStack = [];
		// Set the initial edit state: 
		this.editStack.push(  this.sequencer.getSmil().getXMLString() );
		// Disable undo		
		this.sequencer.getMenu().disableMenuItem( 'edit', 'undo' );
	},
	
	/**
	 * Apply a smil xml transform state ( to support undo / redo ) 
	 */
	registerEdit: function(){	
		mw.log( 'ActionsEdit::registerEdit: ' + this.sequencer.getSmil().getXMLString() );
		// Throw away any edit history after the current editIndex: 
		if( this.editStack.length && this.editIndex > this.editStack.length ) {
			this.editStack = this.editStack.splice(0, this.editIndex);
		}
		
		// @@TODO would be good to just compute the diff in JS and store that
		// ( instead of the full xml text ) 
		this.editStack.push(  this.sequencer.getSmil().getXMLString() );
		
		// Update the editIndex
		this.editIndex = this.editStack.length - 1;
		
		// Enable the undo option: 
		this.sequencer.getMenu().enableMenuItem( 'edit', 'undo' );
	},
	
	/**
	 * Undo an edit action
	 */
	undo: function(){
		this.editIndex--;
		if( this.editStack[ this.editIndex ] ) {
			this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
		} else {
			// index out of range set to 0
			this.editIndex = 0;
			mw.log("Error: SequenceActionsEdit:: undo Already at oldest index:" + this.editIndex);
		}
		// if at oldest undo disable undo option 
		if( this.editIndex - 1  == 0 ){
			this.sequencer.getMenu().disableMenuItem( 'edit', 'undo' );
		}
	},	
	/**
	 * Redo an edit action
	 */
	redo: function(){
		this.editIndex ++;
		if( this.editStack[ this.editIndex ] ) {
			this.sequencer.updateSmilXML( this.editStack[ this.editIndex ] );
		} else {
			// index out of redo range set to last edit
			this.editIndex == this.editStack.length - 1
			mw.log( 'Error: SequencerActionsEdit::Redo: Already at most recent edit avaliable');
		}
		
		// if at newest redo disable redo option 
		if( this.editIndex == this.editStack.length - 1 ){
			this.sequencer.getMenu().disableMenuItem( 'edit', 'redo' );
		}
	}
}