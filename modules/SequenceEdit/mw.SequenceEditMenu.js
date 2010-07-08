//Wrap in mw closure to avoid global leakage
( function( mw ) {
	
mw.SequenceEditMenu = function( sequenceEdit ) {
	return this.init( sequenceEdit );
};

// Set up the mvSequencer object
mw.SequenceEditMenu.prototype = {
		
	init: function( sequenceEdit ){
		this.sequenceEdit =sequenceEdit 
	},
	drawMenu:function(){
		var $menuTarget = this.sequenceEdit.getMenuTarget();
		if( mw.getConfig( 'SequenceEdit.KalturaAttribution' ) ){
			$menuTarget.append(
				$j('<span />')
				.css('float','right')
				.append( 
					gM('mwe-sequenceedit-sequencer_credit_line',
						'http://kaltura.com',
						'http://wikimedia.org'
					)
				)
			)
		}
	}
};

} )( window.mw );