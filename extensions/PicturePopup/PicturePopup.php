<?php

/**
 * AJAJ extension for displaying popups similar to the image description page, when the image is clicked
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "PicturePopup extension\n";
	die( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'PicturePopup',
	'version' => '1.1',
	'author' => 'Tim Starling',
	'url' => 'http://mediawiki.org/wiki/Extension:Picture_Popup',
	'description' => 'AJAJ extension for displaying popups similar to the image description page, when the image is clicked',
);

$wgAjaxExportList[] = 'wfPicturePopupAjax';
$wgAutoloadClasses['PicturePopup'] = dirname( __FILE__ ) . '/PicturePopup_body.php';

function wfPicturePopupAjax( $image, $recache = false ) {
	return PicturePopup::ajax( $image, $recache );
}
