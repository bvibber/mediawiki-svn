<?php

/**
 * Maintenance script to create an account and grant it administrator rights
 *
 * @file
 * @ingroup Maintenance
 * @author Rob Church <robchur@gmail.com>
 */

require_once( "Maintenance.php" );

class CreateAndPromote extends Maintenance {

	public function __construct() {
		parent::__construct();
		$this->mDescription = "Create a new user account with administrator rights";
		$this->addParam( "bureaucrat", "Grant the account bureaucrat rights" );
		$this->addArgs( array( "username", "password" ) );
	}

	public function execute() {
		if( count( $this->mArgs ) < 2 ) {
			$this->error( "Please provide a username and password for the new account.\n", true );
		}
		$username = $this->mArgs[0];
		$password = $this->mArgs[1];
		
		$this->output( wfWikiID() . ": Creating and promoting User:{$username}..." );
		
		$user = User::newFromName( $username );
		if( !is_object( $user ) ) {
			$this->error( "invalid username.\n", true );
		} elseif( 0 != $user->idForName() ) {
			$this->error( "account exists.\n", true );
		}
	
		# Insert the account into the database
		$user->addToDatabase();
		$user->setPassword( $password );
		$user->saveSettings();
	
		# Promote user
		$user->addGroup( 'sysop' );
		if( isset( $this->mOptions['bureaucrat'] ) )
			$user->addGroup( 'bureaucrat' );
	
		# Increment site_stats.ss_users
		$ssu = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
		$ssu->doUpdate();
	
		$this->output( "done.\n" );
	}
}

$maintClass = "CreateAndPromote";
require_once( "doMaintenance.php" );
