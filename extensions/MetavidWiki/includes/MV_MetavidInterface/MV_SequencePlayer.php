<?php
/*
 * MV_SequencePlayer.php Created on Nov 2, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */
 if ( !defined( 'MEDIAWIKI' ) )  die( 1 );
 // make sure the parent class mv_component is included

 class MV_SequencePlayer{
 	function __construct( &$seqTitle ){
 		$this->seqTitle = $seqTitle; 		
 	}
 	function getEmbedSeqHtml( $options=array() ){ 	
 		global $mvDefaultVideoPlaybackRes;
 		if( $options['oldid'] )$oldid = $options['oldid'];	
		
 		$exportTitle = Title::MakeTitle( NS_SPECIAL, 'MvExportSequence/' . $this->seqTitle->getDBKey() );
		$title_url = $exportTitle->getFullURL();						
		
		if ( isset( $options['oldid'] ) ) {			
			$ss = ( strpos( $title_url, '?' ) === false ) ? '?':'&';
			$title_url .= $ss . 'oldid=' . htmlspecialchars( $options['oldid'] );
		}
		
		if ( isset( $options['size'] ) ){			
			list($width, $height) = explode( 'x', $options['size'] );
		}else{			
			list($width, $height) = explode( 'x', $mvDefaultVideoPlaybackRes);
		}
		
		return '<playlist width="' . htmlspecialchars($width) . '" height="'. htmlspecialchars($height) .'" '.
					'src="' . $title_url . '"></playlist>';
 	}
 }
?>
