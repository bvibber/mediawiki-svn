/**
* Sequencer Server bridges a server API with sequence actions like 'load', 'save', 'revision history' etc.   
* ( for now only mediaWiki api is supported )
* We will abstract all the method calls once we add another api backend
*/

//Wrap in mw closure
( function( mw ) {
	
mw.SequencerServer = function( sequencer ) {
	return this.init( sequencer );
};

// Set up the SequencerServer prototype method object
mw.SequencerServer.prototype = {
	init: function( sequencer ){
		this.sequencer = sequencer;
		// Set local config from sequencer options
		var serverConfig = this.sequencer.getOption( 'server' );
		
		// NOTE this should trigger an apiHandler once we have more than one api backend
		this.apiType = serverConfig.type;		
		this.apiUrl = serverConfig.url;		
		this.titleKey = serverConfig.titleKey;		
	},
	getSmilXml: function( callback ){
		mw.getTitleText( this.titleKey, callback )
	}
}


} )( window.mw );	