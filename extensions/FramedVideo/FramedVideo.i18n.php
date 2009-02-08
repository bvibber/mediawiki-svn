<?php
/**
 * Internationalisation file for extension FramedVideo.
 */

$messages = array();

$messages['en'] = array(
	'framedvideo_default_width' => '270', # do not translate or duplicate this message to other languages
	'framedvideo_force_default_size' => 'false', # do not translate or duplicate this message to other languages
	'framedvideo_max_width' => '852', # do not translate or duplicate this message to other languages
	'framedvideo_max_height' => '510', # do not translate or duplicate this message to other languages
	'framedvideo_allow_full_screen' => 'true', # do not translate or duplicate this message to other languages
	'framedvideo_force_allow_full_screen' => 'false', # do not translate or duplicate this message to other languages
	'framedvideo_frames' => 'true', # do not translate or duplicate this message to other languages
	'framedvideo_force_frames' => 'false', # do not translate or duplicate this message to other languages
	'framedvideo_force_position' => 'false', # do not translate or duplicate this message to other languages
	'framedvideo_position' => 'right', # only translate this message to other languages if you have to change it
	'framedvideo_errors' => 'Multiple errors have occurred!',
	'framedvideo_error' => 'An error has occurred!',
	'framedvideo_error_unknown_type' => 'Unknown video service id ("$1"): check "type" parameter.',
	'framedvideo_error_no_id_given' => 'Missing "id" parameter.',
	'framedvideo_error_height_required' => 'Video type "$1" requires "height" parameter.',
	'framedvideo_error_height_required_not_only_width' => 'Video type "$1" requires "height" parameter, not only "width" parameter.',
	'framedvideo_error_width_too_big' => 'Given value of "width" parameter is too large.',
	'framedvideo_error_height_too_big' => 'Given value of "height" parameter is too large.',
	'framedvideo_error_no_integer' => 'Given value of "$1" is not a positive number.',
	'framedvideo_error_limit' => 'The highest allowed value is $1.',
	'framedvideo_error_full_size_not_allowed' => 'Value "full" for "size" parameter not allowed for video service id "$1".',
	'framedvideo_helppage' => 'Help:Video',
	'framedvideo_error_see_help' => '[[{{MediaWiki:Framedvideo_helppage}}|More about syntax]].',
	'framedvideo_error_height_and_width_required' => 'Video type "$1" requires "height" and "width2" or "width" parameters.',
	'framedvideo-desc' => 'Allows embedding videos from various websites using the tag <tt><nowiki><video></nowiki></tt>',
);


$messages['pl'] = array(
	'framedvideo_errors' => 'Wystąpiły błędy!',
	'framedvideo_error' => 'Wystąpił błąd!',
	'framedvideo_error_unknown_type' => 'Nieznany identyfikator „$1” dla serwisu wideo: sprawdź parametr „type”.',
	'framedvideo_error_no_id_given' => 'Brakuje parametru „id”.',
	'framedvideo_error_height_required' => 'Wideo z serwisu o identyfikatorze „$1” wymaga podania parametru „height”.',
	'framedvideo_error_height_required_not_only_width' => 'Wideo z serwisu o identyfikatorze „$1” wymaga podania parametru „height”, nie tylko „width”.',
	'framedvideo_error_width_too_big' => 'Podana wartość „width” jest zbyt duża.',
	'framedvideo_error_height_too_big' => 'Podana wartość „height” jest zbyt duża.',
	'framedvideo_error_no_integer' => 'Podana wartość dla parametru „$1” nie jest dodatnią wartością liczbową.',
	'framedvideo_error_limit' => 'Największa dopuszczalna wartość to $1.',
	'framedvideo_error_full_size_not_allowed' => 'Wartość „full” dla parametru „size” niedopuszczalna dla identyfikatora „$1”.',
	'framedvideo_error_see_help' => 'Aby dowiedzieć się więcej o formatowaniu, zobacz [[pomoc:Wideo|stronę pomocy]].',
	'framedvideo_error_height_and_width_required' => 'Wideo z serwisu o identyfikatorze „$1” wymaga podania parametru „height” i „width2” lub „width”.',
	'framedvideo-desc' => 'Pozwala na osadzanie wideo z innych serwisów',
);
