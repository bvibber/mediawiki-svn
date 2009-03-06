<?php

/**
 * Switch a master in an LBFactory_Multi configuration.
 *
 * Note that you can only do this once during execution of a script, because 
 * after that, $wgLBFactoryConf will be wrong. You have to restart it to have 
 * it re-read the MW configuration.
 */
class MasterSwitcher {
	var $lbfConf;
	var $replUser = 'repl';
	var $rootPass, $replPass, $confLocation;
	var $conns = array();
	var $confOps = array();

	function __construct( $options ) {
		global $wgLBFactoryConf;
		$this->lbfConf = $wgLBFactoryConf;
		$this->rootPass = $options['rootPass'];
		$this->replPass = $options['replPass'];
		$this->confLocation = $options['conf'];
	}

	function switchMaster( $oldMaster, $newMaster, $options = array() ) {
		if ( !$this->sanityCheck( $oldMaster, $newMaster ) ) {
			// Already logged
			return false;
		}

		$sections = $this->getSections( $oldMaster );

		// Fetch the new load configuration
		$newLoads = array();
		$masterLoads = array();
		foreach ( $sections as $section ) {
			if ( isset( $options['newLoad'][$section] ) ) {
				$newLoads[$section] = $options['newLoad'][$section];
			} else {
				$newLoads[$section] = 100;
			}
			if ( isset( $options['masterLoad'][$section] ) ) {
				$masterLoads[$section] = $options['masterLoad'][$section];
			} else {
				$masterLoads[$section] = 100;
			}
		}

		// Set read-only mode on the wiki
		$this->log( 'Setting read-only mode' );
		foreach ( $sections as $section ) {
			$this->setConf( "readOnlyBySection/$section", 
				'Switching master to ' . $newMaster . ', should be back in a few minutes' );
		}
		$this->commitConf();

		// Try to connect to the old master
		$this->log( 'Attempting to connect to the old master' );
		$oldMasterDB = $this->getConnection( $oldMaster );
		if ( $oldMasterDB ) {
			$this->prepareOldMaster( $oldMaster, $oldMasterDB );
		} else {
			$this->log( "Old master is down, continuing anyway." );
		}
		
		// Stop slave on the new master and reset it so it can't start again
		$this->log( 'Configuring the new master' );
		$newMasterDB->query( 'STOP SLAVE' );
		$newMasterDB->query( 'CHANGE MASTER TO master_host=\'\'' );
		$newMasterDB->query( 'RESET SLAVE'  );

		// Reset binlogs on the new master so that we start from binlog 1
		$newMasterDB->query( 'RESET MASTER' );
		$newMasterPos = $newMasterDB->getMasterPos();
		if ( !preg_match( '/^\d+$/', $newMasterPos->pos ) ) {
			$this->log( 'Invalid master position returned by new master, aborting.' );
			return false;
		} else {
			$validatedPos = $newMasterPos->pos;
		}
		$file = $newMasterPos->file;

		// Change master on the new slaves
		$this->log( 'Configuring the slaves' );
		$slaves = $this->getSlaves( $oldMaster );
		$changeMasterSql = 
			'CHANGE MASTER TO' .
			' master_host=' . $cc->addQuotes( $newMaster ) .
			' master_user=' . $cc->addQuotes( $this->replUser ) .
			' master_password=' . $cc->addQuotes( $this->replPassword ) .
			' master_log_file=' . $cc->addQuotes( $newMasterPos->file ) .
			' master_log_pos=' . $validatedPos;

		if ( $oldMasterDB ) {
			if ( $oldMasterDB->getSlavePos() ) {
				$this->doQueryLogErrors( $oldMasterDB, $oldMaster, 'SLAVE STOP' );
			}
			$this->doQueryLogErrors( $oldMasterDB, $oldMaster, $changeMasterSql );
			$this->doQueryLogErrors( $oldMasterDB, $oldMaster, 'SLAVE START' );
		}

		foreach ( $slaves as $slave ) {
			$conn = $this->getConnection( $slave );
			if ( !$conn ) {
				$this->log( "Cannot change master on $slave: connection error" );
				continue;
			}
			$this->doQueryLogErrors( $conn, $slave, 'SLAVE STOP' );
			$this->doQueryLogErrors( $conn, $slave, $changeMasterSql );
			$this->doQueryLogErrors( $conn, $slave, 'SLAVE START' );
		}

		// Update configuration
		$this->log( 'Doing final configuration update' );
		foreach ( $sections as $section ) {
			// Promote the new master
			$this->deleteConf( "sectionLoads/$section/$newMaster" );
			$this->insertConf( "sectionLoads/$section", $newMaster, $masterLoads[$section] );
			// Set the new load for the old master
			$this->setConf( "sectionLoads/$section/$oldMaster", $newLoads[$section] );
			// Unset read-only mode
			$this->deleteConf( "readOnlyBySection/$section" );
		}

		$this->commitConf();
		$this->log( 'All done.' );
	}

