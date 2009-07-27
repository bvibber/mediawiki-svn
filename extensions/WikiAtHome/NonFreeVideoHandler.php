<?php
/*
 * creates an stub for non-free video that is waiting to be transcoded
 */
class nonFreeVideoHandler extends MediaHandler {
	const METADATA_VERSION = 1;

	static $magicDone = false;
	
	function isEnabled() {
		return true;
	}
	
	static function registerMagicWords( &$magicData, $code ) {
		wfLoadExtensionMessages( 'WikiAtHome' );
		return true;
	}
	
	function getParamMap() {
		wfLoadExtensionMessages( 'WikiAtHome' );
		/*return array(
			'img_width' => 'width',
			'ogg_noplayer' => 'noplayer',
			'ogg_noicon' => 'noicon',
			'ogg_thumbtime' => 'thumbtime',
		);*/
	}
	
	function getMetadata( $image, $path ) {
		global $wgffmpeg2theora;
		$metadata = array( 'version' => self::METADATA_VERSION );
		//if we have  fffmpeg2theora
		if( $wgffmpeg2theora && is_file( $wgffmpeg2theora ) ){
							
			$cmd = wfEscapeShellArg( $wgffmpeg2theora ) . ' ' . wfEscapeShellArg ( $path ). ' --info';
			wfProfileIn( 'ffmpeg2theora' );
			wfDebug( __METHOD__.": $cmd\n" );
			$err = wfShellExec( $cmd, $retval );
			wfProfileOut( 'ffmpeg2theora' );
						
		}else{
			$metadata['error'] = array(
				'message' => 'missing ffmpeg2theora<br> check that ffmpeg2theora is installed and that $wgffmpeg2theora points to its location',
				'code' => 1
			);
		}
		
		return serialize( $metadata );
	}
	function unpackMetadata( $metadata ) {
		$unser = @unserialize( $metadata );
		if ( isset( $unser['version'] ) && $unser['version'] == self::OGG_METADATA_VERSION ) {
			return $unser;
		} else {
			return false;
		}
	}
	
}
?>