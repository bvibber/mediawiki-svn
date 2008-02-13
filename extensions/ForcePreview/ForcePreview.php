<?php
/**
* ForcePreview extension by Ryan Schmidt
*/

if(!defined('MEDIAWIKI')) {
	echo("This file is an extension to the MediaWiki software and is not a valid access point");
	die(1);
}

$wgExtensionCredits['other'][] = array(
	'name' => 'Force Preview',
	'version' => '1.0',
	'author' => 'Ryan Schmidt',
	#'description' => 'Force preview for unprivelaged users',
	'descriptionmsg' => 'forcepreview-desc',
	'url' => 'http://www.mediawiki.org/wiki/Extension:ForcePreview',
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['ForcePreview'] = $dir .'ForcePreview.i18n.php';
$wgAvailableRights[] = 'forcepreviewexempt';
$wgHooks['EditPageBeforeEditButtons'][] = 'efForcePreview';

function efForcePreview( &$editpage, &$buttons ) {
	global $wgUser;
	if( !$wgUser->isAllowed( 'forcepreviewexempt' ) && !$editpage->preview ) {
		wfLoadExtensionMessages( 'ForcePreview' );
		$buttons['save'] = str_replace( '/>', 'disabled="disabled" />', $buttons['save'] );
		$buttons['save'] = preg_replace(  '/value="' . wfMsg('savearticle') . '"/i', 'value="' . wfMsg('forcepreview') . '"', $buttons['save'] );
		if( $buttons['live'] !== '' ) {
			$buttons['preview'] = preg_replace( '/style="(.*?);?"/', 'style="$1; font-weight: bold;"', $buttons['preview'] ); #in case something else made it visible
			$buttons['live']  = str_replace( '/>', 'style="font-weight: bold" />', $buttons['live'] );
		} else {
			$buttons['preview'] = str_replace( '/>', 'style="font-weight: bold" />', $buttons['preview'] );
		}
	}
	return true;
}