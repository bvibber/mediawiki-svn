<?php
/**
 * Internationalisation file for the MassBlank extension
 * @addtogroup Extensions
 * @author Tisane
 */

$messages = array();

/** English
 * @author Tisane
 */
$messages['en'] = array(
	'massblank'               => 'Mass blank',
	'massblank-desc'          => 'Gives administrators the ability to [[Special:MassBlank|mass blank]] pages',
	'massblank-nopages'       => 'No new pages by [[Special:Contributions/$1|$1]] in recent changes.',
	'massblank-list'          => 'The following pages were recently created by [[Special:Contributions/$1|$1]];
put in a comment and hit the button to blank them.',
	'massblank-defaultreason' => 'Mass blanking of pages added by $1',
	'massblank-tools'         => 'This tool allows for mass blanking of pages recently added by a given user or an IP address.
Input the username or IP address to get a list of pages to blank.',
	'massblank-submit-user'   => 'Go',
	'massblank-submit-blank'  => 'Blank selected',
	'massblank-blankcomment'  => 'Reason for blanking:',
	'right-massblank'         => 'Mass blank pages',
);

/** Message documentation (Message documentation) */
$messages['qqq'] = array(
	'massblank-desc' => 'Short description of the MassBlank extension, shown in [[Special:Version]]. Do not translate or change links.',
	'massblank-submit-user' => '{{Identical|Go}}',
	'right-massblank' => '{{doc-right}}',
);
