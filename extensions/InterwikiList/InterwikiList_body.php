<?php

// Class definition for Extension:InterwikiList

class InterwikiList extends SpecialPage {
	
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
		global $wgOut;
		$wgOut->setPagetitle( wfMsg( 'interwikilist' ) );
		$selfTitle = Title::makeTitle( NS_SPECIAL, 'InterwikiList' );
		$wgOut->addHTML( $this->getInterwikis() );
	}
	
	/** 
	* Get all Interwiki Links - the heart of the function
	*/
	private function getInterwikis() {
		$dbr = wfGetDB( DB_SLAVE );
		
		$results = $dbr->select( 'interwiki', array( 'iw_prefix', 'iw_url' ) );
		
		$text = Xml::openElement( 'table', array( 'id' => 'sv-software' ) ) . "<tr>
							<th>" . wfMsg( 'interwikilist-linkname' ) . "</th>
							<th>" . wfMsg( 'interwikilist-target' ) . "</th>
						</tr>\n";
		
		while ( $row = $dbr->fetchObject( $results ) ) {                      
				$text .= "						<tr>
							<td>" . $row->iw_prefix . "</td>
							<td>" . $row->iw_url . "</td>
						</tr>\n";
		}
		$text .= Xml::closeElement( 'table' );
		$dbr->freeResult ( $results );
		
		return $text;
	}
}