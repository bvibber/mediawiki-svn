<?php

/**
 * Taken from /includes/ChangesList.php, customized to our needs.
 * See there for docs.
 */
class TickerList extends ChangesList {

	function __construct( $user ) {
		$this->skin = $user->getSkin();
		$this->preCacheMessages();
	}

	function insertDiffHist( &$s, &$rc, $unpatrolled ) {
		# Diff link
		if( $rc->mAttribs['rc_type'] == RC_NEW || $rc->mAttribs['rc_type'] == RC_LOG ) {
			$diffLink = strtoupper( $this->message['diff']{0} );
		} else {
			$rcidparam = $unpatrolled
				? array( 'rcid' => $rc->mAttribs['rc_id'] )
				: array();
			$diffLink = $this->skin->makeKnownLinkObj( $rc->getTitle(), strtoupper( $this->message['diff']{0} ),
				wfArrayToCGI( array(
					'curid' => $rc->mAttribs['rc_cur_id'],
					'diff'  => $rc->mAttribs['rc_this_oldid'],
					'oldid' => $rc->mAttribs['rc_last_oldid'] ),
					$rcidparam ),
				'', '', ' tabindex="'.$rc->counter.'"');
		}
		$s .= '('.$diffLink.') (';

		# History link
		$s .= $this->skin->makeKnownLinkObj( $rc->getTitle(), strtoupper( $this->message['hist']{0} ),
			wfArrayToCGI( array(
				'curid' => $rc->mAttribs['rc_cur_id'],
				'action' => 'history' ) ) );
		$s .= ') ';
	}

	function insertTimestamp(&$s, $rc) {
		global $wgLang;
		# Timestamp
		$s .= $wgLang->time( $rc->mAttribs['rc_timestamp'], true, true ) . ' ';
	}

	function insertUserRelatedLinks(&$s, &$rc) {
		$s .= ' (' . $this->skin->userLink( $rc->mAttribs['rc_user'], $rc->mAttribs['rc_user_text'] ) . ')';
	}

	function recentChangesLine( &$rc, $watched = false ) {
		global $wgContLang, $wgRCShowChangedSize;
	
		# Extract DB fields into local scope
		extract( $rc->mAttribs );

		$this->insertDateHeader( $s, $rc_timestamp );

		$s .= '<li>';

		// moved pages
		if( $rc_type == RC_MOVE || $rc_type == RC_MOVE_OVER_REDIRECT ) {
			$this->insertMove( $s, $rc );
		// log entries
		} elseif ( $rc_namespace == NS_SPECIAL ) {
			list( $specialName, $specialSubpage ) = SpecialPage::resolveAliasWithSubpage( $rc_title );
			if ( $specialName == 'Log' ) {
				$this->insertLog( $s, $rc->getTitle(), $specialSubpage );
			} else {
				wfDebug( "Unexpected special page in recentchanges\n" );
			}
		// all other stuff
		} else {
			$this->insertArticleLink($s, $rc, $unpatrolled, $watched);
			$s .= '<br /><small>';

			$this->insertTimestamp( $s, $rc );
			$this->insertDiffHist($s, $rc, $unpatrolled);
			$this->insertUserRelatedLinks( $s, $rc );

			if( $wgRCShowChangedSize ) {
				$s .= ( $rc->getCharacterDifference() == '' ? '' : ' ' . $rc->getCharacterDifference() );
			}

			$s .= '</small>';
		}



		$s .= "</li>\n";

		wfProfileOut( $fname.'-rest' );

		wfProfileOut( $fname );
		return $s;
	}
}
?>
