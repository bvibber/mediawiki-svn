<?php
if( !defined( 'MEDIAWIKI' ) )
	die( 1 );

// Hooks registration:
global $wgHooks;
$wgHooks['AjaxAddScript'][] = 'wfAjaxQueryPagesAddJS';

function wfAjaxQueryPagesAddJS( $out ) {
	global $wgTitle;
	if( $wgTitle->getNamespace() != NS_SPECIAL ) {
		return true;
	}
	if( !$spObj = SpecialPage::getPage( $wgTitle->getDBKey() ) ) {
		return true;
	}

	global $wgJsMimeType, $wgScriptPath ;
	$out->addScript( "<script type=\"{$wgJsMimeType}\" src=\"$wgScriptPath/extensions/AjaxQueryPages/AjaxQueryPages.js\"></script>\n" );
	return true;
}

?>
