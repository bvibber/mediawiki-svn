<?php

/**
 * Special page to direct the user to a random page
 *
 * @ingroup SpecialPage
 * @author Rob Church <robchur@gmail.com>, Ilmari Karonen
 * @license GNU General Public Licence 2.0 or later
 * @modified by Tisane
 */
class RandomExcludeBlank extends SpecialPage {
	private $namespaces;  // namespaces to select pages from
	function __construct( $name = 'RandomExcludeBlank' ){
		global $wgContentNamespaces;

		$this->namespaces = $wgContentNamespaces;

		parent::__construct( $name );
		$this->setGroup('RandomExcludeBlank'
			,'redirects');
	}

	public function getNamespaces() {
		return $this->namespaces;
	}

	public function setNamespace ( $ns ) {
		if( !$ns || $ns < NS_MAIN ) $ns = NS_MAIN;
		$this->namespaces = array( $ns );
	}

	// select redirects instead of normal pages?
	// Overriden by SpecialRandomredirect
	public function isRedirect(){
		return false;
	}

	public function execute( $par ) {
		global $wgOut, $wgContLang;

		if ($par)
			$this->setNamespace( $wgContLang->getNsIndex( $par ) );

		$isBlank=true;
		$dbr = wfGetDB( DB_SLAVE );
		while ($isBlank==true){
			$title = $this->getRandomTitle();
			$result=$dbr->selectRow('blanked_page','blank_page_id'
				,array("blank_page_id" => $title->getArticleID()));
			if (!$result && !$title->isRedirect()){
				$isBlank=false;
			}
		}

		if( is_null( $title ) ) {
			$this->setHeaders();
			$wgOut->addWikiMsg( strtolower( $this->mName ) . '-nopages',  $wgContLang->getNsText( $this->namespace ) );
			return;
		}

		$query = $this->isRedirect() ? 'redirect=no' : '';
		$wgOut->redirect( $title->getFullUrl( $query ) );
	}


	/**
	 * Choose a random title.
	 * @return Title object (or null if nothing to choose from)
	 */
	public function getRandomTitle() {
		
		$randstr = wfRandom();
		$row = $this->selectRandomPageFromDB( $randstr );

		/* If we picked a value that was higher than any in
		 * the DB, wrap around and select the page with the
		 * lowest value instead!  One might think this would
		 * skew the distribution, but in fact it won't cause
		 * any more bias than what the page_random scheme
		 * causes anyway.  Trust me, I'm a mathematician. :)
		 */
		if( !$row )
			$row = $this->selectRandomPageFromDB( "0" );

		if( $row )
			return Title::makeTitleSafe( $row->page_namespace, $row->page_title );
		else
			return null;
	}

	private function selectRandomPageFromDB( $randstr ) {
		global $wgExtraRandompageSQL;
		$fname = 'RandomPage::selectRandomPageFromDB';

		$dbr = wfGetDB( DB_SLAVE );

		$use_index = $dbr->useIndexClause( 'page_random' );
		$page = $dbr->tableName( 'page' );

		$ns = implode( ",", $this->namespaces );
		$redirect = $this->isRedirect() ? 1 : 0;

		$extra = $wgExtraRandompageSQL ? "AND ($wgExtraRandompageSQL)" : "";
		$sql = "SELECT page_title, page_namespace
			FROM $page $use_index
			WHERE page_namespace IN ( $ns )
			AND page_is_redirect = $redirect
			AND page_random >= $randstr
			$extra
			ORDER BY page_random";

		$sql = $dbr->limitResult( $sql, 1, 0 );
		$res = $dbr->query( $sql, $fname );
		return $dbr->fetchObject( $res );
	}
}


/**
 * Implements Special:Allpages
 * @ingroup SpecialPage
 */
class AllPagesExcludeBlank extends IncludableSpecialPage {

	/**
	 * Maximum number of pages to show on single subpage.
	 */
	protected $maxPerPage = 345;

