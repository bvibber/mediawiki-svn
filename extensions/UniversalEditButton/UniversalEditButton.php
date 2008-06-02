<?php

// For experimental browser widget :)

$wgHooks['BeforePageDisplay'][] = 'efUniversalEditLink';

function efUniversalEditLink( $output, $skin ) {
	global $wgArticle, $wgTitle, $wgUser;
	if( isset( $wgArticle ) &&
		isset( $wgTitle ) &&
		($wgTitle->quickUserCan( 'edit' )
			&& ( $wgTitle->exists()
				|| $wgTitle->quickUserCan( 'create' ) ) ) ) {
		$output->addLink(
			array(
				'rel' => 'alternate',
				'type' => 'application/wiki',
				'title' => wfMsg( 'edit' ),
				'href' => $wgTitle->getFullURL( 'action=edit' ) ) );
	}
	return true;
}
