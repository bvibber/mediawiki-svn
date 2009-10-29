<?php

/**
 * Simple script (read "hack") to import comments into Wikilog.
 */


/*
 * Since this script may fall in a public directory, it is disabled by
 * default, for security. Comment the die() statement below before using.
 */
die();


$options = array();
include_once '../../maintenance/commandLine.inc';

$commentdata = array(
	0 => array(
		'article' => 'Blog:Wikilog Title/Article Title',
		'parent' => null,
		'user' => 'WikiSysop',
		'time' => '20090101000000',
		'text' => 'Sample post.'
	),
	1 => array(
		'article' => 'Blog:Wikilog Title/Article Title',
		'parent' => 0,
		'user' => 'WikiSysop',
		'time' => '20090101000000',
		'text' => 'Sample reply.'
	)
);


wfOut( "Wikilog comment import.\n" );
wfLoadExtensionMessages( 'Wikilog' );

global $wgWikilogEnableComments;
assert( $wgWikilogEnableComments );

global $wgUser;
$wgUser = User::newFromName( wfMsgForContent( 'wikilog-auto' ), false );


function import() {
	global $wgUser;
	global $commentdata;

	foreach( $commentdata as $sid => &$source ) {
		$title = Title::newFromText( $source['article'] );
		$info = Wikilog::getWikilogInfo( $title );
		$item = WikilogItem::newFromInfo( $info );

		if ( !$item ) {
			wfOut( "Comment {$sid}: Wikilog article '{$source['article']}' doesn't exist.\n" );
			continue;
		}

		$talk = $info->getItemTitle()->getTalkPage();
		if ( !$talk->exists() ) {
			$talkpage = new WikilogCommentsPage( $talk, $info );
			$talkpage->doEdit(
				wfMsgForContent( 'wikilog-newtalk-text' ),
				wfMsgForContent( 'wikilog-newtalk-summary' ),
				EDIT_NEW | EDIT_SUPPRESS_RC, false, $wgUser
			);
			$titletext = $talk->getPrefixedText();
			wfOut( "Page '{$titletext}' created.\n" );
		}

		$parent = null;
		if ( !is_null( $source['parent'] ) ){
			if ( !isset( $commentdata[$source['parent']] ) ||
			     !isset( $commentdata[$source['parent']]['comment'] ) )
			{
				wfOut( "Comment {$sid}: Failed to get parent {$source['parent']}.\n" );
				continue;
			}
			$parent = $commentdata[$source['parent']]['comment']->getID();
		}

		$author = User::newFromName( $source['user'] );

		$comment = WikilogComment::newFromText( $item, $source['text'], $parent );
		$comment->setUser( $author );
		$comment->mTimestamp = $comment->mUpdated = $source['time'];
		$comment->saveComment();

		$cid = $comment->getID();
		wfOut( "Comment {$cid} to '{$source['article']}' by '{$source['user']}' posted.\n" );

		$source['comment'] =& $comment;
	}
}


import();

