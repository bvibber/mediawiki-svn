<?php

class SpecialCommunityApplications extends SpecialPage {
	function __construct() {
		parent::__construct( 'CommunityApplications', 'view-community-applications' );
		wfLoadExtensionMessages( 'CommunityApplications' );
	}
	
	function execute($par) {
		global $wgUser, $wgOut;
		
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}
		
		$wgOut->setPageTitle( wfMsg( 'community-applications-title' ) );
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$res = $dbr->select( 'community_hiring_application', '*', 1, __METHOD__ );
		
		foreach( $res as $row ) {
			$this->showEntry( $row );
		}
	}
	
	function showEntry( $row ) {
		global $wgOut;
		$wgOut->addHTML( "<hr/>" );
		$wgOut->addHTML( "<table><tbody>" );
		
		$data = FormatJson::decode( $row->ch_data, true );
		
		$header = wfMsg( 'community-applications-application-title', $row->ch_id,
				$data['family-name'], $data['given-name'] );
		$wgOut->addHTML( Xml::element( 'h2', null, $header ) );
		foreach( $data as $key => $value ) {
			$html = Xml::element( 'td', null, $key );
			$html .= Xml::element( 'td', null, $value );
			$html = Xml::tags( 'tr', null, $html ) . "\n";
			$wgOut->addHTML( $html );
		}
		
		$wgOut->addHTML( "</tbody></table>" );
	}
}
