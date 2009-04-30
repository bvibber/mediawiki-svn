<?php 
/*
 * simple entry point to initiate a background download
 * 
 * arguments: 
 * 
 * -sid {$session_id} -usk {$upload_session_key}
 */

global $optionsWithArgs;
$optionsWithArgs = Array('sid', 'usk');

require_once( 'commandLine.inc' );

if(!isset($options['sid']) || !isset($options['usk'])){
	print<<<EOT
	simple entry point to initiate a background download
	
	Usage: http_session_download.php [options]
	Options:
		--sid the session id (required)
		--usk the upload session key (also required)  
EOT;

	exit();
}
//run the download: 
Http::doSessionIdDownload( $options['sid'], $options['usk'] );

?>