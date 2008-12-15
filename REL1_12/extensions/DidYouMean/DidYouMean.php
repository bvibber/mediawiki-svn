<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

require_once( 'DYMNorm.php' );

$wgExtensionCredits['other'][] = array( 'name' => 'DidYouMean', 'author' => 'hippietrail (Andrew Dunbar)' );

# do database lookup from these
$wgHooks['ArticleNoArticleText'][] = 'wfDymArticleNoArticleText';
$wgHooks['SpecialSearchNogomatch'][] = 'wfDymSpecialSearchNogomatch';

# db lookup + parse existing {{see}} and add enhanced one with db results
$wgHooks['ParserBeforeStrip'][] = 'wfDymParserBeforeStrip';

# handle delete
$wgHooks['ArticleDelete'][] = 'wfDymArticleDelete';

# handle move
$wgHooks['TitleMoveComplete'][] = 'wfDymTitleMoveComplete';

# handle create / edit
$wgHooks['AlternateEdit'][] = 'wfDymAlternateEdit';
$wgHooks['ArticleSaveComplete'][] = 'wfDymArticleSaveComplete';

# handle undelete
$wgHooks['ArticleUndelete'][] = 'wfDymArticleUndelete';

# set this in LocalSettings.php
$wgDymUseSeeTemplate = false;

# TODO this is called even when editing a new page

function wfDymArticleNoArticleText( &$article, &$text ) {
	wfDebug( 'HIPP: ' . __METHOD__ . "\n" );

	$sees = wfDymLookup( 0, $article->getTitle()->getText() );

	sort($sees);

	if (count($sees))
		$text = build_sees($sees) . $text;

	return true;
}

# this is called when using the Go/Search box but it is not called when entering
# a URL for a non-existing article

function wfDymSpecialSearchNogomatch( &$title ) {
	global $wgOut;

	wfDebug( 'HIPP: ' . __METHOD__ . "\n" );

	$sees = wfDymLookup( 0, $title->getText() );

	sort($sees);

	if (count($sees))
		$wgOut->addWikiText( build_sees($sees) );

	return true;
}

# this is called per chunk of wikitext, not per article

function wfDymParserBeforeStrip( &$parser, &$text, &$stripState ) {
	#wfDebug( 'HIPP: ' . __METHOD__ . "\n" );

	# if revisionid is 0 this is not an article chunk
	if( isset( $parser->mDymFirstChunk ) || !$parser->getVariableValue('revisionid') || $parser->getVariableValue('namespace'))
		return true;

	$parser->mDymFirstChunk = 'done';

	$title = $parser->getTitle();
	$parser->mDymSees = wfDymLookup( $title->getArticleID(), $title->getText() );

	if (preg_match( "/{{[sS]ee\|([^}]*)}}/", $text, $see )) {
		wfDebug( "HIPP: see Hit\n" );
		$sees = explode("|", $see[1]);
	} elseif (preg_match( "/{{[xX]see(\|[^}]*)}}/", $text, $see )) {
		wfDebug( "HIPP: xsee Hit\n" );
		preg_match_all( "/\|\[\[([^]|]*)(?:\|([^|]*))?\]\](?: \(([^)]*)\))?/", $see[1], $ma );
		$sees = $ma[1];
	} else {
		wfDebug( "HIPP: (x)see Miss\n" );
		# there's no {{see}} in this chunk of wikitext
		# if this is the 1st chunk of the article itself we can put an empty {{see}} there.
		$text = "{{see|}}\n" . $text;
		$sees = array();
	}

	# normalize entities and urlencoding to pure utf-8
	foreach ($sees as &$value)
		$value = urldecode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));

	wfDebug( 'HIPP: Parser: ' . utf8_decode(implode(', ', $sees)) . "\n" );
	wfDebug( 'HIPP: DBase:  ' . utf8_decode(implode(', ', $parser->mDymSees)) . "\n" );

	# add in the stuff from the database lookup
	$sees = array_unique(array_merge($sees, $parser->mDymSees));
	sort($sees);

	wfDebug( 'HIPP: Merged: ' . utf8_decode(implode(', ', $sees)) . "\n" );

	# TODO is it better to use $parser->insertStripItem() ?

	if (count($sees))
		$built_sees = build_sees($sees);
	else
		$built_sees = '';

	$text = preg_replace(
		'/{{[xX]?[sS]ee\|[^}]*}}/',
		#$built_sees . '<div style="text-decoration:line-through">$0</div>',
		$built_sees,
		$text );

	return true;
}

# turn the array of titles into some wikitext we can add to an article

