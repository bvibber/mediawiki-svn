<?php

class AmUserView {
	function __construct( $username ) {
		$this->username = $username;
		$this->title = SpecialPage::getTitleFor( 'AccountManager' );
	}
	function execute() {
		global $wgOut; 
		
		// Populate the user object
		$this->fetchUserData();
		
		// Form and table header
		$this->createHeader();
		
		// Table rows
		$props = NssProperties::getAll();
		foreach ( NssProperties::getAll() as $prop ) {
			$wgOut->addHtml( $this->makeRow( $prop ) );
		}
		
		// Submit buttons and footer
		$this->createFooter();
	}
	
	function fetchUserData() {
		$this->user = new NssUser( $this->username );
		$this->user->load();
		$this->action = $this->user->exists ? 'submit' : 'create';
	}
	
	function createHeader() {
		global $wgOut;
		
		$wgOut->addHtml( Xml::element( 'form', array(
			'action' => $this->title->getLocalUrl( array( 
				'action' => $this->action,
				'user' => $this->username,
			) ),
			'method' => 'post',
		) ) . "\n" );
		$wgOut->addHtml( Xml::element( 'table', array( 
			'class' => "am-{$this->action}"
		) ) . "\n" );
	}
	
	function makeRow( $prop ) {
		return ( "\t<tr><td>" . 
			Xml::label( wfMsg( "am-$prop" ), "am-$prop" ) .
			"</td><td>" .
			Xml::input( /* $name */ "am-$prop", /* $size */ false, 
				/* $value */ $this->get( $prop ),
				array( 'id' => "am-$prop" ) ) .
			"</td></tr>\n"
		);
	}
	
	function createFooter() {
		global $wgOut;
		$wgOut->addHtml( "</table>\n" . 
			Xml::element( 'p', array( 
				'id' => ''
			) ) );
		$wgOut->addHtml( Xml::submitButton( wfMsg( 'am-save-changes' ) ) );
		$wgOut->addHtml( "</p>\n</form>\n" );
	}
	
}

