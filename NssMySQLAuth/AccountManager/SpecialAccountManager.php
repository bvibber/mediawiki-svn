<?php

class SpecialAccountManager extends SpecialPage {
	function __construct() {
		wfLoadExtensionMessages( 'accountmanager' );		
		
		parent::__construct( 'AccountManager', 'accountmanager', false );
		$this->error = false;
	}
	
	function processData() {
		global $wgRequest, $wgAuth;
		$action = $wgRequest->getVal( 'action' );
		$username = $wgRequest->getVal( 'user' );
		if ( !$username )		
			$username = $wgRequest->getVal( 'am-username' );
			
		if ( !( $action == 'create' || $action == 'submit' ) || !$wgRequest->wasPosted()  )
			return;
		
		$user = new NssUser( $username );
		$user->load();
		
		if ( $action == 'submit' && !$user->exists )
			return;
		
		// Extract post data
		$post = $wgRequest->getValues();
		$options = array();
		foreach( $post as $key => $value ) {
			// Only am-* data is proper data
			if( substr( $key, 0, 3 ) != 'am-' || strlen( $key ) <= 3 )
				continue;
			// Split off the am- prefix
			$keyname = str_replace( '-', ' ', strtolower( substr( $key, 3 ) ) );
			$user->set( $keyname, $value );
			$options[$keyname] = $value;
		}
		
		if ( $action == 'submit' ) {
			$user->commit();
		} else {
			global $wgPasswordSender;			
			
			$password = $wgAuth->createAccount( $username, $options );
			$user->commit();
			
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
		
		$export = $wgRequest->getVal( 'export' );
		if ( $export ) {
			$exporter = new AmExport();
			if ( $exporter->execute( $export ) === true )
				return;
		}
		
		AmExport::setSubtitle();
		
		$username = $wgRequest->getVal( 'user' );
		
		$result = $this->processData();
		if ( $result === true ) {
			$wgOut->addHTML( Xml::element('p', array(), wfMsg( 'am-updated' ) ) );
		} else if ( $result === false ) {
			$wgOut->addHTML( Xml::element( 'p', array( 'class' => 'error' ), 
					wfMsg( $this->error ) ) . "\n" );
		}
		
		$userView = new AmUserView( $username );
		$userView->execute();

		$list = new AmUserListView();
		$list->execute();
	}


}
