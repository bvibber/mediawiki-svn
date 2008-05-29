<?php

$IP = getenv( 'MW_INSTALL_PATH' );
if ( !$IP ) {
	exit;
}
require_once( $IP . '/maintenance/commandLine.inc' );

$dbr = wfGetDB( DB_SLAVE );
$fname = 'voterList.quick.php';
$maxUser = $dbr->selectField( 'user', 'MAX(user_id)', false );
$server = str_replace( 'http://', '', $wgServer );
$listFile = fopen( "voter-list", "a" );

for ( $user = 0; $user <= $maxUser; $user++ ) {
	$oldEdits = $dbr->selectField( 
		'revision', 
		'COUNT(*)',
		array( 
			'rev_user' => $user,
			"rev_timestamp < '200803010000'"
		), 
		$fname
	);
	$newEdits = $dbr->selectField( 
		'revision', 
		'COUNT(*)',
		array( 
			'rev_user' => $user,
			"rev_timestamp BETWEEN '200801010000' AND '200805285959'"
		), 
		$fname
	);
	if ( $oldEdits >= 600 && $newEdits >= 50 ) {
		$userRow = $dbr->selectField(
			'user',
			array( 'user_name', 'user_email' ),
			array( 'user_id' => $user ),
			$fname
		);
		if ( $userRow === false ) {
			fwrite( STDERR, "User row missing for user_id $user!\n" );
			continue;
		}

		fwrite( $listFile, "$wgDBname\t{$userRow->user_name}\t{$userRow->user_email}\n" );
	}
}
fclose( $listFile );

?>
