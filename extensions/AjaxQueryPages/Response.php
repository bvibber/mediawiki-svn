<?php
if( !defined( 'MEDIAWIKI' ) )
	die( 1 );

// Ajax actions registration
$wgAjaxExportList[] = "wfAjaxQueryPages";

/**
 * Ajax responder entry point
 */
function wfAjaxQueryPages( $specialpagename, $offset, $limit ) {

	// Make sure we requested an existing special page
	if( !$spObj = SpecialPage::getPage( $specialpagename ) ) {
		return null;
	}

	// todo check values
	$_REQUEST['offset'] = (int) $offset;
	$_REQUEST['limit'] = (int) $limit;

	$spObj->execute( null );

	global $wgOut;
	return $wgOut->getHTML();
}

