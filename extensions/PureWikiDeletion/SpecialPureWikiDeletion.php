<?php

/**
 * Special page to direct the user to a random page that is not in the blank_page
 * table
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
* Fill the blank_page table with title of all the currently blank pages; ignore
* the File namespace. Purge caches as needed
*/ 
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