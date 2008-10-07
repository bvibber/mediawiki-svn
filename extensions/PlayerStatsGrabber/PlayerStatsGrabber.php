<?php
/*
 * simple stats output and gather for oggPlay and a "sample page" 
 */									 

# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/MyExtension/MyExtension.php" );
EOT;
        exit( 1 );
}
/*
 * config values
 */

//embed code and "weight" (ie weight of 3 is 3x more likely than weight of 1)  
//flash embed coode (the raw html that gets outputed to the browers)
$psEmbedAry = array(
	array( 'embed_type'=>'flash',
		   'source_type'=>'youtube',
		   'name'=>'Sample Youtube Embed',
		   'weight'=>1, 
		   'code'=>'<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/eaADQTeZRCY&hl=en&fs=1"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.youtube.com/v/eaADQTeZRCY&hl=en&fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="425" height="344"></embed></object>' 
	),
	array( 'embed_type'	=>'local',
		   'weight'=>1,
		   'wiki_code'	=>'[[Image:Sample_fish.ogg]]'
	)
);



/*
 * end config
 */

$wgExtensionMessagesFiles['PlayerStatsGrabber'] 	= dirname( __FILE__ ) . '/PlayerStatsGrabber.i18n.php';
$wgAutoloadClasses['SpecialPlayerStatsGrabber'] 	= dirname( __FILE__ ) . '/PlayerStatsGrabber_body.php';
$wgSpecialPages['PlayerStatsGrabber']		   				=  array( 'SpecialPlayerStatsGrabber' );
											 
$wgSpecialPageGroups['PlayerStatsGrabber'] = 'wiki'; // like Special:Statistics

//add ajax hook to accept the status input: 
$wgAjaxExportList[] = 'mw_push_player_stats';

$wgExtensionCredits['media'][] = array(
	'name'           => 'PlayerStats',
	'author'         => 'Michael Dale',
	'svn-date' 		 => '$LastChangedDate: 2008-08-06 07:18:43 -0700 (Wed, 06 Aug 2008) $',
	'svn-revision' 	 => '$LastChangedRevision: 38715 $',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:PlayerStats',
	'description'    => 'PlayerStats and survey for monitoring theora support relative to flash'	
);


/*
 * does a player stats request.. returns the "db key"
 *  (lets people fill out survay after playing clip) 
 *  or 
 *  (ties survay page data to detection) 
 */
function mw_push_player_stats(){
	global $wgRequest;	
	//do the insert into the userPlayerStats table:
	$dbr =& wfGetDB( DB_READ );
	if(	   $wgRequest->getVal('cb')=='' 
		|| $wgRequest->getVal('cb_inx')==''
		|| $wgRequest->getVal('uh')==''){
		//output error: 		
		return 'error: missing param for json callback';
	}	
	if(!isset($wgRequest->data['cs'])|| !is_array($wgRequest->data['cs'])){
		$wgRequest->data['cs']=array();
	}
	//set up insert array: 
	$insAry = array(
		'user_hash'			=> $wgRequest->getVal('uh'),
		'b_user_agent'		=> $wgRequest->getVal('b_user_agent'),
		'b_name'			=> $wgRequest->getVal('b_name'),
		'b_version'			=> $wgRequest->getVal('b_version'),
		'b_os'				=> $wgRequest->getVal('b_os'),
		'flash_version'		=> $wgRequest->getVal('fv'),
		'java_version'		=> $wgRequest->getVal('jv'),
		'html5_video_enabled'=>(in_array('videoElement',  $wgRequest->data['cs'] ))?true:false,
		'java_enabled'		=> ( in_array('cortado', $wgRequest->data['cs'] ) )?true:false,
		'totem_enabled'		=> ( in_array('totem', $wgRequest->data['cs'] ) )?true:false,
		'flash_enabled'		=>( in_array('flash', $wgRequest->data['cs'] ) )?true:false,
		'quicktime_enabled'	=>( in_array( array('quicktime-mozilla','quicktime-activex'),
									$wgRequest->data['cs'] ) 
							  )?true:false,
		'vlc_enabled'		=>( in_array( array('vlc-mozilla', 'vlc-activex'),
							   		$wgRequest->data['cs'] ) 
							  )?true:false,
		'mplayer_enabled'	=>( in_array( 'mplayerplug-in',
							   		$wgRequest->data['cs'] ) 
							  )?true:false					  
	);	
	//check for user hash (don't collect multiple times for the same user)
	//$user_hash = 				
	$insert_id = $dbr->selectField('player_stats_log', 'id', 
				array(	'user_hash'=>$wgRequest->getVal('uh') ),
						'mw_push_player_stats::Select User Hash');	
	//last_insert_id	
	if( $insert_id ){
		//for now don't insert repeates		
	}else{
		$dbw =& wfGetDB( DB_WRITE );
		$dbw->insert( 'player_stats_log', $insAry, 'mw_push_player_stats:Insert');
		$insert_id = $dbw->insertId();
		$dbw->commit();
	}
	header( 'Content-Type: text/javascript' );
	return htmlspecialchars( $wgRequest->getVal('cb') ). '(' .PhpArrayToJsObject_Recurse(	
			array(	
				'cb_inx' => htmlspecialchars( $wgRequest->getVal('cb_inx') ),	
				'id' => $insert_id
			)
		) . ');';
}

/*
 * @@todo should use API json output wrappers
 */ 
if( ! function_exists('php2jsObj') ){
	function php2jsObj( $array, $objName = 'mv_result' )
	{
	   return  $objName . ' = ' . phpArrayToJsObject_Recurse( $array ) . ";\n";
	}
}
if( ! function_exists('PhpArrayToJsObject_Recurse') ){
	function PhpArrayToJsObject_Recurse( $array ) {
	   // Base case of recursion: when the passed value is not a PHP array, just output it (in quotes).
	   if ( ! is_array( $array ) && !is_object( $array ) ) {
	       // Handle null specially: otherwise it becomes "".
	       if ( $array === null )
	       {
	           return 'null';
	       }
	       return '"' . javascript_escape( $array ) . '"';
	   }
	   // Open this JS object.
	   $retVal = "{";
	   // Output all key/value pairs as "$key" : $value
	   // * Output a JS object (using recursion), if $value is a PHP array.
	   // * Output the value in quotes, if $value is not an array (see above).
	   $first = true;
	   foreach ( $array as $key => $value ) {
	       // Add a comma before all but the first pair.
	       if ( ! $first ) {
	           $retVal .= ', ';
	       }
	       $first = false;
	       // Quote $key if it's a string.
	       if ( is_string( $key ) ) {
	           $key = '"' . $key . '"';
	       }
	       $retVal .= $key . ' : ' . PhpArrayToJsObject_Recurse( $value );
	   }
	   // Close and return the JS object.
	   return $retVal . "}";
	}
}
if( ! function_exists('javascript_escape')){
	function javascript_escape( $val ) {
		// first strip /r
		$val = str_replace( "\r", '', $val );
		return str_replace(	array( '"', "\n", '{', '}' ),
							array( '\"', '"' . "+\n" . '"', '\{', '\}' ),
							$val );
	}
}

?>