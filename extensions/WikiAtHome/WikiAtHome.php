<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is the WikiAtHome extension. Please see the README file for installation instructions.\n";
	exit( 1 );
}

$exDir = dirname(__FILE__);
//setup autoloader php:
$wgAutoloadClasses[ 'NonFreeVideoHandler' ] 	= "$exDir/NonFreeVideoHandler.php";
$wgAutoloadClasses[ 'WahJobManager' ] 		= "$exDir/WahJobManager.php";
$wgAutoloadClasses[ 'ApiWikiAtHome' ]			= "$exDir/ApiWikiAtHome.php";

//setup autoloading javascript:
$wgJSAutoloadLocalClasses[ 'WikiAtHome' ]		= "extensions/WikiAtHome/WikiAtHome.js";

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
$wgExtensionAliasesFiles['WikiAtHome'] = "$exDir/WikiAtHome.alias.php";
$wgHooks['LanguageGetMagic'][] = 'NonFreeVideoHandler::registerMagicWords';

//special pages
$wgAutoloadClasses['SpecialWikiAtHome']		= "$exDir/SpecialWikiAtHome.php";
$wgSpecialPages['SpecialWikiAtHome']		= 'SpecialWikiAtHome';

//add api module for processing jobs
$wgAPIModules['wikiathome'] = 'ApiWikiAtHome';

//credits
$wgExtensionCredits['media'][] = array(
	'path'           => __FILE__,
	'name'           => 'Wiki@Home',
	'author'         => 'Michael Dale',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:WikiAtHome',
	'description'    => 'Enables distributing transcoding video jobs to clients using firefogg.',
	'descriptionmsg' => 'wah-desc',
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

//GLOBAL FUNCTIONS:

/*
 * gets the json metadata from a given file (also validates it as a valid file)
 */
function wahGetMediaJsonMeta( $path ){
	$cmd = wfEscapeShellArg( $wgffmpeg2theora ) . ' ' . wfEscapeShellArg ( $path ). ' --info';
	wfProfileIn( 'ffmpeg2theora shellExec' );
	wfDebug( __METHOD__.": $cmd\n" );
	$json_meta_str = wfShellExec( $cmd );
	wfProfileOut( 'ffmpeg2theora shellExec' );
	$objMeta = json_decode( $json_meta_str );

	//if we return the same string then json_decode has failed in php < 5.2.6
	//workaround for bug http://bugs.php.net/bug.php?id=45989
	if( $objMeta == $json_meta_str )
		return false;
	return $objMeta;
}

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

//how long before considering a job ready to be assigned to others
//note first "in" wins & if once time is up we decrement set_c
$wgJobTimeOut = 60*10; //10 min

//this meaters how many copies of any given stream we should send out as part of a job
$wgNumberOfClientsPerJobSet = 25;

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

