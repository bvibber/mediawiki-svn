<?php
/**
 * Page history
 * 
 * Split off from Article.php and Skin.php, 2003-12-22
 * @package MediaWiki
 */

/**
 * @todo document
 * @package MediaWiki
 */
class PageHistory {
	var $mArticle, $mTitle, $mSkin;
	var $lastline, $lastdate;
	var $linesonpage;
	function PageHistory( $article ) {
		$this->mArticle =& $article;
		$this->mTitle =& $article->mTitle;
	}

	# This shares a lot of issues (and code) with Recent Changes

	function history() {
		global $wgUser, $wgOut, $wgLang;

		# If page hasn't changed, client can cache this

		if( $wgOut->checkLastModified( $this->mArticle->getTimestamp() ) ){
			# Client cache fresh and headers sent, nothing more to do.
			return;
		}
		$fname = 'PageHistory::history';
		wfProfileIn( $fname );

		$wgOut->setPageTitle( $this->mTitle->getPRefixedText() );
		$wgOut->setSubtitle( wfMsg( 'revhistory' ) );
		$wgOut->setArticleFlag( false );
		$wgOut->setArticleRelated( true );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );

		$id = $this->mTitle->getArticleID();
		if( $id == 0 ) {
			$wgOut->addHTML( wfMsg( 'nohistory' ) );
			wfProfileOut( $fname );
			return;
		}

		list( $limit, $offset ) = wfCheckLimits();

		/* Check one extra row to see whether we need to show 'next' and diff links */
		$limitplus = $limit + 1;

		$namespace = $this->mTitle->getNamespace();
		$title = $this->mTitle->getText();

		$db =& wfGetDB( DB_SLAVE );
		$use_index = $db->useIndexClause( 'page_timestamp' );
		$revision = $db->tableName( 'revision' );

		$sql = "SELECT rev_id,rev_user," .
		  "rev_comment,rev_user_text,rev_timestamp,rev_minor_edit ".
		  "FROM $revision $use_index " .
		  "WHERE rev_page=$id " .
		  "ORDER BY inverse_timestamp ".$db->limitResult($limitplus,$offset);
		$res = $db->query( $sql, $fname );

		$revs = $db->numRows( $res );

		if( $revs < $limitplus ) // the sql above tries to fetch one extra
			$this->linesonpage = $revs;
		else
			$this->linesonpage = $revs - 1;

		$atend = ($revs < $limitplus);

