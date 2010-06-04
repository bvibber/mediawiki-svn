/*
* Loader for smilPlayer
*/

mw.addClassFilePaths( {
	"mw.SmilPlayer" : "mw.SmilPlayer.js"	
} );

// Add the mw.SmilPlayer to the embedPlayer loader:
$j( mw ).bind( 'LoaderEmbedPlayerUpdateRequest', function( event, playerElement, classRequest ) {
	
	// Check if the playerElement includes a smil source.
	var includeSmilPlayer = false;
	function checkElementForSMIL( element ){
		if( $j( element ) .attr('type' ) == 'application/smil' ||
			( $j( element ).attr('src' ) && 
		 	$j( element ).attr('src' ).substr( -4) == 'smil' ) ) 
		 {
		 	return true;
		 }
		 return false;
	}
	
	// Check player and sources for "SMIL"
	var loadSmil = false;
	loadSmil = ( checkElementForSMIL( playerElement ) ) ? true : false;	
	if( !loadSmil ){
		$j( playerElement ).find( 'source' ).each( function(inx, sourceElement){
			if( checkElementForSMIL( sourceElement ) ){
				loadSmil = true;
			}			
		});
	}
	
	// Add smil / sequence player if needed
	if( loadSmil ) {				
		// If the swarm transport is enabled add mw.SwarmTransport to the request.   	
		if( $j.inArray( 'mw.SmilPlayer', classRequest ) == -1 )  {
			classRequest.push( 'mw.SmilPlayer' );
		}
	}
} );