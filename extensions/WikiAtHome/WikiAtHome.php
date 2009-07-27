<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the WikiAtHome extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

$exDir = dirname(__FILE__);
$wgAutoloadClasses['nonFreeVideoHandler'] = "$exDir/nonFreeVideoHandler.php";

$wgMediaHandlers['video/avi'] = 'nonFreeVideoHandler';

//add a set of video extensions to the $wgFileExtensions set that we support transcoding from
$tmpExt = array('avi', 'mov', 'mp4', 'mp2', 'mpeg', 'mpeg2', 'mpeg4', 'dv', 'wmv' );
foreach($tmpExt as $ext){
	if ( !in_array( $ext, $wgFileExtensions ) ) {
		$wgFileExtensions[] = $ext;
	}
}

$wgExtensionMessagesFiles['WikiAtHome'] = "$exDir/WikiAtHome.i18n.php";
$wgParserOutputHooks['WikiAtHome'] = array( 'WikiAtHome', 'outputHook' );
$wgHooks['LanguageGetMagic'][] = 'WikiAtHome::registerMagicWords';

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'Wiki@Home',
	'author'         => 'Michael Dale',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:WikiAtHome',
	'description'    => 'Enables distributing transcoding video jobs to clients using firefogg.',
	'descriptionmsg' => 'wikiathome-desc',
);

/******************* CONFIGURATION STARTS HERE **********************/

//ffmpeg2theora path: enables us to get basic source file information 
$wgffmpeg2theora = '/usr/bin/ffmpeg2theora';

//the oggCat path enables server side concatenation of encoded "chunks" 
$wgOggCat =  '/usr/bin/oggCat';

//if you do have oggCat installed then we can do encoding jobs in "chunks"
//and assemble on the server: (this way no single slow client slows down 
//a video job and we can have tighter timeouts)
// $wgChunkDuration is set in seconds: (setting this too low will result in bad encodes)
// $wgChunkDuration is only used if we have a valid $wgOggCat install
$wgChunkDuration = '10'; 
