<?php
#restrict to subpages
class WatchSubpages extends SpecialPage
{
	function WatchSubpages() {
		SpecialPage::SpecialPage( 'Watchsubpages', 'watchsubpages' );
	}

	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;

		wfLoadExtensionMessages( 'WatchSubpages' );

		$namespace = $wgRequest->getInt( 'namespace' );
		$guide = $wgRequest->getVal( 'guide' );
		if ( isset( $guide ) ) {
			$guidename = $guide;
		} elseif ( isset( $par ) ) {
			$guidename = $par;
		}

		$this->setHeaders();

#		$guidename = Title::newFromText( $guidename , NS_MAIN );

		if ( wfReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal( 'token' ), 'watchsubpages' ) ) {
			$titles = $this->extractTitles( $wgRequest->getArray( 'titles' ) );
			$current = $this->getWatchlist( $wgUser );
			$toWatch = array_diff( $titles, $current );
			$this->watchTitles( $toWatch, $wgUser );
			$wgUser->invalidateCache();
			$wgOut->addHtml( 'The following has been added to your watchlist.' );
			$this->showTitles( $toWatch, $wgOut, $wgUser->getSkin() );
		}
		$this->showForm( $wgOut, $wgUser, $namespace, trim($guidename, "/") );
	}

	/**
	 * Extract a list of titles from a blob of text, returning
	 * (prefixed) strings; unwatchable titles are ignored
	 *
	 * @param mixed $list
	 * @return array
	 */
	private function extractTitles( $list ) {
		$titles = array();
		if( !is_array( $list ) ) {
			$list = explode( "\n", trim( $list ) );
			if( !is_array( $list ) )
				return array();
		}
		foreach( $list as $text ) {
			$text = trim( $text );
			if( strlen( $text ) > 0 ) {
				$title = Title::newFromText( $text );
				if( $title instanceof Title && $title->isWatchable() )
					$titles[] = $title->getPrefixedText();
			}
		}
		return array_unique( $titles );
	}

	/**
	 * Prepare a list of titles on a user's watchlist (excluding talk pages)
	 * and return an array of (prefixed) strings
	 *
	 * @param User $user
	 * @return array
	 */
	private function getWatchlist( $user ) {
		$list = array();
		$dbr = wfGetDB( DB_MASTER );
		$res = $dbr->select(
			'watchlist',
			'*',
			array(
				'wl_user' => $user->getId(),
			),
			__METHOD__
		);
		if( $dbr->numRows( $res ) > 0 ) {
			while( $row = $dbr->fetchObject( $res ) ) {
				$title = Title::makeTitleSafe( $row->wl_namespace, $row->wl_title );
				if( $title instanceof Title && !$title->isTalkPage() )
					$list[] = $title->getPrefixedText();
			}
			$res->free();
		}
		return $list;
	}

	/**
	 * Add a list of titles to a user's watchlist
	 *
	 * $titles can be an array of strings or Title objects; the former
	 * is preferred, since Titles are very memory-heavy
	 *
	 * @param array $titles An array of strings, or Title objects
	 * @param User $user
	 */
	private function watchTitles( $titles, $user ) {
		$dbw = wfGetDB( DB_MASTER );
		$rows = array();
		foreach( $titles as $title ) {
			if( !$title instanceof Title )
				$title = Title::newFromText( $title );
			if( $title instanceof Title ) {
				$rows[] = array(
					'wl_user' => $user->getId(),
					'wl_namespace' => ( $title->getNamespace() & ~1 ),
					'wl_title' => $title->getDBkey(),
					'wl_notificationtimestamp' => null,
				);
				$rows[] = array(
					'wl_user' => $user->getId(),
					'wl_namespace' => ( $title->getNamespace() | 1 ),
					'wl_title' => $title->getDBkey(),
					'wl_notificationtimestamp' => null,
				);
			}
		}
		$dbw->insert( 'watchlist', $rows, __METHOD__, 'IGNORE' );
	}

	/**
	 * Print out a list of linked titles
	 *
	 * $titles can be an array of strings or Title objects; the former
	 * is preferred, since Titles are very memory-heavy
	 *
	 * @param array $titles An array of strings, or Title objects
	 * @param OutputPage $output
	 * @param Skin $skin
	 */
	private function showTitles( $titles, $output, $skin ) {
		$talk = wfMsgHtml( 'talkpagelinktext' );
		// Do a batch existence check
		$batch = new LinkBatch();
		foreach( $titles as $title ) {
			if( !$title instanceof Title )
				$title = Title::newFromText( $title );
			if( $title instanceof Title ) {
				$batch->addObj( $title );
				$batch->addObj( $title->getTalkPage() );
			}
		}
		$batch->execute();
		// Print out the list
		$output->addHtml( "<ul>\n" );
		foreach( $titles as $title ) {
			if( !$title instanceof Title )
				$title = Title::newFromText( $title );
			if( $title instanceof Title ) {
				$output->addHtml( "<li>" . $skin->makeLinkObj( $title )
				. ' (' . $skin->makeLinkObj( $title->getTalkPage(), $talk ) . ")</li>\n" );
			}
		}
		$output->addHtml( "</ul>\n" );
	}

	/**
	 * Show the standard watchlist editing form
	 *
	 * @param OutputPage $output
	 * @param User $user
	 * @param GuideName $guide
	 */
	private function showForm( $output, $user, $namespace, $guide ) {
		global $wgScript, $wgContLang;

		$self = SpecialPage::getTitleFor( 'Watchsubpages' );
		# Input boxes at the top
		$form .= Xml::openElement( 'div', array( 'class' => 'namespaceoptions' ) );
		$form .= Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) );
		$form .= Xml::hidden( 'title', $self->getPrefixedText() );
		$form .= Xml::openElement( 'table', array( 'id' => 'nsselect', 'class' => 'allpages' ) );
		$form .= "<tr>
				<td>" .
					Xml::label( 'Guide name:', 'nsfrom' ) .
				"</td>
				<td>" .
					Xml::input( 'guide', 20, htmlspecialchars ( $guide . '/' ), array( 'id' => 'nsfrom' ) ) .
				"</td>
			</tr>
			<tr>
				<td>" .
					Xml::label( wfMsg( 'namespace' ), 'namespace' ) .
				"</td>
				<td>" .
					Xml::namespaceSelector( $namespace, null ) .
					Xml::submitButton( wfMsg( 'allpagessubmit' ) ) .
				"</td>
				</tr>";
		$form .= Xml::closeElement( 'table' );
		$form .= Xml::closeElement( 'form' );
		$form .= Xml::closeElement( 'div' );

		$form .= Xml::openElement( 'form', array( 'method' => 'post',
			'action' => $self->getLocalUrl( 'guide=' . $guide ) ) );
		$form .= Xml::hidden( 'token', $user->editToken( 'watchsubpages' ) );
		$form .= '<fieldset><legend>Add titles to watchlist</legend>';
		$form .= 'Select the titles to add to your watchlist below. To add a title, check the box next to it, and click Add Titles.<br /><br />When checking or unchecking multiple titles, holding the shift key allows you to select consecutive checkboxes by clicking each end of the range to be checked.<br /><br />';
		foreach( $this->getPrefixlistInfo( $namespace, $guide . '/' ) as $namespace => $pages ) {
			$form .= '<h2>' . $this->getNamespaceHeading( $namespace ) . '</h2>';
			$form .= '<ul>';
			foreach( $pages as $dbkey => $redirect ) {
				$title = Title::makeTitleSafe( $namespace, $dbkey );
				$form .= $this->buildLine( $title, $redirect, $user->getSkin() );
			}
			$form .= '</ul>';
		}
		$form .= '<p>' . Xml::submitButton( 'Add Titles' ) . '</p>';
		$form .= '</fieldset></form>';
		$output->addHtml( $form );
	}

	/**
	 * Get a list of titles that are subpages of a given title, excluding talk pages,
	 * and return as a two-dimensional array with namespace, title and
	 * redirect status
	 *
	 * @param GuideName $guide
	 * @return array
	 */
	private function getPrefixlistInfo( $namespace = NS_MAIN, $guide ) {
		$prefixList = $this->getNamespaceKeyAndText($namespace, $guide);

		$titles = array();
		list( $prefixNS, $prefixKey, $guide ) = $prefixList;
		$dbr = wfGetDB( DB_MASTER );
		$res = $dbr->select( 'page',
			array( 'page_namespace', 'page_title', 'page_id', 'page_is_redirect' ),
			array(
				'page_namespace' => $prefixNS,
				'page_title LIKE \'' . $dbr->escapeLike( $prefixKey ) .'%\'',
			),
			__METHOD__,
			array(
				'ORDER BY'  => 'page_title',
				'USE INDEX' => 'name_title',
			)
		);
		if( $res && $dbr->numRows( $res ) > 0 ) {
			$cache = LinkCache::singleton();
			while( $row = $dbr->fetchObject( $res ) ) {
				$title = Title::makeTitleSafe( $row->page_namespace, $row->page_title );
				if( $title instanceof Title ) {
					// Update the link cache while we're at it
					if( $row->page_id ) {
						$cache->addGoodLinkObj( $row->page_id, $title );
					} else {
						$cache->addBadLinkObj( $title );
					}
					// Ignore non-talk
					if( !$title->isTalkPage() )
						$titles[$row->page_namespace][$row->page_title] = $row->page_is_redirect;
				}
			}
		}
		return $titles;
	}

	/**
	 * Get the correct "heading" for a namespace
	 *
	 * @param int $namespace
	 * @return string
	 */
	private function getNamespaceHeading( $namespace ) {
		return $namespace == NS_MAIN
			? wfMsgHtml( 'blanknamespace' )
			: htmlspecialchars( $GLOBALS['wgContLang']->getFormattedNsText( $namespace ) );
	}

	/**
	 * @param int $ns the namespace of the article
	 * @param string $text the name of the article
	 * @return array( int namespace, string dbkey, string pagename ) or NULL on error
	 * @static (sort of)
	 * @access private
	 */
	function getNamespaceKeyAndText ($ns, $text) {
		if ( $text == '' )
			return array( $ns, '', '' ); # shortcut for common case

		$t = Title::makeTitleSafe($ns, $text);
		if ( $t && $t->isLocal() ) {
			return array( $t->getNamespace(), $t->getDBkey(), $t->getText() );
		} else if ( $t ) {
			return NULL;
		}

		# try again, in case the problem was an empty pagename
		$text = preg_replace('/(#|$)/', 'X$1', $text);
		$t = Title::makeTitleSafe($ns, $text);
		if ( $t && $t->isLocal() ) {
			return array( $t->getNamespace(), '', '' );
		} else {
			return NULL;
		}
	}

	/**
	 * Build a single list item containing a check box selecting a title
	 * and a link to that title, with various additional bits
	 *
	 * @param Title $title
	 * @param bool $redirect
	 * @param Skin $skin
	 * @return string
	 */
	private function buildLine( $title, $redirect, $skin ) {
		$link = $skin->makeLinkObj( $title );
		if( $redirect )
			$link = '<span class="watchlistredir">' . $link . '</span>';
		$tools[] = $skin->makeLinkObj( $title->getTalkPage(), wfMsgHtml( 'talkpagelinktext' ) );
		if( $title->exists() ) {
			$tools[] = $skin->makeKnownLinkObj( $title, wfMsgHtml( 'history_short' ), 'action=history' );
		}
		if( $title->getNamespace() == NS_USER && !$title->isSubpage() ) {
			$tools[] = $skin->makeKnownLinkObj( SpecialPage::getTitleFor( 'Contributions', $title->getText() ), wfMsgHtml( 'contributions' ) );
		}
		return '<li>'
			. Xml::check( 'titles[]', true, array( 'value' => $title->getPrefixedText() ) )
			. $link . ' (' . implode( ' | ', $tools ) . ')' . '</li>';
	}
}
