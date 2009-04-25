<?php

class SpecialAccountManager extends SpecialPage {
	function __construct() {
		parent::__construct( 'AccountManager', 'accountmanager', false );
		$this->error = false;
	}
	
	function processData( $action ) {
		global $wgRequest;
		$action = $wgRequest->getVal( 'action' );
		$username = $wgRequest->getVal( 'user' );		
		
		if ( !( $action == 'create' || $action == 'submit' ) )
			return;
		
		$user = new NssUser( $username );
		$user->load();
		
		if ( $action == 'submit' && !$user->exists )
			return;
		
		// Extract post data
		$post = $wgRequest->getValues();
		foreach( $post as $key => $value ) {
			if( substr( $key, 0, 3 ) != 'am-' )
				continue;
			$parts = explode( '-', $key, 2 );
			if( count( $parts ) != 2 )
				continue;

			$keyname = str_replace( '_', '-', strtolower( $parts[1] ) );
			$user->set( $keyname, $value );
		}
		
		if ( $action == 'submit' ) {
			$user->commit();
		} else {
			global $wgAuth, $wgPasswordSender;			
			
			$password = $wgAuth->createAccount( $username );
			$user->insert();
			
			$email = wfMsg( 'am-welcome-mail', $username, $password );
			$mailSubject = wfMsg( 'am-welcome-mail-subject' );
			$mailFrom = new MailAddress( $wgPasswordSender );
			$mailTo = new MailAddress( User::newFromName( $username ) );
		
			$mailResult = UserMailer::send( $mailTo, $mailFrom, $mailSubject, $email );
		
			if ( WikiError::isError( $mailResult ) ) { 
				$this->error = $mailResult->getMessage();
				return false;
			}
		}
		$wgAuth->getDB( DB_WRITE )->immediateCommit();
		return true;
		
	}
	
	function execute() {
		global $wgRequest, $wgUser, $wgOut;
		if( !$this->userCanExecute( $wgUser ) )
			return $this->displayRestrictionError();
		$this->setHeaders();
		
		$username = $wgRequest->getVal( 'user' );
		
		$result = $this->processData();
		if ( $result === true ) {
			$wgOut->addHTML( Xml::element('p', array(), wfMsg( 'am-updated' ) ) );
		} else if ( $result === false ) {
			$wgOut->addHTML( Xml::element( 'p', array( 'class' => 'error' ), 
					wfMsg( $this->error ) ) . "\n" );
		}
		
		$list = new AmUserListView();
		$list->execute();
		
		$userView = new AmUserView( $username );
		$userView->execute();
		
	}


}
