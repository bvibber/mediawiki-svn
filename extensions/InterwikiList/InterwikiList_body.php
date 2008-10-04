<?php

// Class definition for Extension:InterwikiList

// Suppress fatal error about SpecialPage class not found if called as entry point
if ( !defined('MEDIAWIKI') ) {
        die( '' );
}
 
class InterwikiList extends SpecialPage {
	
	// Privates
	private $mTitle; // The title for this specialpage

	/**
	* Constructor
	*/
	public function InterwikiList() {
		SpecialPage::SpecialPage("InterwikiList");
		wfLoadExtensionMessages('InterwikiList');
	}
	
	/**
	 * Execute
	 */
	public function execute( $par ) {
		global $wgOut, $wgRequest;
		$wgOut->setPagetitle( wfMsg( 'interwikilist' ) );
		$this->mTitle = SpecialPage::getTitleFor( 'InterwikiList' );
		$prefix = $wgRequest->getText( 'iwsearch', $par );
		$wgOut->addHTML( $this->getInterwikis( $prefix ) );
	}
	
	/** 
	* Get all Interwiki Links - the heart of the function
	* @param $prefix string Prefix to search for in list
	* @return string HTML
	*/
	private function getInterwikis( $prefix = null ) {
		global $wgScript;
		$dbr = wfGetDB( DB_SLAVE );

		$conds = array();
		if ( !is_null( $prefix ) ) {
			$conds[] = "iw_prefix LIKE " . $dbr->addQuotes( $dbr->escapeLike( $prefix ) . "%" );
		}

		$results = $dbr->select( 'interwiki', array( 'iw_prefix', 'iw_url' ), $conds );

		$form = Xml::openElement( 'form', array( 'action' => $wgScript, 'method' => 'get', 'id' => 'interwikilist-search' ) ) .
				Xml::hidden( 'title', $this->mTitle->getPrefixedText() ) .
				Xml::inputLabel( wfMsg('interwikilist-prefix'), 'iwsearch', 'interwikilist-prefix', false, $prefix  ) .
				Xml::submitButton( wfMsg('search') ) .
				Xml::closeElement( 'form' );
		$text = Xml::fieldSet( wfMsg('interwikilist-filter'), $form );

		$text .= Xml::openElement( 'table', array( 'id' => 'sv-software' ) ) . 
					Xml::openElement( 'tr' ) .
						Xml::element( 'th', null, wfMsg( 'interwikilist-linkname' ) ) .
						Xml::element( 'th', null, wfMsg( 'interwikilist-target' ) ) .
					Xml::closeElement( 'tr' );;

		while ( $row = $dbr->fetchObject( $results ) ) {                      
			$text .=	Xml::openElement( 'tr' ) .
							Xml::element( 'td', null, $row->iw_prefix ) .
							Xml::element( 'td', null, $row->iw_url ) .
						Xml::closeElement( 'tr' );
		}
		$text .= Xml::closeElement( 'table' );
		$dbr->freeResult( $results );

		return $text;
	}
}
