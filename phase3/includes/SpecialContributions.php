<?php
/**
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

/**
 * Special page "user contributions".
 * Shows a list of the contributions of a user.
 *
 * @return	none
 * @param	string	$par	(optional) user name of the user for which to show the contributions
 */
function wfSpecialContributions( $par = '' ) {
	global $wgUser, $wgOut, $wgLang, $wgContLang, $wgRequest;
	$fname = "wfSpecialContributions";

	if( $par )
		$target = $par;
	else
		$target = $wgRequest->getVal( 'target' );

	if ( "" == $target ) {
		$wgOut->errorpage( "notargettitle", "notargettext" );
		return;
	}

	# FIXME: Change from numeric offsets to date offsets
	list( $limit, $offset ) = wfCheckLimits( 50, "" );
	$offlimit = $limit + $offset;
	$querylimit = $offlimit + 1;
	$hideminor = ($wgRequest->getVal( 'hideminor' ) ? 1 : 0);
	$sk = $wgUser->getSkin();
	$dbr =& wfGetDB( DB_SLAVE );
	$userCond = "";

	$nt = Title::newFromURL( $target );
	if ( !$nt ) {
		$wgOut->errorpage( "notargettitle", "notargettext" );
		return;
	}
	$nt->setNamespace( Namespace::getUser() );

	$id = User::idFromName( $nt->getText() );

	if ( 0 == $id ) {
		$ul = $nt->getText();
	} else {
		$ul = $sk->makeLinkObj( $nt, htmlspecialchars( $nt->getText() ) );
		$userCond = "=" . $id;
	}
	$talk = $nt->getTalkPage();
	if( $talk ) {
		$ul .= " (" . $sk->makeLinkObj( $talk, $wgLang->getNsText(Namespace::getTalk(0)) ) . ")";
	}


	if ( $target == 'newbies' ) {
		# View the contributions of all recently created accounts
		$max = $dbr->selectField( 'user', 'max(user_id)', false, $fname );
		$userCond = ">" . ($max - $max / 100);
		$ul = wfMsg ( 'newbies' );
		$id = 0;
	}

	$wgOut->setSubtitle( wfMsg( "contribsub", $ul ) );

	if ( $hideminor ) {
		$minorQuery = "AND rev_minor_edit=0";
		$mlink = $sk->makeKnownLink( $wgContLang->specialPage( "Contributions" ),
	  	  WfMsg( "show" ), "target=" . htmlspecialchars( $nt->getPrefixedURL() ) .
		  "&offset={$offset}&limit={$limit}&hideminor=0" );
	} else {
		$minorQuery = "";
		$mlink = $sk->makeKnownLink( $wgContLang->specialPage( "Contributions" ),
	  	  WfMsg( "hide" ), "target=" . htmlspecialchars( $nt->getPrefixedURL() ) .
		  "&offset={$offset}&limit={$limit}&hideminor=1" );
	}

	extract( $dbr->tableNames( 'old', 'cur' ) );
	if ( $userCond == "" ) {
		$condition = "rev_user_text=" . $dbr->addQuotes( $nt->getText() );
	} else {
		$condition = "rev_user {$userCond}";
	}
	$page = $dbr->tableName( 'page' );
	$revision = $dbr->tableName( 'revision' );
	$sql = "SELECT
		page_namespace,page_title,page_is_new,page_latest,
		rev_id,rev_timestamp,rev_comment,rev_minor_edit,rev_user_text
		FROM $page,$revision
		WHERE page_id=rev_page AND $condition $minorQuery " .
	  "ORDER BY inverse_timestamp LIMIT {$querylimit}";
	$res = $dbr->query( $sql, $fname );
	$numRows = $dbr->numRows( $res );

	$top = wfShowingResults( $offset, $limit );
	$wgOut->addHTML( "<p>{$top}\n" );

	$sl = wfViewPrevNext( $offset, $limit,
	  $wgContLang->specialpage( "Contributions" ),
	  "hideminor={$hideminor}&target=" . wfUrlEncode( $target ),
	  ($numRows) <= $offlimit);

	$shm = wfMsg( "showhideminor", $mlink );
	$wgOut->addHTML( "<br />{$sl} ($shm)</p>\n");


	if ( 0 == $numRows ) {
		$wgOut->addHTML( "\n<p>" . wfMsg( "nocontribs" ) . "</p>\n" );
		return;
	}

	$wgOut->addHTML( "<ul>\n" );
	while( $obj = $dbr->fetchObject( $res ) ) {
		ucListEdit( $sk,
			$obj->page_namespace,
			$obj->page_title,
			$obj->rev_timestamp,
			($obj->rev_id == $obj->page_latest),
			$obj->rev_comment,
			($obj->rev_minor_edit),
			$obj->page_is_new,
			$obj->rev_user_text,
			$obj->rev_id );
	}
	$wgOut->addHTML( "</ul>\n" );

	# Validations
	global $wgUseValidation;
	if( $wgUseValidation ) {
		require_once( 'SpecialValidate.php' );
		$val = new Validation ;
		$val = $val->countUserValidations ( $id ) ;
		$wgOut->addHTML( wfMsg ( 'val_user_validations', $val ) );
	}
}


