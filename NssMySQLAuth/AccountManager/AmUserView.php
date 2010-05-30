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
		
		if ( $this->user->exists ) {
			$wgOut->addHtml( '<h2>' . wfMsgExt( 'am-edit-user', 'parseinline', 
				$this->user->name ) . '</h2>' );
		} else {
			$wgOut->addHtml( '<h2>' . wfMsgExt( 'am-create-user', 'parseinline' ) . '</h2>' );
		}
		$wgOut->addHtml( Xml::openElement( 'form', array(
			'action' => $this->title->getLocalUrl( array( 
				'action' => $this->action,
				'user' => $this->username,
			) ),
			'method' => 'post',
		) ) . "\n" );
		$wgOut->addHtml( Xml::openElement( 'table', array( 
			'class' => "am-{$this->action}"
		) ) . "\n" );
	}
	
	function makeRow( $prop ) {
		$amName = 'am-'.str_replace( ' ', '-', $prop );
		$label = wfMsg( $amName );
		if ( wfEmptyMsg( $amName, $label ) )
			$label = $prop;
			
		if ( $prop == 'active' ) {
			global $wgUserActivityLevels;
			$select = new XmlSelect( $amName, false, 'active' );
			foreach ( $wgUserActivityLevels as $level )
				$select->addOption( $level );

			$input = $select->getHTML();
		} else {
			$input = Xml::input( /* $name */ $amName, /* $size */ 40, 
				/* $value */ $this->user->get( $prop ),
				array( 'id' => $amName ) );
		}
		
		return ( "\t<tr><td>" . 
			Xml::label( $label, $amName ) .
			"</td><td>$input</td></tr>\n"
		);
	}
	
	function createFooter() {
		global $wgOut;
		$wgOut->addHtml( "</table>\n" . 
			Xml::openElement( 'p', array( 
				'id' => ''
			) ) );
		$wgOut->addHtml( Xml::submitButton( wfMsg( 'am-save-changes' ) ) );
		$wgOut->addHtml( "</p>\n</form>\n" );
	}
	
}