		$this->mSkin = $wgUser->getSkin();
		$numbar = wfViewPrevNext(
			$offset, $limit,
			$this->mTitle->getPrefixedText(),
			'action=history', $atend );
		$s = $numbar;
		if($this->linesonpage > 0) {
			$submitpart1 = '<input class="historysubmit" type="submit" accesskey="'.wfMsg('accesskey-compareselectedversions').
			'" title="'.wfMsg('tooltip-compareselectedversions').'" value="'.wfMsg('compareselectedversions').'"';
			$this->submitbuttonhtml1 = $submitpart1 . ' />';
			$this->submitbuttonhtml2 = $submitpart1 . ' id="historysubmit" />';
		}
		$s .= $this->beginHistoryList();
		$counter = 1;
		while ( $line = $db->fetchObject( $res ) ) {
			$s .= $this->historyLine(
				$line->rev_timestamp, $line->rev_user,
				$line->rev_user_text, $namespace,
				$title, $line->rev_id,
				$line->rev_comment, ( $line->rev_minor_edit > 0 ),
				$counter,
				($counter == 1 && $offset == 0)
			);
			$counter++;
		}
		$s .= $this->endHistoryList( !$atend );
		$s .= $numbar;
		$wgOut->addHTML( $s );
		wfProfileOut( $fname );
	}

	function beginHistoryList() {
		global $wgTitle;
		$this->lastdate = $this->lastline = '';
		$s = '<p>' . wfMsg( 'histlegend' ) . '</p>';
		$s .= '<form action="' . $wgTitle->escapeLocalURL( '-' ) . '" method="get">';
		$prefixedkey = htmlspecialchars($wgTitle->getPrefixedDbKey());
		$s .= "<input type='hidden' name='title' value=\"{$prefixedkey}\" />\n";
		$s .= !empty($this->submitbuttonhtml1) ? $this->submitbuttonhtml1."\n":'';
		$s .= '<ul id="pagehistory">';
		return $s;
	}

	function endHistoryList( $skip = false ) {
		$last = wfMsg( 'last' );

		$s = $skip ? '' : preg_replace( "/!OLDID![0-9]+!/", $last, $this->lastline );
		$s .= '</ul>';
		$s .= !empty($this->submitbuttonhtml2) ? $this->submitbuttonhtml2 : '';
		$s .= '</form>';
		return $s;
	}

	function historyLine( $ts, $u, $ut, $ns, $ttl, $oid, $c, $isminor, $counter = '', $latest = false ) {
		global $wgLang, $wgContLang;

		$artname = Title::makeName( $ns, $ttl );
		$last = wfMsg( 'last' );
		$cur = wfMsg( 'cur' );
		$cr = wfMsg( 'currentrev' );

		if ( $oid && $this->lastline ) {
			$ret = preg_replace( "/!OLDID!([0-9]+)!/", $this->mSkin->makeKnownLink(
			  $artname, $last, "diff=\\1&oldid={$oid}",'' ,'' ,' tabindex="'.$counter.'"' ), $this->lastline );
		} else {
			$ret = '';
		}
		$dt = $wgLang->timeanddate( $ts, true );

		if ( $oid ) {
			$q = 'oldid='.$oid;
		} else {
			$q = '';
		}
		$link = $this->mSkin->makeKnownLink( $artname, $dt, $q );

		if ( 0 == $u ) {
			$ul = $this->mSkin->makeKnownLink( $wgContLang->specialPage( 'Contributions' ),
				htmlspecialchars( $ut ), 'target=' . urlencode( $ut ) );
		} else {
			$ul = $this->mSkin->makeLink( $wgContLang->getNsText(
				Namespace::getUser() ) . ':'.$ut , htmlspecialchars( $ut ) );
		}

		$s = '<li>';
		if ( $oid && !$latest ) {
			$curlink = $this->mSkin->makeKnownLink( $artname, $cur,
			  'diff=0&oldid='.$oid );
		} else {
			$curlink = $cur;
		}
		$arbitrary = '';
		if( $this->linesonpage > 1) {
			# XXX: move title texts to javascript
			$checkmark = '';
			if ( !$oid || $latest ) {
				$arbitrary = '<input type="radio" style="visibility:hidden" name="oldid" value="'.$oid.'" title="'.wfMsg('selectolderversionfordiff').'" />';
				$checkmark = ' checked="checked"';
			} else {
				if( $counter == 2 ) $checkmark = ' checked="checked"';
				$arbitrary = '<input type="radio" name="oldid" value="'.$oid.'" title="'.wfMsg('selectolderversionfordiff').'"'.$checkmark.' />';
				$checkmark = '';
			}
			$arbitrary .= '<input type="radio" name="diff" value="'.$oid.'" title="'.wfMsg('selectnewerversionfordiff').'"'.$checkmark.' />';
		}
		$s .= "({$curlink}) (!OLDID!{$oid}!) $arbitrary {$link} <span class='user'>{$ul}</span>";
		$s .= $isminor ? ' <span class="minor">'.wfMsg( "minoreditletter" ).'</span>': '' ;


		if ( '' != $c && '*' != $c ) {
			$c = $this->mSkin->formatcomment($c,$this->mTitle);
			$s .= " <em>($c)</em>";
		}
		$s .= '</li>';

		$this->lastline = $s;
		return $ret;
	}

}

?>