/**
 * Generates each row in the contributions list.
 *
 * Contributions which are marked "top" are currently on top of the history.
 * For these contributions, a [rollback] link is shown for users with sysop
 * privileges. The rollback link restores the most recent version that was not
 * written by the target user.
 * 
 * If the contributions page is called with the parameter &bot=1, all rollback
 * links also get that parameter. It causes the edit itself and the rollback
 * to be marked as "bot" edits. Bot edits are hidden by default from recent
 * changes, so this allows sysops to combat a busy vandal without bothering
 * other users.
 * 
 * @todo This would probably look a lot nicer in a table.
 */
function ucListEdit( $sk, $ns, $t, $ts, $topmark, $comment, $isminor, $isnew, $target, $oldid ) {
	global $wgLang, $wgOut, $wgUser, $wgRequest;
	$page = Title::makeName( $ns, $t );
	$link = $sk->makeKnownLink( $page, '' );
	$difftext = $topmarktext = '';
	if($topmark) {
		$topmarktext .= '<strong>' . wfMsg('uctop') . '</strong>';
		if(!$isnew) {
			$difftext .= $sk->makeKnownLink( $page, '(' . wfMsg('diff') . ')', 'diff=0' );
		} else {
			$difftext .= wfMsg('newarticle');
		}
		$sysop = $wgUser->isSysop();
		if($sysop ) {
			$extraRollback = $wgRequest->getBool( 'bot' ) ? '&bot=1' : '';
			# $target = $wgRequest->getText( 'target' );
			$topmarktext .= ' ['. $sk->makeKnownLink( $page,
		  	wfMsg( 'rollbacklink' ),
		  	'action=rollback&from=' . urlencode( $target ) . $extraRollback ) .']';
		}

	}
	if ( $oldid ) {
		$difftext= $sk->makeKnownLink( $page, '('.wfMsg('diff').')', 'diff=prev&oldid='.$oldid );
	} 
	$histlink='('.$sk->makeKnownLink($page,wfMsg('hist'),'action=history').')';

	if($comment) {

		$comment='<em>('. $sk->formatComment($comment, Title::newFromText($t) ) .')</em> ';

	}
	$d = $wgLang->timeanddate( $ts, true );

	if ($isminor) {
		$mflag = '<span class="minor">'.wfMsg( 'minoreditletter' ).'</span> ';
	} else {
		$mflag = '';
	}

	$wgOut->addHTML( "<li>{$d} {$histlink} {$difftext} {$mflag} {$link} {$comment} {$topmarktext}</li>\n" );
}

/**
 *
 */
function ucCountLink( $lim, $d ) {
	global $wgUser, $wgContLang, $wgRequest;

	$target = $wgRequest->getText( 'target' );
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgContLang->specialPage( "Contributions" ),
	  "{$lim}", "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}

/**
 *
 */
function ucDaysLink( $lim, $d ) {
	global $wgUser, $wgContLang, $wgRequest;

	$target = $wgRequest->getText( 'target' );
	$sk = $wgUser->getSkin();
	$s = $sk->makeKnownLink( $wgContLang->specialPage( "Contributions" ),
	  "{$d}", "target={$target}&days={$d}&limit={$lim}" );
	return $s;
}
?>
