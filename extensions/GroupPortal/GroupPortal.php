<?php

$wgExtensionCredits['other'][] = array(
    'name'    => "GroupPortal",
	'version' => '1.0',
	'author'  => 'Tim Laqua',
	'url'     => 'http://www.mediawiki.org/wiki/Extension:GroupPortal'
);

$wgHooks['ArticlePageDataBefore'][] = 'efGroupPortal_ArticlePageDataBefore';

function efGroupPortal_ArticlePageDataBefore ( &$article, &$fields ) {
	$mainTitle = Title::newMainPage();
	$title = $article->getTitle();
	if ( $title->getFullText() == $mainTitle->getFullText() ) {
		$groupPortalText = wfMsgForContentNoTrans('groupportal');
		#echo 'groupportal:' . $groupPortalText;
		
		preg_match_all('/^(.+)\|(.+)$/', $groupPortalText, $groupPortals, PREG_SET_ORDER );
		
		global $wgUser;
		$groups = $wgUser->getGroups();
		
		$targetPortal = '';
		
		foreach ($groupPortals as $groupPortal) {
			#echo '<br />Testing: ' . $groupPortal[1];
			
			foreach ($groups as $group) {
				#echo '<br />group: ' . $group;
			}
			if ( in_array($groupPortal[1], $groups ) ) {
				$targetPortal = $groupPortal[2];
				#echo 'Redirecting to: ' . $groupPortal[2];
			}
		}
		
		if ( !empty( $targetPortal ) ) {
			#echo 'trying to redirect!:<br />';
			$target = Title::newFromText($targetPortal);
			
			if( is_object( $target ) ) {
				header('Location: ' . $target->getLocalURL() );
			}
		}
	}
	return true;
}