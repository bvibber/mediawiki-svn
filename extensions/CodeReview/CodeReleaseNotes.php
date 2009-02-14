<?php

class CodeReleaseNotes extends CodeView {
	function __construct( $repoName ) {
		global $wgRequest;
		parent::__construct( $repoName );
		$this->mRepo = CodeRepository::newFromName( $repoName );
		$this->mPath = htmlspecialchars( trim( $wgRequest->getVal( 'path' ) ) );
		if( strlen($this->mPath) && $this->mPath[0] !== '/' ) {
			$this->mPath = "/{$this->mPath}"; // make sure this is a valid path
		}
		$this->mPath = preg_replace( '/\/$/', '', $this->mPath ); // kill last slash
		$this->mStartRev = $wgRequest->getIntOrNull('startrev');
		$this->mEndRev = $wgRequest->getIntOrNull('endrev');
	}
	
	function execute() {
		if( !$this->mRepo ) {
			$view = new CodeRepoListView();
			$view->execute();
			return;
		}
		$this->showForm();
		# Sanity/performance check...
		$lastRev = $this->mRepo->getLastStoredRev();
		if( $this->mStartRev < ($lastRev - 5000) ) $this->mStartRev = NULL;
		# Show notes if we have at least a starting revision
		if( $this->mStartRev ) {
			$this->showReleaseNotes();
		}
	}
	
	protected function showForm() {
		global $wgOut, $wgScript, $wgUser;
		$special = SpecialPage::getTitleFor( 'Code', $this->mRepo->getName().'/releasenotes' );
		$wgOut->addHTML( 
			Xml::openElement( 'form', array( 'action' => $wgScript, 'method' => 'get' ) ) .
			"<fieldset><legend>".wfMsgHtml('code-release-legend')."</legend>" .
				Xml::hidden( 'title', $special->getPrefixedDBKey() ) . '<b>' .
				Xml::inputlabel( wfMsg("code-release-startrev"), 'startrev', 'startrev', 10, $this->mStartRev ) .
				'</b>&nbsp;' .
				Xml::inputlabel( wfMsg("code-release-endrev"), 'endrev', 'endrev', 10, $this->mEndRev ) .
				'&nbsp;' .
				Xml::inputlabel( wfMsg("code-pathsearch-path"), 'path', 'path', 45, $this->mPath ) .
				'&nbsp;' .
				Xml::submitButton( wfMsg( 'allpagessubmit' ) ) . "\n" .
			"</fieldset>" . Xml::closeElement( 'form' )
		);
	}
	
	protected function showReleaseNotes() {
		global $wgOut;
		$linker = new CodeCommentLinkerWiki( $this->mRepo );
		$dbr = wfGetDB( DB_SLAVE );
		if( $this->mEndRev ) {
			$where = 'cr_id BETWEEN '.intval($this->mStartRev).' AND '.intval($this->mEndRev);
		} else {
			$where = 'cr_id >= '.intval($this->mStartRev);
		}
		if( $this->mPath ) {
			$where .= ' AND (cr_path LIKE '.$dbr->addQuotes( $dbr->escapeLike("{$this->mPath}/").'%');
			$where .= ' OR cr_path = '.$dbr->addQuotes($this->mPath).')';
		}
		# Select commits within this range...
		$res = $dbr->select( 'code_rev', array('cr_message','cr_author'),
			array( 
				'cr_repo_id' => $this->mRepo->getId(), // this repo
				"cr_status NOT IN('reverted','deferred','fixme')", // not reverted/deferred/fixme
				"cr_message != ''",
				$where // in range
			),
			__METHOD__,
			array( 'ORDER BY' => 'cr_id DESC' )
		);
		$wgOut->addHTML( '<ul>' );
		# Output any relevant seeming commits...
		while( $row = $dbr->fetchObject( $res ) ) {
			$summary = htmlspecialchars($row->cr_message);
			# Add this commit summary if needed
			if( $this->isRelevant( $summary ) ) {
				# Asterixs often used as point-by-point bullets
				$summary = strtr( $summary, '*', "\n" );
				# Keep it short if possible...
				$blurbs = explode("\n",$summary);
				$summary = "";
				foreach( $blurbs as $blurb ) {
					if( $this->isRelevant( $blurb ) ) {
						$summary .= trim($blurb) . "\n";
					}
				}
				# Anything left? (this can happen with some heuristics)
				if( $summary ) {
					$summary = nl2br( trim($summary) ); // Newlines -> <br/>
					$wgOut->addHTML( "<li>" );
					$wgOut->addWikiText( $linker->link($summary)." ''(".htmlspecialchars($row->cr_author).")''" );
					$wgOut->addHTML( "</li>\n" );
				}
			}
		}
		$wgOut->addHTML( '</ul>' );
	}
	
	private function isRelevant( $summary ) {
		# Fixed a bug?
		if( preg_match( '/\bbug #?(\d+)\b/i', $summary, $m ) )
			return true;
		# Config var?
		if( preg_match( '/\b\$wg[a-bA-Z]{3,100}\b/', $summary, $m ) )
			return true;
		# Sanity check: summary cannot be *too* short to be useful
		if( mb_strlen($summary) < 40 )
			return false;
		# All caps words? like "BREAKING CHANGE"
		if( preg_match( '/\b[A-Z]{8,20}\b/', $summary, $m ) )
			return true;
		# Longish summary :)
		if( mb_strlen($summary) > 250 )
			return true;
		# Otherwise false
		return false;
	}
}
