<?php

$messages = array();

$messages['en'] = array(
	'specialwikiathome'		=> 'Wiki@Home',
	'wikiathome-desc' 		=> 'Enables distributing transcoding video jobs to clients using firefogg.',
	'wah-short-audio'      	=> '$1 sound file, $2',
	'wah-short-video'      	=> '$1 video file, $2',
	'wah-short-general'    	=> '$1 media file, $2',

	'wah-long-audio'       	=> '($1 sound file, length $2, $3)',
	'wah-long-video'       	=> '($1 video file, length $2, $4×$5 pixels, $3)',
	'wah-long-multiplexed' 	=> '(multiplexed audio/video file, $1, length $2, $4×$5 pixels, $3 overall)',
	'wah-long-general'     	=> '(media file, length $2, $3)',
	'wah-long-error'       	=> '(ffmpeg could not read this file: $1)',

	'wah-transcode-working' => 'This video is being transcoded its $1% done',
	'wah-transcode-helpout' => 'You can help transcode this video by visiting [[Special:WikiAtHome|Wiki@Home]]',

	'wah-javascript-off'	=> 'You must have javascript enabled to participate in Wiki@Home',
	'wah-loading'			=> 'loading wiki@home interface <blink>...</blink>'
);
?>