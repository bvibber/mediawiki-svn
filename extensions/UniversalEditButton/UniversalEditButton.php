<?php

// For experimental browser widget :)

$wgExtensionCredits['other'][] = array(
	'name'           => 'UniversalEditButton',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:UniversalEditButton',
	'svn-date'       => '$LastChangedDate$',
	'svn-revision'   => '$LastChangedRevision$',
	'description'    => 'For experimental browser widget :)',
	'author'         => 'Brion Vibber',
);

$wgHooks['BeforePageDisplay'][] = 'efUniversalEditLink';

function efUniversalEditLink( $output ) {
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
