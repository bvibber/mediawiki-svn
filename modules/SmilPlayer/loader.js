/*
* Loader for smilPlayer
*/

mw.addClassFilePaths( {
	"mw.SmilPlayer" : "mw.SmilPlayer.js",
	"smilEmbed"	: "smilEmbed.js"
} );

$j( mw ).bind( 'addElementWaitForMetaEvent', function( event, waitForMetaObject ) {
	// Tell embedPlayer ~not~ to wait for metadata in cases of smil documents
	if( mw.CheckElementForSMIL(  waitForMetaObject[ 'playerElement' ] ) ){
		waitForMetaObject[ 'waitForMeta' ] = false;
		return false;
	}
});

// Add the mw.SmilPlayer to the embedPlayer loader:
$j( mw ).bind( 'LoaderEmbedPlayerUpdateRequest', function( event, playerElement, classRequest ) {		
	// Add smil / sequence player if needed
	if( mw.CheckElementForSMIL( playerElement )  ) {				
		// If the swarm transport is enabled add mw.SwarmTransport to the request.   	
		if( $j.inArray( 'mw.SmilPlayer', classRequest ) == -1 )  {
			classRequest.push( 'mw.SmilPlayer' );
		}
	}
} );

// Add the smil player to avaliable player types: 
$j( mw ).bind( 'EmbedPlayerManagerReady', function( event ) {			
	
	// Add the swarmTransport playerType	
	mw.EmbedTypes.players.defaultPlayers[ 'application/smil' ] = [ 'smil' ];
	
	// Build the swarm Transport "player"
	var smilMediaPlayer = new mediaPlayer( 'smilPlayer', [ 'application/smil' ], 'smil' );
	
	// Add the swarmTransport "player"
	mw.EmbedTypes.players.addPlayer( smilMediaPlayer );
				
} );		

/**
* Check if a video tag element has a smil source
*/ 
mw.CheckElementForSMIL = function( element ){
	if( $j( element ) .attr('type' ) == 'application/smil' ||
		( $j( element ).attr('src' ) && 
	 	$j( element ).attr('src' ).substr( -4) == 'smil' ) ) 
	 {
	 	return true;
	 }
	 var loadSmil = false;
	 $j( element ).find( 'source' ).each( function(inx, sourceElement){
		if( mw.CheckElementForSMIL( sourceElement ) ){
			loadSmil = true;
			return true;
		}			
	});	 
	return loadSmil;
};