function build_sees( $sees ) {
	global $wgDymUseSeeTemplate;

	if ($wgDymUseSeeTemplate == true)
		return '{{see|' . implode('|', $sees) . '}}';
	else
		return '<div>\'\'See also:\'\' \'\'\'[[' . implode(']]\'\'\', \'\'\'[[', $sees) . ']]\'\'\'</div>';
}

# pass pageid = 0 to lookup by normtitle

function wfDymLookup( $pageid, $title ) {
	wfDebug( 'HIPP: ' . __METHOD__ . "\n" );

	$sees = array();

	$dbr = wfGetDB( DB_SLAVE );

	if ( $dbr->tableExists( 'dympage' ) &&  $dbr->tableExists( 'dymnorm' ) ) {
		$normid = false;

		if ($pageid) {
			wfDebug( "HIPP: lookup by pageid: $pageid\n" );
			$normid = $dbr->selectField(
				array( 'page', 'dympage' ),
				'dp_normid',
				array( 'page_id = dp_pageid', 'page_id' => $pageid )
			);
		} else {
			wfDebug( "HIPP: lookup by normtitle: " . wfDymNormalise($title) . "\n" );
			$normid = $dbr->selectField(
				'dymnorm',
				'dn_normid',
				array( 'dn_normtitle' => wfDymNormalise($title) )
			);
		}

		if ($normid) {
			$res = $dbr->select(
					/* FROM   */ array( 'page', 'dympage' ),
					/* SELECT */ 'page_title',
					/* WHERE  */ array( 'page_id = dp_pageid', 'dp_normid' => $normid )
			);
			
			$nr = $dbr->numRows( $res );

			if ($nr == 0) {
				wfDebug( "HIPP: DB New Miss\n" );
			} else {
				wfDebug( "HIPP: DB New  Hit\n" );

				# accumulate the db results
				while( $o = $dbr->fetchObject( $res ) ) {
					$t2 = str_replace('_', ' ', $o->page_title);
					$dbo = utf8_decode($t2);
					if ($title != $t2) {
						array_push( $sees, $t2 );
						$dbo = '++ ' . $dbo;
					}
					else
						$dbo = '  (' . $dbo . ')';
					wfDebug( "HIPP: $dbo\n" );
				}

				$dbr->freeResult( $res );
			}
		}
	} else {
		wfDebug( "HIPP: No dympage or dymnorm table\n" );
	}

	return $sees;
}

function wfDymArticleInsertComplete( &$article, &$user, $text, $summary, $isminor, $watchthis, $something ) {

	if ($article->getTitle()->getNamespace() != 0 || $article->isRedirect() == true)
		return true;

	wfDoInsert( $article->getID(), $article->getTitle()->getText() );

	return true;
}

function dymArticleUndelete( &$title, &$create ) {

	if ($create == false || $title->getNamespace() != 0)
		return true;

	# TODO it's not possible to detect if the undeleted article is a redirect!
	#$artic1e = new Article( $title );
	#if ($article->isRedirect( $article->getContent() )) {
	#	return true;
	#}

	doInsert( $title->getArticleId(), $title->getText() );

	return true;
}

function wfDymArticleDelete( $article, $user, $reason ) {

	if ($article->getTitle()->getNamespace() != 0 || $article->isRedirect() == true)
		return true;

	wfDoDelete( $article->getID() );

	return true;
}

function wfDymTitleMoveComplete( &$title, &$nt, &$wgUser, &$pageid, &$redirid ) {
	$oldtitletext = $title->getText();
	$oldns = $title->getNamespace();
	$newtitletext = $nt->getText();
	$newns = $nt->getNamespace();

	wfDebug( 'HIPP: ' . __METHOD__ . "\n" );

	if ($oldns != 0 && $newns != 0)
		return true;

	# TODO we can't always check if we're moving a redirect because the old article's content
	# TODO  has already been replaced with the redirect to the new title but a
	# TODO  new title's content is still "noarticletext" at this point!
	#$a1 = new Article( $title );
	#$a2 = new Article( $nt );
	#wfDebug( "HIPP: getContent() for isRedirect()\n\tfrom <<<" . $a1->getContent() . ">>>\n\t  to <<<" . $a2->getContent() . ">>>\n" );
	#if ($a1->isRedirect( $a->getContent() )) {
	#	wfDebug( "HIPP: moving a redirect (?)\n" );
	#	return true;
	#}

	if ($oldns == 0 && $newns == 0) {
		wfDoUpdate( $pageid, $newtitletext );
	} elseif ($oldns == 0) {
		wfDoDelete( $pageid );
	} elseif ($newns == 0) {
		wfDoInsert( $pageid, $newtitletext );
	}

	return true;
}

# called at action=edit. can detect if we're about to edit a redirect

function wfDymAlternateEdit( $editpage ) {
	global $wgParser;

	if ($editpage->mArticle->isRedirect())
		$wgParser->mDymRedirBeforeEdit = true;

	return 1;
}

