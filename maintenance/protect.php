<?php
/**
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class Protect extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Protect or unprotect an article from the command line.";
		$this->addOption( 'unprotect', 'Removes protection' );
		$this->addOption( 'semiprotect', 'Adds semi-protection' );
		$this->addOption( 'u', 'Username to protect with', false, true );
		$this->addOption( 'r', 'Reason for un/protection', false, true );
	}

	public function execute() {
		global $wgUser, $wgTitle, $wgArticle;

		$userName = $this->getOption( 'u', 'Maintenance script' );
		$reason = $this->getOption( 'r', '' );

		$protection = "sysop";
		if ( $this->hasOption('semiprotect') ) {
			$protection = "autoconfirmed";
		} elseif ( $this->hasOption('unprotect') ) {
			$protection = "";
		}

		$wgUser = User::newFromName( $userName );
		$restrictions = array( 'edit' => $protection, 'move' => $protection );

		$wgTitle = Title::newFromText( $args[0] );
		if ( !$wgTitle ) {
			$this->error( "Invalid title\n", true );
		}

		$wgArticle = new Article( $wgTitle );

		# un/protect the article
		$this->output( "Updating protection status... " );
		$success = $wgArticle->updateRestrictions($restrictions, $reason);
		if ( $success ) {
			$this->output( "done\n" );
		} else {
			$this->output( "failed\n" );
		}
	}
}

$maintClass = "Protect";
require_once( DO_MAINTENANCE );
