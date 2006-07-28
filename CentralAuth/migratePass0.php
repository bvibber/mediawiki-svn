<?php

// --> disable account creations, password changes
// pass 0:
// * generate 'localuser' entries for each user on each wiki
// * generate 'globaluser' entries for each username
// --> enable 

require_once 'commandLine.inc';


/**
 * Copy user data for this wiki into the localuser table
 */
function migratePassZero() {
	global $wgDBname;
	$dbBackground = wfGetDB( DB_SLAVE ); // fixme for large dbs
	$start = microtime( true );
	$result = $dbBackground->select(
		'user',
		array(
			'user_id',
			'user_name',
			'user_password',
			'user_newpassword',
			'user_email',
			'user_email_authenticated',
		),
		'',
		__METHOD__ );
	$migrated = 0;
	while( $row = $dbBackground->fetchObject( $result ) ) {
		$count = getEditCount( $row->user_id );
		//$count = 0;
		CentralAuthUser::storeLocalData( $wgDBname, $row, $count );
		if( ++$migrated % 100 == 0 ) {
			$delta = microtime( true ) - $start;
			$rate = ($delta == 0.0) ? 0.0 : $migrated / $delta;
			printf( "%d done in %0.1f secs (%0.3f accounts/sec).\n",
				$migrated, $delta, $rate );
		}
	}
	$dbBackground->freeResult( $result );
	
	$delta = microtime( true ) - $start;
	$rate = ($delta == 0.0) ? 0.0 : $migrated / $delta;
	printf( "%d done in %0.1f secs (%0.3f accounts/sec).\n",
		$migrated, $delta, $rate );
}

function getEditCount( $userId ) {
	return countEdits( $userId, 'revision', 'rev_user' );
}

function countEdits( $userId, $table, $field ) {
	$dbr = wfGetDB( DB_SLAVE );
	$count = $dbr->selectField( $table, 'COUNT(*)',
		array( $field => $userId ),
		__METHOD__ );
	return intval( $count );
}

if( $wgCentralAuthState != 'premigrate' ) {
	if( $wgCentralAuthState == 'testing' ) {
		echo "WARNING: \$wgCentralAuthState is set to 'testing', generated data may be corrupt.\n";
	} else {
		wfDie( "\$wgCentralAuthState is '$wgCentralAuthState', please set to 'premigrate' to prevent conflicts.\n" );
	}
}

echo "CentralAuth migration pass 0:\n";
echo "$wgDBname preparing migration data...\n";
migratePassZero();
echo "done.\n";

?>