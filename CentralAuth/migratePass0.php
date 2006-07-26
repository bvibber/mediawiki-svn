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
	while( $row = $dbBackground->fetchObject( $result ) ) {
		$count = getEditCount( $row->user_id );
		CentralAuthUser::storeLocalData( $wgDBname, $row, $count );
	}
	$dbBackground->freeResult( $result );
}

function getEditCount( $userId ) {
	return countEdits( $userId, 'revision', 'rev_user' );
}

function countEdits( $userId, $table, $field ) {
	$dbr = wfGetDB( DB_SLAVE );
	$count = $dbr->selectField( $table, 'COUNT(*)',
		array(),
		__METHOD__,
		array( 'GROUP BY' => $field ) );
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