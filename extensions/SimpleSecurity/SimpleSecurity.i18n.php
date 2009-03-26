<?php
/**
 * Internationalisation for SimpleSecurity extension
 *
 * @author Nad
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Nad
 */
$messages['en'] = array(
	'security'                 => 'Security log',
	'security-desc'            => 'Extends the MediaWiki article protection to allow restricting viewing of article content',
	'security-logpage'         => 'Security log',
	'security-logpagetext'     => 'This is a log of actions blocked by the [http://www.mediawiki.org/wiki/Extension:SimpleSecurity SimpleSecurity extension].',
	'security-logentry'        => '', # do not translate or duplicate this message to other languages
	'badaccess-read'           => 'Warning: "$1" is referred to here, but you do not have sufficient permissions to access it.',
	'security-info'            => 'There are $1 on this article',
	'security-info-toggle'     => 'security restrictions',
	'security-inforestrict'    => '$1 is restricted to $2',
	'security-desc-LS'         => "''(applies because this article is in the '''$2 $1''')''",
	'security-desc-PR'         => "''(set from the '''protect tab''')''",
	'security-desc-CR'         => "''(this restriction is '''in effect now''')''",
	'security-infosysops'      => "No restrictions are in effect because you are a member of the '''sysop''' group",
	'security-manygroups'      => 'groups $1 and $2',
	'protect-unchain'          => 'Modify actions individually',
);
