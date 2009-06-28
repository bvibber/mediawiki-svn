<?php
/**
 * @file
 * @ingroup Maintenance
 */

require_once( "Maintenance.php" );

class EditCLI extends Maintenance {
	public function __construct() {
		parent::__construct();
		$this->mDescription = "Edit an article from the command line, text is from stdin";
		$this->addOption( 'u', 'Username', false, true );
		$this->addOption( 's', 'Edit summary', false, true );
		$this->addOption( 'm', 'Minor edit' );
		$this->addOption( 'b', 'Bot edit' );
		$this->addOption( 'a', 'Enable autosummary' );
		$this->addOption( 'no-rc', 'Do not show the change in recent changes' );
		$this->addArgs( array( 'title' ) );
	}

	public function execute() {
		global $wgUser, $wgTitle, $wgArticle;

		$userName = $this->getOption( 'u', 'Maintenance script' );
		$summary = $this->getOption( 's', '' );
		$minor = $this->hasOption( 'm' );
		$bot = $this->hasOption( 'b' );
		$autoSummary = $this->hasOption( 'a' );
		$noRC = $this->hasOption( 'no-rc' );
		
		$wgUser = User::newFromName( $userName );
		if ( !$wgUser ) {
			$this->error( "Invalid username\n", true );
		}
		if ( $wgUser->isAnon() ) {
			$wgUser->addToDatabase();
		}
	
		$wgTitle = Title::newFromText( $this->getArg() );
		if ( !$wgTitle ) {
			$this->error( "Invalid title\n", true );
		}
	
		$wgArticle = new Article( $wgTitle );
	
		# Read the text
		$text = $this->getStdin();
		
		# Do the edit
		$this->output( "Saving... " );
		$status = $wgArticle->doEdit( $text, $summary, 
			( $minor ? EDIT_MINOR : 0 ) |
			( $bot ? EDIT_FORCE_BOT : 0 ) | 
			( $autoSummary ? EDIT_AUTOSUMMARY : 0 ) |
			( $noRC ? EDIT_SUPPRESS_RC : 0 ) );
		if ( $status->isOK() ) {
			$this->output( "done\n" );
			$exit = 0;
		} else {
			$this->output( "failed\n" );
			$exit = 1;
		}
		if ( !$status->isGood() ) {
			$this->output( $status->getWikiText() . "\n" );
		}
		exit( $exit );
	}
}

$maintClass = "EditCLI";
require_once( DO_MAINTENANCE );