	function getConnection( $host ) {
		if ( !isset( $this->conns[$host] ) ) {
			$this->conns[$host] = new Database( $host, 'root', $this->rootPass );
		}
		return $this->conns[$host];
	}

	function setConf( $path, $value ) {
		$this->confOps[] = array(
			'type' => 'set',
			'path' => "\$wgLBFactoryConf/$path",
			'value' => var_export( $value, true )
		);
	}

	function deleteConf( $path ) {
		$this->confOps[] = array(
			'type' => 'delete',
			'path' => "\$wgLBFactoryConf/$path",
		);
	}

	function insertConf( $path, $name, $value ) {
		$this->confOps[] = array(
			'type' => 'insert',
			'path' => "\$wgLBFactoryConf/$path",
			'key' => var_export( $name, true ),
			'value' => var_export( $value, true ),
		);
	}

	function getConfText() {
		return file_get_contents( $this->confLocation );
	}

	function testConf() {
		$text = $this->getConfText();
		$confEditor = new ConfEditor( $text );
		try {
			$confEditor->parse();
		} catch ( ConfEditorParseError $e ) {
			$this->log( $e->getMessage() . "\n" . $e->highlight( $text ) );
			return false;
		}
		if ( !isset( $this->lbfConf['readOnlyBySection'] ) ) {
			$this->log( "The \$wgLBFactoryConf['readOnlyBySection'] key must exist." );
			return false;
		}
		return true;
	}

	function commitConf() {
		$text = $this->getConfText();
		$confEditor = new ConfEditor( $text );
		$text = $confEditor->edit( $this->confOps );
		// Create a backup
		rename( $this->confLocation, "{$this->confLocation}~" );
		// Write the file
		file_put_contents( $this->confLocation, $text );
		// Sync
		passthru( "sync-file " . basename( $this->confLocation ), $ret );
		if ( $ret ) {
			$this->log( "Unable to sync the configuration file.\n" );
			exit( 1 );
		}

		$this->confOps = array();
	}

	function getMaster( $section ) {
		if ( !isset( $this->lbfConf['sectionLoads'][$section] ) ) {
			throw new MWException( "Invalid section: $section, did you mean DEFAULT?\n" );
		}

		$loads = $this->lbfConf['sectionLoads'][$section];
		reset( $loads );
		return key( $loads );
	}

	function getSections( $master ) {
		// Find all sections with this host as the master
		$sections = array();
		foreach ( $this->lbfConf['sectionLoads'] as $section => $loads ) {
			reset( $loads );
			$master = key( $loads );
			if ( $master == $host ) {
				$sections[] = $section;
			}
		}
		return $sections;
	}

	function getSlaves( $master ) {
		$slaves = array();
		$sections = $this->getSections( $master );
		if ( !$sections ) {
			return false;
		}

		foreach ( $sections as $section ) {
			$loads = $this->lbfConf['sectionLoads'][$section];
			foreach ( $loads as $host => $load ) {
				if ( $host !== $master ) {
					$slaves[] = $host;
				}
			}
		}
		return array_unique( $slaves );
	}

