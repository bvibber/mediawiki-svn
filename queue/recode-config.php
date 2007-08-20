<?php
/*
* $wgVideoBitrates and $wgVideoWidths must be parallel arrays,
* with each bitrate being encoded at the corresponding width.
*/
$wgVideoBitrates = array(150, 300, 700);
$wgVideoWidths = array(175, 300, 640);

$wgRecodeAudio = array(
	'ogg' => array(
		'classname' => 'OggAudioFormat',
		32, 128
	)
);

$wgRecodeVideo = array(
	'ogg' => array(
		'classname' => 'OggVideoFormat',
		array('width' => 175, 'bitrate' => 150),
		array('width' => 320, 'bitrate' => 300),
		array('width' => 640, 'bitrate' => 700)
	)
);

/*
* IP addresses of hosts that may process uploads and need to notify recoded
* of new jobs.
*/
$acceptIPs = array('127.0.0.1', '192.168.123.133');

/*
* The path to encoder_example
*/
$encoder_directory = '/data/build/libtheora-1.0alpha7/examples';

if(! defined("NOTIFY_SCRIPT_URL"))
{
	/* the below is a default that will probably be wrong for your install.
	*  Set this value correctly to point to the notify script that will
	*  accompany the daemon on this machine.
	*/
	define("NOTIFY_SCRIPT_URL", 'http://' . trim(`hostname`) . '/queue/notify.php');
}

define("MW_INSTALL_PATH", "/data/www/wiki/phase3");

if(! defined("SIGUSR1"))
{
	/* PHP will not define the signal constants for notify.php because it
	*  runs under a web server. You may need to change this value to
	*  match your system.
	*/
	define("SIGUSR1", 10);
}

##################### DO NOT EDIT BELOW #####################
define("RECODE_DAEMON_LOCKFILE", "MWRECODED_LOCKFILE");
define("RECODE_NOTIFY_LOCKFILE", "MWRECODEN_LOCKFILE");
define("RECODE_DAEMON_OUTPIPE", "MWRECODED_OUTPIPE.fifo");
define("RECODE_DAEMON_INPIPE", "MWRECODED_INPIPE.fifo");
define("RECODE_DAEMON_STATUS_ERR", 1);

require('recode-common.php');