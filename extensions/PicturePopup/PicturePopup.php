<?php

/**
 * AJAJ extension for displaying popups similar to the image description page, when the image is clicked
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "PicturePopup extension\n";
	die( 1 );
}

$wgAjaxExportList[] = 'wfPicturePopupAjax';
$wgAutoloadClasses['PicturePopup'] = dirname( __FILE__ ) . '/PicturePopup_body.php';

function wfPicturePopupAjax( $image, $recache = false ) {
	return PicturePopup::ajax( $image, $recache );
}

?>