	function getSectionWikis( $section ) {
		if ( $section == 'DEFAULT' ) {
			$sectionedWikis = array();
			foreach ( $this->lbfConf['sectionsByDB'] as $wiki => $section2 ) {
				$sectionedWikis[] = $wiki;
			}
			return array_diff( $wgLocalDatabases, $sectionedWikis );
		}

		$wikis = array();
		foreach ( $this->lbfConf['sectionsByDB'] as $wiki => $section2 ) {
			if ( $section == $section2 ) {
				$wikis[] = $wiki;
			}
		}
		return $wikis;
	}
		
	function getIP( $host ) {
		if ( isset( $this->lbfConf['hostsByName'][$host] ) ) {
			return $this->lbfConf['hostsByName'][$host];
		} else {
			return $host;
		}
	}

	function getRootPassword() {
		return $this->rootPass;
	}

	function doQueryLogErrors( $conn, $hostName, $sql ) {
		try {
			$conn->query( $sql );
		} catch ( DBQueryError $e ) {
			$this->log( "Error running query on $hostName: $sql" );
		}
	}

	function sanityCheck( $oldMaster, $newMaster ) {
		// Check that the new master is a slave
		$slaves = $this->getSlaves( $oldMaster );
		if ( $slaves === false ) {
			$this->log( "The old master $oldMaster is not configured as a master." );
			$this->log( "Please configure it as a master so I know what its slaves are." );
			return false;
		}
		if ( !in_array( $newMaster, $slaves ) ) {
			$this->log( "The new master '$newMaster' must be configured as a slave of the old master '$oldMaster'" );
			return false;
		}

		// Check that the new master has binlog enabled
		$newMasterDB = $this->getConnection( $newMaster );
		if ( !$newMasterDB ) {
			$this->log( "Cannot connect to the new master '$newMaster'" );
			return false;
		}
		$res = $newMasterDB->query( "SHOW VARIABLES LIKE 'log_bin'" );
		$row = $res->fetchObject();
		if ( $row->Value !== 'ON' ) {
			$this->log( 'log_bin is not enabled on the new master. ' . '
				You need to restart the server with it enabled.' );
			return false;
		}

		// Check that the conf file is editable
		if ( !$this->testConf() ) {
			return false;
		}

		return true;
	}

	function prepareOldMaster( $hostName, $conn ) {
		// Set the old master to read-only
		$conn->query( 'SET GLOBAL read_only=1' );
		
		// Kill long-running queries
		$res = $conn->query( 'SHOW PROCESSLIST' );
		foreach ( $res as $row ) {
			if ( ( $row->User == 'wikiadmin' || $row->User == 'wikiuser' )
				&& $row->Time > 10 && preg_match( '/^\d+$/', $row->Id ) )
			{
				$killQueries[] = 'KILL ' . $row->Id;
			}
		}
		foreach ( $killQueries as $query ) {
			try {
				$conn->query( $query );
			} catch ( DBQueryError $e ) {}
		}

		// Flush tables
		// This ensures that pending transactions are committed
		$conn->query( 'FLUSH TABLES' );

		// Sanity check
		$res = $conn->query( 'SELECT @@read_only as read_only' );
		$row = $res->fetchObject();
		if ( $row->read_only != 1 ) {
			$this->log( 'Failed sanity check: old master is still r/w after r/o request' );
		}

		// Get old master pos
		$oldMasterPos = $conn->getMasterPos();

		// Wait for that position on all slaves
		foreach ( $this->getSlaves( $hostName ) as $slave ) {
			$slaveDB = $this->getConnection( $slave );
			if ( !$slaveDB ) {
				$this->log( "Error connecting to slave DB: $slave. Continuing anyway." );
				continue;
			}
			$slaveDB->waitFor( $oldMasterPos );
		}
	}

	function log( $msg ) {
		echo "$msg\n";
	}
}


