/**
* Handles the smil transistions
*/
mw.SmilTransitions = function( smilObject ){
	return this.init( smilObject );
}
mw.SmilTransitions.prototype = {
	
	init: function( smilObject ){
		this.smil = smilObject;	
	},
	
	// Generates a transition overlay based on the transition type  
	getTransitionOverlay: function( smilElement, animateTime ) {
		// Get the transition type and id: 
		var tranType = transitionId = false;
		
		if( $j( smilElement ).attr( 'transIn' ) ){
			tranType = 'transIn';
			transitionId = $j( smilElement ).attr( 'transIn' );
		}
		
		if( $j( smilElement ).attr( 'transOut' ) ){
			tranType = 'transOut';
			transitionId = $j( smilElement ).attr( 'transOut' );
		}
		
		if( !tranType || !transitionId ){
			// No transition ( smilElement )
			return ;
		}
		
		// Get the transition element
		$transition = this.smil.$dom.find( '#' + transitionId );
		if( ! $transition.attr('dur') ){
			mw.log( "Error: transition " + transitionId + "does not have duration " ); 
			return ;
		}
		// Get the transision type
		if( ! $transition.attr('type' ) {
			mw.log( "Error: transition " + transitionId + "does not have type " ); 
			return ;
		}
		// Check if the transition is in range
		var duration = this.smil.parseTime( $transition.attr('dur') );
		if( tranType == 'transIn' && duration > animateTime  ){
			
		}
	}
	

}