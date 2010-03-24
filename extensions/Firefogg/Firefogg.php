<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * @copyright Copyright © 2010 Mark A. Hershberger <mah@everybody.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Firefogg',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Firefogg',
	'author' => array( 'Mark A. Hershberger' ),
	'descriptionmsg' => 'firefogg-desc',
);


$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['Firefogg'] = $dir . 'Firefogg.i18n.php';
