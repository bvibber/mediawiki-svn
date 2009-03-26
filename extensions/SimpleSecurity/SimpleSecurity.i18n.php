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
	'security'                 => "Security log",
	'security-logpage'         => "Security log",
	'security-logpagetext'     => "This is a log of actions blocked by the [http://www.mediawiki.org/wiki/Extension:SimpleSecurity SimpleSecurity extension].",
	'security-logentry'        => "",
	'badaccess-read'           => "\nWarning: \"$1\" is referred to here, but you do not have sufficient permisions to access it.\n",
	'security-info'            => "There are $1 on this article",
	'security-info-toggle'     => "security restrictions",
	'security-inforestrict'    => "$1 is restricted to $2",
	'security-desc-LS'         => "<i>(applies because this article is in the <b>$2 $1</b>)</i>",
	'security-desc-PR'         => "<i>(set from the <b>protect tab</b>)</i>",
	'security-desc-CR'         => "<i>(this restriction is <b>in effect now</b>)</i>",
	'security-infosysops'      => "No restrictions are in effect because you are a member of the <b>sysop</b> group",
	'security-manygroups'      => "groups $1 and $2",
	'protect-unchain'          => "Modify actions individually",
);