	/**
	 * Maximum number of pages to show on single index subpage.
	 */
	protected $maxLineCount = 200;

	/**
	 * Maximum number of chars to show for an entry.
	 */
	protected $maxPageLength = 70;

	/**
	 * Determines, which message describes the input field 'nsfrom'.
	 */
	protected $nsfromMsg = 'allpagesfrom';

	function __construct( $name = 'Allpages' ){
		parent::__construct( $name );
	}

	/**
	 * Entry point : initialise variables and call subfunctions.
	 * @param $par String: becomes "FOO" when called like Special:Allpages/FOO (default NULL)
	 * @param $specialPage See the SpecialPage object.
	 */
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgContLang;

		$this->setHeaders();
		$this->outputHeader();

		# GET values
		$from = $wgRequest->getVal( 'from', null );
		$to = $wgRequest->getVal( 'to', null );
		$namespace = $wgRequest->getInt( 'namespace' );

		$namespaces = $wgContLang->getNamespaces();

		$wgOut->setPagetitle( ( $namespace > 0 && in_array( $namespace, array_keys( $namespaces) ) )  ?
			wfMsg( 'allinnamespace', str_replace( '_', ' ', $namespaces[$namespace] ) ) :
			wfMsg( 'allarticles' )
		);

		if( isset($par) ) {
			$this->showChunk( $namespace, $par, $to );
		} elseif( isset($from) && !isset($to) ) {
			$this->showChunk( $namespace, $from, $to );
		} else {
			$this->showToplevel( $namespace, $from, $to );
		}
	}

	/**
	 * HTML for the top form
	 * @param integer $namespace A namespace constant (default NS_MAIN).
	 * @param string $from dbKey we are starting listing at.
	 * @param string $to dbKey we are ending listing at.
	 */
	function namespaceForm( $namespace = NS_MAIN, $from = '', $to = '' ) {
	    global $wgScript;
	    $t = $this->getTitle();

	    $out  = Xml::openElement( 'div', array( 'class' => 'namespaceoptions' ) );
	    $out .= Xml::openElement( 'form', array( 'method' => 'get', 'action' => $wgScript ) );
	    $out .= Xml::hidden( 'title', $t->getPrefixedText() );
	    $out .= Xml::openElement( 'fieldset' );
	    $out .= Xml::element( 'legend', null, wfMsg( 'allpages' ) );
	    $out .= Xml::openElement( 'table', array( 'id' => 'nsselect', 'class' => 'allpages' ) );
	    $out .= "<tr>
	            <td class='mw-label'>" .
	                Xml::label( wfMsg( 'allpagesfrom' ), 'nsfrom' ) .
	            "</td>
	            <td class='mw-input'>" .
	                Xml::input( 'from', 30, str_replace('_',' ',$from), array( 'id' => 'nsfrom' ) ) .
	            "</td>
	        </tr>
	        <tr>
	            <td class='mw-label'>" .
	                Xml::label( wfMsg( 'allpagesto' ), 'nsto' ) .
	            "</td>
	            <td class='mw-input'>" .
	                Xml::input( 'to', 30, str_replace('_',' ',$to), array( 'id' => 'nsto' ) ) .
	            "</td>
	        </tr>
	        <tr>
	            <td class='mw-label'>" .
	                Xml::label( wfMsg( 'namespace' ), 'namespace' ) .
	            "</td>
	            <td class='mw-input'>" .
	                Xml::namespaceSelector( $namespace, null ) . ' ' .
	                Xml::submitButton( wfMsg( 'allpagessubmit' ) ) .
	            "</td>
	            </tr>";
	    $out .= Xml::closeElement( 'table' );
	    $out .= Xml::closeElement( 'fieldset' );
	    $out .= Xml::closeElement( 'form' );
	    $out .= Xml::closeElement( 'div' );
	    return $out;
	}

	/**
	 * @param integer $namespace (default NS_MAIN)
	 */
	function showToplevel( $namespace = NS_MAIN, $from = '', $to = '' ) {
		global $wgOut, $wgContLang;
		$align = $wgContLang->isRtl() ? 'left' : 'right';

		# TODO: Either make this *much* faster or cache the title index points
		# in the querycache table.

		$dbr = wfGetDB( DB_SLAVE );
		$out = "";
		$where = array( 'page_namespace' => $namespace );

		$from = Title::makeTitleSafe( $namespace, $from );
		$to = Title::makeTitleSafe( $namespace, $to );
		$from = ( $from && $from->isLocal() ) ? $from->getDBKey() : null;
		$to = ( $to && $to->isLocal() ) ? $to->getDBKey() : null;

		if( isset($from) )
			$where[] = 'page_title >= '.$dbr->addQuotes( $from );
		if( isset($to) )
			$where[] = 'page_title <= '.$dbr->addQuotes( $to );

		global $wgMemc;
		$key = wfMemcKey( 'allpages', 'ns', $namespace, $from, $to );
		$lines = $wgMemc->get( $key );

		$count = $dbr->estimateRowCount( 'page', '*', $where, __METHOD__ );
		$maxPerSubpage = intval($count/$this->maxLineCount);
		$maxPerSubpage = max($maxPerSubpage,$this->maxPerPage);

		if( !is_array( $lines ) ) {
			$options = array( 'LIMIT' => 1 );
			$options['ORDER BY'] = 'page_title ASC';
			$firstTitle = $dbr->selectField( 'page', 'page_title', $where, __METHOD__, $options );
			$lastTitle = $firstTitle;
			# This array is going to hold the page_titles in order.
			$lines = array( $firstTitle );
			# If we are going to show n rows, we need n+1 queries to find the relevant titles.
			$done = false;
			while( !$done ) {
				// Fetch the last title of this chunk and the first of the next
				$chunk = ( $lastTitle === false )
					? array()
					: array( 'page_title >= ' . $dbr->addQuotes( $lastTitle ) );
				$res = $dbr->select( 'page', /* FROM */
					'page_title', /* WHAT */
					array_merge($where,$chunk),
					__METHOD__,
					array ('LIMIT' => 2, 'OFFSET' => $maxPerSubpage - 1, 'ORDER BY' => 'page_title ASC')
				);

				if( $s = $dbr->fetchObject( $res ) ) {
					array_push( $lines, $s->page_title );
				} else {
					// Final chunk, but ended prematurely. Go back and find the end.
					$endTitle = $dbr->selectField( 'page', 'MAX(page_title)',
						array_merge($where,$chunk),
						__METHOD__ );
					array_push( $lines, $endTitle );
					$done = true;
				}
				if( $s = $res->fetchObject() ) {
					array_push( $lines, $s->page_title );
					$lastTitle = $s->page_title;
				} else {
					// This was a final chunk and ended exactly at the limit.
					// Rare but convenient!
					$done = true;
				}
				$res->free();
			}
			$wgMemc->add( $key, $lines, 3600 );
		}

		// If there are only two or less sections, don't even display them.
		// Instead, display the first section directly.
		if( count( $lines ) <= 2 ) {
			if( !empty($lines) ) {
				$this->showChunk( $namespace, $lines[0], $lines[count($lines)-1] );
			} else {
				$wgOut->addHTML( $this->namespaceForm( $namespace, $from, $to ) );
			}
			return;
		}

		# At this point, $lines should contain an even number of elements.
		$out .= "<table class='allpageslist' style='background: inherit;'>";
		while( count ( $lines ) > 0 ) {
			$inpoint = array_shift( $lines );
			$outpoint = array_shift( $lines );
			$out .= $this->showline( $inpoint, $outpoint, $namespace );
		}
		$out .= '</table>';
		$nsForm = $this->namespaceForm( $namespace, $from, $to );

		# Is there more?
		if( $this->including() ) {
			$out2 = '';
		} else {
			if( isset($from) || isset($to) ) {
				global $wgUser;
				$out2 = '<table style="background: inherit;" width="100%" cellpadding="0" cellspacing="0" border="0">';
				$out2 .= '<tr valign="top"><td>' . $nsForm;
				$out2 .= '</td><td align="' . $align . '" style="font-size: smaller; margin-bottom: 1em;">' .
					$wgUser->getSkin()->makeKnownLinkObj( $this->getTitle(), wfMsgHtml ( 'allpages' ) );
				$out2 .= "</td></tr></table>";
			} else {
				$out2 = $nsForm;
			}
		}
		$wgOut->addHTML( $out2 . $out );
	}

	/**
	 * Show a line of "ABC to DEF" ranges of articles
	 * @param string $inpoint Lower limit of pagenames
	 * @param string $outpout Upper limit of pagenames
	 * @param integer $namespace (Default NS_MAIN)
	 */
	function showline( $inpoint, $outpoint, $namespace = NS_MAIN ) {
		global $wgContLang;
		$align = $wgContLang->isRtl() ? 'left' : 'right';
		$inpointf = htmlspecialchars( str_replace( '_', ' ', $inpoint ) );
		$outpointf = htmlspecialchars( str_replace( '_', ' ', $outpoint ) );
		// Don't let the length runaway
		$inpointf = $wgContLang->truncate( $inpointf, $this->maxPageLength );
		$outpointf = $wgContLang->truncate( $outpointf, $this->maxPageLength );

		$queryparams = $namespace ? "namespace=$namespace&" : '';
		$special = $this->getTitle();
		$link = $special->escapeLocalUrl( $queryparams . 'from=' . urlencode($inpoint) . '&to=' . urlencode($outpoint) );

		$out = wfMsgHtml( 'alphaindexline',
			"<a href=\"$link\">$inpointf</a></td><td>",
			"</td><td><a href=\"$link\">$outpointf</a>"
		);
		return '<tr><td align="' . $align . '">'.$out.'</td></tr>';
	}

	/**
	 * @param integer $namespace (Default NS_MAIN)
	 * @param string $from list all pages from this name (default FALSE)
	 * @param string $to list all pages to this name (default FALSE)
	 */
	function showChunk( $namespace = NS_MAIN, $from = false, $to = false ) {
		global $wgOut, $wgUser, $wgContLang, $wgLang;

		$sk = $wgUser->getSkin();

		$fromList = $this->getNamespaceKeyAndText($namespace, $from);
		$toList = $this->getNamespaceKeyAndText( $namespace, $to );
		$namespaces = $wgContLang->getNamespaces();
		$align = $wgContLang->isRtl() ? 'left' : 'right';

		$n = 0;

		if ( !$fromList || !$toList ) {
			$out = wfMsgWikiHtml( 'allpagesbadtitle' );
		} elseif ( !in_array( $namespace, array_keys( $namespaces ) ) ) {
			// Show errormessage and reset to NS_MAIN
			$out = wfMsgExt( 'allpages-bad-ns', array( 'parseinline' ), $namespace );
			$namespace = NS_MAIN;
		} else {
			list( $namespace, $fromKey, $from ) = $fromList;
			list( $namespace2, $toKey, $to ) = $toList;

			$dbr = wfGetDB( DB_SLAVE );
			$conds = array(
				'page_namespace' => $namespace,
				'page_title >= ' . $dbr->addQuotes( $fromKey )
			);
			if( $toKey !== "" ) {
				$conds[] = 'page_title <= ' . $dbr->addQuotes( $toKey );
			}

			$res = $dbr->select( 'page',
				array( 'page_namespace', 'page_title', 'page_is_redirect' ),
				$conds,
				__METHOD__,
				array(
					'ORDER BY'  => 'page_title',
					'LIMIT'     => $this->maxPerPage + 1,
					'USE INDEX' => 'name_title',
				)
			);

			if( $res->numRows() > 0 ) {
				$out = '<table style="background: inherit;" border="0" width="100%">';

				while( ( $n < $this->maxPerPage ) && ( $s = $res->fetchObject() ) ) {
					$t = Title::makeTitle( $s->page_namespace, $s->page_title );
					if( $t ) {
						$link = ( $s->page_is_redirect ? '<div class="allpagesredirect">' : '' ) .
							$sk->makeKnownLinkObj( $t, htmlspecialchars( $t->getText() ), false, false ) .
							($s->page_is_redirect ? '</div>' : '' );
					} else {
						$link = '[[' . htmlspecialchars( $s->page_title ) . ']]';
					}
					if( $n % 3 == 0 ) {
						$out .= '<tr>';
					}
					$out .= "<td width=\"33%\">$link</td>";
					$n++;
					if( $n % 3 == 0 ) {
						$out .= '</tr>';
					}
				}
				if( ($n % 3) != 0 ) {
					$out .= '</tr>';
				}
				$out .= '</table>';
			} else {
				$out = '';
			}
		}

		if ( $this->including() ) {
			$out2 = '';
		} else {
			if( $from == '' ) {
				// First chunk; no previous link.
				$prevTitle = null;
			} else {
				# Get the last title from previous chunk
				$dbr = wfGetDB( DB_SLAVE );
				$res_prev = $dbr->select(
					'page',
					'page_title',
					array( 'page_namespace' => $namespace, 'page_title < '.$dbr->addQuotes($from) ),
					__METHOD__,
					array( 'ORDER BY' => 'page_title DESC', 'LIMIT' => $this->maxPerPage, 'OFFSET' => ($this->maxPerPage - 1 ) )
				);

				# Get first title of previous complete chunk
				if( $dbr->numrows( $res_prev ) >= $this->maxPerPage ) {
					$pt = $dbr->fetchObject( $res_prev );
					$prevTitle = Title::makeTitle( $namespace, $pt->page_title );
				} else {
					# The previous chunk is not complete, need to link to the very first title
					# available in the database
					$options = array( 'LIMIT' => 1 );
					if ( ! $dbr->implicitOrderby() ) {
						$options['ORDER BY'] = 'page_title';
					}
					$reallyFirstPage_title = $dbr->selectField( 'page', 'page_title',
						array( 'page_namespace' => $namespace ), __METHOD__, $options );
					# Show the previous link if it s not the current requested chunk
					if( $from != $reallyFirstPage_title ) {
						$prevTitle =  Title::makeTitle( $namespace, $reallyFirstPage_title );
					} else {
						$prevTitle = null;
					}
				}
			}

			$self = $this->getTitle();

			$nsForm = $this->namespaceForm( $namespace, $from, $to );
			$out2 = '<table style="background: inherit;" width="100%" cellpadding="0" cellspacing="0" border="0">';
			$out2 .= '<tr valign="top"><td>' . $nsForm;
			$out2 .= '</td><td align="' . $align . '" style="font-size: smaller; margin-bottom: 1em;">' .
					$sk->makeKnownLinkObj( $self,
						wfMsgHtml ( 'allpages' ) );

			# Do we put a previous link ?
			if( isset( $prevTitle ) &&  $pt = $prevTitle->getText() ) {
				$q = 'from=' . $prevTitle->getPartialUrl()
					. ( $namespace ? '&namespace=' . $namespace : '' );
				$prevLink = $sk->makeKnownLinkObj( $self,
					wfMsgHTML( 'prevpage', htmlspecialchars( $pt ) ), $q );
				$out2 = $wgLang->pipeList( array( $out2, $prevLink ) );
			}

			if( $n == $this->maxPerPage && $s = $res->fetchObject() ) {
				# $s is the first link of the next chunk
				$t = Title::MakeTitle($namespace, $s->page_title);
				$q = 'from=' . $t->getPartialUrl()
					. ( $namespace ? '&namespace=' . $namespace : '' );
				$nextLink = $sk->makeKnownLinkObj( $self,
					wfMsgHtml( 'nextpage', htmlspecialchars( $t->getText() ) ), $q );
				$out2 = $wgLang->pipeList( array( $out2, $nextLink ) );
			}
			$out2 .= "</td></tr></table>";
		}

		$wgOut->addHTML( $out2 . $out );
		if( isset($prevLink) or isset($nextLink) ) {
			$wgOut->addHTML( '<hr /><p style="font-size: smaller; float: ' . $align . '">' );
			if( isset( $prevLink ) ) {
				$wgOut->addHTML( $prevLink );
			}
			if( isset( $prevLink ) && isset( $nextLink ) ) {
				$wgOut->addHTML( wfMsgExt( 'pipe-separator'
					, 'escapenoentities' ) );
			}
			if( isset( $nextLink ) ) {
				$wgOut->addHTML( $nextLink );
			}
			$wgOut->addHTML( '</p>' );

		}

	}

	/**
	 * @param int $ns the namespace of the article
	 * @param string $text the name of the article
	 * @return array( int namespace, string dbkey, string pagename ) or NULL on error
	 * @static (sort of)
	 * @access private
	 */
	function getNamespaceKeyAndText($ns, $text) {
		if ( $text == '' )
			return array( $ns, '', '' ); # shortcut for common case

		$t = Title::makeTitleSafe($ns, $text);
		if ( $t && $t->isLocal() ) {
			return array( $t->getNamespace(), $t->getDBkey(),
				$t->getText() );
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
}

class PopulateBlankedPagesTable extends SpecialPage {
	function __construct() {
		parent::__construct( 'PopulateBlankedPagesTable','purewikideletion' );
		wfLoadExtensionMessages( 'PureWikiDeletion' );
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut,$wgUser;
		if ( !$this->userCanExecute($wgUser) ) {
			$this->displayRestrictionError();
			return;
		}
		$dbr = wfGetDB( DB_SLAVE );
		$dbw = wfGetDB( DB_MASTER );
		$page_row=array(
			'page_id',
		        'page_namespace'
		);
		$result=$dbr->select( 'page', $page_row );
		while ($myRow=$dbr->fetchRow($result)){
			$myId=$myRow['page_id'];
			$myNamespace=$myRow['page_namespace'];
			if ($myNamespace!=NS_FILE){
				$myRevision=Revision::loadFromPageId($dbr,$myId);
				$myText=$myRevision->getRawText();
				if ($myText==""){
					while ($myPrevious=$myRevision->getPrevious()){
						if ($myPrevious->getRawText()==''){
							$myRevision=$myPrevious;
						}
						else {
							break;
						}
					}
					$blank_row=array(
						'blank_page_id'		=> $myId,
						'blank_user_id'
							=> $myRevision->getRawUser(),
						'blank_user_name'
							=> $myRevision->getRawUserText(),
						'blank_timestamp'
							=> $myRevision->getTimeStamp(),
						'blank_summary'
							=> $myRevision->getRawComment(),
						'blank_parent_id'
							=> $myRevision->getParentId(),
					);
					$checkPresence=$dbr->selectRow('blanked_page',
						'blank_page_id'
						,array("blank_page_id" => $myId));
					if (!$checkPresence){
						$dbw->insert('blanked_page',$blank_row);
					$mTitle=Title::newFromID($myId);
					Article::onArticleDelete( $mTitle );
					$mTitle->resetArticleID( 0 );
					}
				}
			}
			
		}
		$output = wfMsg( 'purewikideletion-population-done' );
		$wgOut->addWikiText($output);
	}
}