# called at end of action=submit

function wfDymArticleSaveComplete( $article, $user, $text, $summary, $isminor, $dunno1, $dunno2, $flags ) {
	global $wgParser;

	if ($article->getTitle()->getNamespace() != 0)
		return true;

	if ($article->isRedirect($text)) {
		if (!$wgParser->mDymRedirBeforeEdit && !($flags & EDIT_NEW))
			wfDoDelete( $article->getID() );
	} else {
		if ($wgParser->mDymRedirBeforeEdit || $flags & EDIT_NEW)
			wfDoInsert( $article->getID(), $article->getTitle()->getText() );
	}

	$wgParser->mDymRedirBeforeEdit = false;

	return true;
}

function wfDoInsert( $pageid , $title ) {
	wfDebug( 'HIPP: ' . __METHOD__ . " INSERT\n" );
	$dbw = wfGetDB( DB_MASTER );

	$norm = wfDymNormalise($title);

	# find or create normid for the new title
	$normid = $dbw->selectField( 'dymnorm', 'dn_normid', array( 'dn_normtitle' => $norm ) );
	if ($normid) {
		wfDebug( "HIPP: old: $title ->\t$norm = $normid\n" );
	} else {
		$nsvid = $dbw->nextSequenceValue( 'dymnorm_dn_normid_seq' );
		$dbw->insert( 'dymnorm', array( 'dn_normid' => $nsvid, 'dn_normtitle' => $norm ) );
		$normid = $dbw->insertId();
		wfDebug( "HIPP: NEW: $title ->\t$norm = $normid\n" );
	}
	$dbw->insert( 'dympage', array( 'dp_pageid' => $pageid, 'dp_normid' => $normid ) );

	# touch all pages which will now link here
	wfTouchPages( "dp_normid=$normid" );

}


function wfTouchPages( $condition ) {
	global $wgDBtype;

	$dbw = wfGetDB( DB_MASTER );
	$page = $dbw->tableName('page');
	$dpage = $dbw->tableName('dympage');

	$whereclause = "WHERE page_id = dp_pageid AND $condition";
	if ($wgDBtype == 'postgres') {
		$sql = "UPDATE $page SET page_touched=now() FROM $dpage $whereclause";
	} else {
		$sql = "UPDATE $page, $dpage SET page_touched = " . $dbw->addQuotes( $dbw->timestamp() ) . $whereclause;
	}

	$dbw->query( $sql, __METHOD__ );

}

function wfDoDelete( $pageid ) {
	wfDebug( 'HIPP: ' . __METHOD__ . " DELETE\n" );
	$dbw = wfGetDB( DB_MASTER );

	$normid = $dbw->selectField( 'dympage', 'dp_normid', array('dp_pageid' => $pageid) );

	$dbw->delete( 'dympage', array('dp_pageid' => $pageid) );

	$count = $dbw->selectField( 'dympage', 'COUNT(*)', array('dp_normid' => $normid) );

	if ($count == 0)
		$dbw->delete( 'dymnorm', array('dn_normid' => $normid) );

	# touch all pages which will now link here
	wfTouchPages( "dp_normid=$normid" );
}

function wfDoUpdate( $pageid, $title ) {
	wfDebug( 'HIPP: ' . __METHOD__ . " MOVE\n" );
	$dbw = wfGetDB( DB_MASTER );

	$norm = wfDymNormalise($title);

	$normid = $dbw->selectField( 'dymnorm', 'dn_normid', array( 'dn_normtitle' => $norm ) );
	if ($normid) {
		wfDebug( "HIPP: old: $title ->\t$norm = $normid\n" );
	} else {
		$nsvid = $dbw->nextSequenceValue( 'dymnorm_dn_normid_seq' );
		$dbw->insert( 'dymnorm', array( 'dn_normid' => $nsvid, 'dn_normtitle' => $norm ) );
		$normid = $dbw->insertId();
		wfDebug( "HIPP: NEW: $title ->\t$norm = $normid\n" );
	}

	$oldnormid = $dbw->selectField( 'dympage', 'dp_normid', array('dp_pageid' => $pageid) );

	if ($oldnormid != $normid) {
		$dbw->update( 'dympage', array( 'dp_normid' => $normid ), array( 'dp_pageid' => $pageid ) );

		$count = $dbw->selectField( 'dympage', 'COUNT(*)', array('dp_normid' => $oldnormid) );

		if ($count == 0)
			$dbw->delete( 'dymnorm', array('dn_normid' => $oldnormid) );

		# touch all pages which linked to the old name or will link to the new one
		wfTouchPages( "(dp_normid=$normid OR dp_normid=$oldnormid)" );

	}
}


