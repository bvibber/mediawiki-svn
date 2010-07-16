<?php

class CliInstaller extends Installer {

	/* The maintenance class in effect */
	private $maint;

	private $optionMap = array(
		'dbtype' => 'wgDBtype',
		'dbserver' => 'wgDBserver',
		'dbname' => 'wgDBname',
		'dbuser' => 'wgDBuser',
		'dbpass' => 'wgDBpassword',
		'dbprefix' => 'wgDBprefix',
		'dbtableoptions' => 'wgDBTableOptions',
		'dbmysql5' => 'wgDBmysql5',
		'dbserver' => 'wgDBserver',
		'dbport' => 'wgDBport',
		'dbname' => 'wgDBname',
		'dbuser' => 'wgDBuser',
		'dbpass' => 'wgDBpassword',
		'dbschema' => 'wgDBmwschema',
		'dbts2schema' => 'wgDBts2schema',
		'dbpath' => 'wgSQLiteDataDir',
	);


	/** Constructor */
	function __construct( $siteName, $admin = null, $option = array()) {
		parent::__construct();

		foreach ( $this->optionMap as $opt => $global ) {
			if ( isset( $option[$opt] ) ) {
				$GLOBALS[$global] = $option[$opt];
				$this->setVar( $global, $option[$opt] );
			}
		}

		if ( isset( $option['lang'] ) ) {
			global $wgLang, $wgContLang, $wgLanguageCode;
			$this->setVar( '_UserLang', $option['lang'] );
			$wgContLang = Language::factory( $option['lang'] );
			$wgLang = Language::factory( $option['lang'] );
			$wgLanguageCode = $option['lang'];
		}

		$this->setVar( 'wgSitename', $siteName );
		if ( $admin ) {
			$this->setVar( '_AdminName', $admin );
		} else {
			$this->setVar( '_AdminName',
				wfMsgForContent( 'config-admin-default-username' ) );
		}

		if ( !isset( $option['installdbuser'] ) ) {
			$this->setVar( '_InstallUser',
				$this->getVar( 'wgDBuser' ) );
			$this->setVar( '_InstallPassword',
				$this->getVar( 'wgDBpassword' ) );
		}

		if ( isset( $option['pass'] ) ) {
			$this->setVar( '_AdminPassword', $option['pass'] );
		}
	}

	/**
	 * Main entry point.
	 */
	function execute( ) {
		foreach( $this->getInstallSteps() as $stepObj ) {
			$step = is_array( $stepObj ) ? $stepObj['name'] : $stepObj;
			$this->showMessage( wfMsg( "config-install-$step") .
					wfMsg( 'ellipsis' ) . wfMsg( 'word-separator' ) );
			$func = 'install' . ucfirst( $step );
			$status = $this->{$func}();
			$warnings = $status->getWarningsArray();
			if ( !$status->isOk() ) {
				$this->showStatusMessage( $status );
				echo "\n";
				exit;
			} elseif ( count($warnings) !== 0 ) {
				foreach ( $status->getWikiTextArray( $warnings ) as $w ) {
					$this->showMessage( $w . wfMsg( 'ellipsis') .
						wfMsg( 'word-separator' ) );
				}
			}
			$this->showMessage( wfMsg( 'config-install-step-done' ) ."\n");
		}
	}

	function showMessage( $msg /*, ... */ ) {
		echo html_entity_decode( strip_tags( $msg ), ENT_QUOTES );
		flush();
	}

	function showStatusMessage( $status ) {
		$this->showMessage( $status->getWikiText() );
	}

}