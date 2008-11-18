<?php

class MysqlInstaller extends InstallerDBType {
	var $globalNames = array(
		'wgDBserver',
		'wgDBname',
		'wgDBuser',
		'wgDBpassword',
		'wgDBprefix',
		'wgDBTableOptions',
		'wgDBmysql5',
	);

	var $internalDefaults = array(
		'_MysqlInstallUser' => 'root',
		'_MysqlInstallPassword' => '',
		'_MysqlSameAccount' => true,
	);

	var $minimumVersion = '4.0.14';

	var $conn;

	function getName() {
		return 'mysql';
	}
	
	function isCompiled() {
		return $this->checkExtension( 'mysql' );
	}

	function getGlobalNames() {
		return $this->globalNames;
	}

	function getGlobalDefaults() {
		return array();
	}

	function getInternalDefaults() {
		return $this->internalDefaults;
	}

	function getConnectForm() {
		return
			$this->getLabelledTextBox( 'wgDBserver', 'config-db-host' ) .
			$this->parent->getHelpBox( 'config-db-host-help' ) . 
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', array(), wfMsg( 'config-db-wiki-settings' ) ) .
			$this->getLabelledTextBox( 'wgDBname', 'config-db-name' ) .
			$this->parent->getHelpBox( 'config-db-name-help' ) .
			$this->getLabelledTextBox( 'wgDBprefix', 'config-db-prefix' ) .
			$this->parent->getHelpBox( 'config-db-prefix-help' ) .
			Xml::closeElement( 'fieldset' ) .
			Xml::openElement( 'fieldset' ) . 
			Xml::element( 'legend', array(), wfMsg( 'config-db-install-account' ) ) .
			$this->getLabelledTextBox( '_MysqlInstallUser', 'config-db-username' ) .
			$this->getLabelledPasswordBox( '_MysqlInstallPassword', 'config-db-password' ) .
			$this->parent->getHelpBox( 'config-db-install-help' ) .
			Xml::closeElement( 'fieldset' );
	}

	function submitConnectForm() {
		// Get variables from the request
		$newValues = $this->setVarsFromRequest();

		// Validate them
		$status = Status::newGood();
		if ( !strlen( $newValues['wgDBname'] ) ) {
			$status->fatal( 'config-missing-db-name' );
		} elseif ( !preg_match( '/^[a-zA-Z0-9_]+$/', $newValues['wgDBname'] ) ) {
			$status->fatal( 'config-invalid-db-name', $newValues['wgDBname'] );
		}
		if ( !preg_match( '/^[a-zA-Z0-9_]*$/', $newValues['wgDBprefix'] ) ) {
			$status->fatal( 'config-invalid-db-prefix', $newValues['wgDBprefix'] );
		}
		if ( !$status->isOK() ) {
			return $status;
		}

		// Try to connect
		$status = $this->attemptConnection();
		if ( !$status->isOK() ) {
			return $status;
		}

		// Check version
		$version = $this->conn->getServerVersion();
		if ( version_compare( $version, $this->minimumVersion ) < 0 ) {
			return Status::newFatal( 'config-mysql-old', $this->minimumVersion, $version );
		}

		return $status;
	}

	function attemptConnection() {
		$status = Status::newGood();
		try {
			$this->conn = new Database( 
				$this->getVar( 'wgDBserver' ),
				$this->getVar( '_MysqlInstallUser' ),
				$this->getVar( '_MysqlInstallPassword' ),
				false,
				false,
				0, 
				$this->getVar( 'wgDBprefix' )
			);
		} catch ( DBConnectionError $e ) {
			$status->fatal( 'config-connection-error', $e->getMessage() );
		}
		return $status;
	}

	function getConnection() {
		$status = $this->attemptConnection();
		if ( $status->isOK() ) {
			return $this->conn;
		} else {
			return $status;
		}
	}

	function doUpgrade() {
		$conn = $this->getConnection();
		if ( $conn instanceof Status ) {
			$this->parent->showStatusError( $conn );
			return;
		}

		# Determine existing default character set
		if ( $conn->tableExists( "revision" ) ) {
			$revision = $conn->escapeLike( $this->getVar( 'wgDBprefix' ) . 'revision' );
			$res = $conn->query( "SHOW TABLE STATUS LIKE '$revision'" );
			$row = $conn->fetchObject( $res );
			if ( !$row ) {
				$this->parent->showMessage( 'config-show-table-status' );
				$existingSchema = false;
				$existingEngine = false;
			} else {
				if ( preg_match( '/^latin1/', $row->Collation ) ) {
					$existingSchema = 'mysql4';
				} elseif ( preg_match( '/^utf8/', $row->Collation ) ) {
					$existingSchema = 'mysql5';
				} elseif ( preg_match( '/^binary/', $row->Collation ) ) {
					$existingSchema = 'mysql5-binary';
				} else {
					$existingSchema = false;
					$this->parent->showMessage( 'config-unknown-collation' );
				}
				if ( isset( $row->Engine ) ) {
					$existingEngine = $row->Engine;
				} else {
					$existingEngine = $row->Type;
				}
			}
		}
			
		// TODO...
		return;

		# Create user if required
		if ( $conf->Root ) {
			$conn = $dbc->newFromParams( $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname, 1 );
			if ( $conn->isOpen() ) {
				print "<li>DB user account ok</li>\n";
				$conn->close();
			} else {
				print "<li>Granting user permissions...";
				if( $mysqlOldClient && $mysqlNewAuth ) {
					print " <b class='error'>If the next step fails, see <a href='http://dev.mysql.com/doc/mysql/en/old-client.html'>http://dev.mysql.com/doc/mysql/en/old-client.html</a> for help.</b>";
				}
				print "</li>\n";
				dbsource( "../maintenance/users.sql", $conn );
			}
		}
	}

	function getSettingsForm() {
		$installUser = $this->getVar( '_MysqlInstallUser' );
		$installPass = $this->parent->getFakePassword( $this->getVar( '_MysqlInstallPassword' ) );
		$js = 'disableControlArray( "mysql__MysqlSameAccount", ' .
			'["mysql_wgDBuser", "mysql_wgDBpassword"] )';

		return
			Xml::openElement( 'fieldset' ) . 
			Xml::element( 'legend', array(), wfMsg( 'config-db-web-account' ) ) .
			$this->getLabelledCheckBox( 
				'_MysqlSameAccount', 'config-db-web-account-same', array( 'onclick' => $js )
			) .
			"<br/>\n" .
			$this->getLabelledTextBox( 'wgDBuser', 'config-db-username' ) .
			$this->getLabelledPasswordBox( 'wgDBpassword', 'config-db-password' ) .
			$this->parent->getHelpBox( 'config-db-web-help' ) .
			Xml::closeElement( 'fieldset' ) .
			"<script type=\"text/javascript\">$js</script>";
	}
}
