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

		$text = "<fieldset>\n" . 
				"<legend>" . wfMsg('interwikilist-filter') . "</legend>\n" .
				"<form action=\"". $wgScript . "\" method=\"get\" id=\"interwikilist-search\">\n" . 
				"<input type=\"hidden\" name=\"title\" value=\"" . $this->mTitle->getPrefixedText() . "\">\n" .
				wfMsg('interwikilist-prefix') . " <input type=\"text\" name=\"iwsearch\" id=\"interwikilist-prefix\" value=\"" . 
				htmlspecialchars( $prefix ) . "\"><br />\n" .
				"<input type=\"submit\" value=\"" . wfMsg('search') . "\">\n" .
				"</form>\n</fieldset>";

		$text .= "<table id=\"sv-software\"<tr>
							<th>" . wfMsg( 'interwikilist-linkname' ) . "</th>
							<th>" . wfMsg( 'interwikilist-target' ) . "</th>
						</tr>\n";
		
		while ( $row = $dbr->fetchObject( $results ) ) {                      
				$text .= "						<tr>
							<td>" . htmlspecialchars( $row->iw_prefix ) . "</td>
							<td>" . htmlspecialchars( $row->iw_url ) . "</td>
						</tr>\n";
		}
		$text .= "</table>\n";
		$dbr->freeResult( $results );

		return $text;
	}
}
