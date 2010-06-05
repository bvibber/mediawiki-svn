
mw.SmilBody = function( $body ){
	return this.init( $body );
}

mw.SmilBody.prototype = {
	
	init: function( $body ){
		this.$dom = $body; 
	},	
	// maybe "build" layout ? 
	updateLayout: function ( $layoutDom , time ) {
		var _this = this;
		
		// Set up the top level smil blocks container
		this.smilBlocks = [];					
		
		this.recurseSmilBlocks( this.$dom, this.smilBlocks );
		
		
		
		return  $layoutDom;
	},	
	
	/**
	* Recurse parse out smil elements
	*/
	recurseSmilBlocks: function( $node, blockStore ){
		var _this = this;
		// Recursively parse the body for "<par>" and <seq>"
		$node.each( function( inx, bodyChild ){
			var smilBlock = null;			
			switch( bodyChild.nodeName ) {
				case 'par': 
					smilBlock = new mw.SmilPar( bodyChild ) ) 					
				break;
				case 'seq':
					smilBlock = new mw.SmilSeq( bodyChild ) )
				break;
				default:
					mw.log(' Skiped ' + bodyChild.nodeName + ' ( not recognized tag )');
				break;
			}			
			// Add a blockStore the smilBlock
			blockStore.push( smilBlock );
			// if children have children add a block store
			
			// recurse
			
		});
		// Check if we have more children
	},
	
	
	// Updates the layout and adds a css animation to the next frame
	updateLayoutCssAnimation: function(){
		
	}
}

/**
* Par Block
*/ 
mw.SmilPar = function( $parElement ){
	return this.init(  $parElement );
}
mw.SmilPar.prototype = {
	init: function( $parElement ) {
		var _this = this;
		this.$dom = $parElement;
		
		// Go though all its children recursing on mw.SmilSeq where needed
		
	}
}

/**  
* Seq Block 
*/
mw.SmilSeq = function( $seqElement ){
	return this.init(  $seqElement );
}
mw.SmilSeq.prototype = {
	init: function( $seqElement ) {
		var _this = this;
		this.$dom = $seqElement;		
		// Go though all its children recursing on mw.SmilSeq where needed		
	}
}
