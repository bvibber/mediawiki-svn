<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the WikiAtHome extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

$exDir = dirname(__FILE__);
$wgAutoloadClasses['NonFreeVideoHandler'] 	= "$exDir/NonFreeVideoHandler.php";
$wgAutoloadClasses['WahJobManager'] 		= "$exDri/WahJobManager.php";

//add a set of video extensions to the $wgFileExtensions set that we support transcoding from
$tmpExt = array('avi', 'mov', 'mp4', 'mp2', 'mpeg', 'mpeg2', 'mpeg4', 'dv', 'wmv' );
foreach($tmpExt as $ext){
	if ( !in_array( $ext, $wgFileExtensions ) ) {
		$wgFileExtensions[] = $ext;
	}
	if( !isset( $wgMediaHandlers['video/'.$ext] )){
		$wgMediaHandlers['video/'.$ext] = 'NonFreeVideoHandler';
	}
}

$wgExtensionMessagesFiles['WikiAtHome'] = "$exDir/WikiAtHome.i18n.php";
$wgHooks['LanguageGetMagic'][] = 'NonFreeVideoHandler::registerMagicWords';

$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'Wiki@Home',
	'author'         => 'Michael Dale',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:WikiAtHome',
	'description'    => 'Enables distributing transcoding video jobs to clients using firefogg.',
	'descriptionmsg' => 'wikiathome-desc',
);

/*
 * Main WikiAtHome Class hold some constants and config values
 * 
 */
class WikiAtHome {
	const ENC_SAVE_BANDWITH = '256_200kbs';
	const ENC_WEB_STREAM = '400_300kbs';
	const ENC_HQ_STREAM = 'high_quality';
}

/******************* CONFIGURATION STARTS HERE **********************/

//what to encode to: 
$wgEnabledDerivatives = array( WikiAtHome::ENC_WEB_STREAM );

//these params are set via firefogg encode options see:
//http://firefogg.org/dev/index.html
//if you want to re-derive things you should change its key above in the WikiAtHome class
$wgDerivativeSettings[ WikiAtHome::ENC_SAVE_BANDWITH ] = 
		array(
			'videoBitrate'	=> '200',
			'audioBitrate'	=> '32',
			'samplerate'	=> '24',
			'channels'		=> '1',
			'width'			=> '256',
			'noUpscaling'	=> 'true'
		);
$wgDerivativeSettings[ WikiAtHome::ENC_WEB_STREAM ] = 
		array( 
			'width'			=> '400',
			'videoBitrate'	=> '400',
			'audioBitrate'	=> '64',
			'noUpscaling'	=> 'true'
		);
$wgDerivativeSettings[ WikiAtHome::ENC_HQ_STREAM ] =
		array(
			'videoQuality'  => 9,
			'audioQuality'	=> 9,
			'noUpscaling'	=> 'true'		
		);

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
