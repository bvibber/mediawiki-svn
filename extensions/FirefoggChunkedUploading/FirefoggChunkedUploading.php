<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * @copyright Copyright © 2010 Mark A. Hershberger <mah@everybody.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'FirefoggChunkedUploading',
	'url' => 'http://www.mediawiki.org/wiki/Extension:FirefoggChunkedUploading',
	'author' => array( 'Mark A. Hershberger' ),
	'descriptionmsg' => 'firefoggcu-desc',
);


$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['FirefoggChunkedUploading'] = $dir . 'FirefoggChunkedUploading.i18n.php';
